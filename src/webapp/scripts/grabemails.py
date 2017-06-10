#!/usr/bin/python2.7
# -*- coding: utf-8 -*-
import getpass
import json
import sys
import json
import email
import argparse
import re
import uuid
import datetime
import calendar
import dateutil.tz
import dateutil.parser
import base64
import hashlib
import quopri
import collections
import os
import StringIO
import imaplib
import mimetypes
import mimify
import datetime
from emlparser import decode_email_s
from pymongo import MongoClient
from bson.json_util import dumps
from email.header import Header, decode_header, make_header

moveEmailsOnComplete=False
# Log in parameters
EMAIL_ACCOUNT = "inbox@automaticanalysis.eu"
EMAIL_FOLDER = "Inbox.NOTprocessed"
EMAIL_PASSWORD = getpass.getpass()
EMAIL_PASSWORD = "69GoIkIz"
M = imaplib.IMAP4_SSL('e36.ehosts.com')
mongoConnectn = MongoClient("mongodb://localhost:27017")

# connect to the students database and the ctec121 collection
mongoDb = mongoConnectn.grabemails.emails

#mongoDb.insert({'name':'hello'});
invalid_chars_in_filename='<>:"/\\|?*\%\''+reduce(lambda x,y:x+chr(y), range(32), '')
invalid_windows_name='CON PRN AUX NUL COM1 COM2 COM3 COM4 COM5 COM6 COM7 COM8 COM9 LPT1 LPT2 LPT3 LPT4 LPT5 LPT6 LPT7 LPT8 LPT9'.split()

atom_rfc2822=r"[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+"
atom_posfix_restricted=r"[a-zA-Z0-9_#\$&'*+/=?\^`{}~|\-]+" # without '!' and '%'
atom=atom_rfc2822
dot_atom=atom  +  r"(?:\."  +  atom  +  ")*"
quoted=r'"(?:\\[^\r\n]|[^\\"])*"'
local="(?:"  +  dot_atom  +  "|"  +  quoted  +  ")"
domain_lit=r"\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\]"
domain="(?:"  +  dot_atom  +  "|"  +  domain_lit  +  ")"
addr_spec=local  +  "\@"  +  domain

email_address_re=re.compile('^'+addr_spec+'$')

class Attachment:
    def __init__(self, part, filename=None, type=None, payload=None, charset=None, content_id=None, description=None, disposition=None, sanitized_filename=None, is_body=None):
        self.part=part          # original python part
        self.filename=filename  # filename in unicode (if any)
        self.type=type          # the mime-type
        self.payload=payload    # the MIME decoded content
        self.charset=charset    # the charset (if any)
        self.description=description    # if any
        self.disposition=disposition    # 'inline', 'attachment' or None
        self.sanitized_filename=sanitized_filename # cleanup your filename here (TODO)
        self.is_body=is_body        # usually in (None, 'text/plain' or 'text/html')
        self.content_id=content_id  # if any
        if self.content_id:
            # strip '<>' to ease searche and replace in "root" content (TODO)
            if self.content_id.startswith('<') and self.content_id.endswith('>'):
                self.content_id=self.content_id[1:-1]

def getmailheader(header_text, default="ascii"):
    """Decode header_text if needed"""
    try:
        headers = email.Header.decode_header(header_text)
    except email.Errors.HeaderParseError:
        # This already append in email.base64mime.decode()
        # instead return a sanitized ascii string
        # this faile '=?UTF-8?B?15HXmdeh15jXqNeVINeY15DXpteUINeTJ9eV16jXlSDXkdeg15XXldeUINem15PXpywg15TXptei16bXldei15nXnSDXqdecINek15zXmdeZ?==?UTF-8?B?157XldeR15nXnCwg157Xldek16Ig157Xl9eV15wg15HXodeV15bXnyDXk9ec15DXnCDXldeh15gg157Xl9eR16rXldeqINep15wg15HXmdeQ?==?UTF-8?B?15zXmNeZ?='
        return header_text.encode('ascii', 'replace').decode('ascii')
    else:
        for i, (text, charset) in enumerate(headers):
            try:
                headers[i]=unicode(text, charset or default, errors='replace')
            except LookupError:
                # if the charset is unknown, force default
                headers[i]=unicode(text, default, errors='replace')
        return u"".join(headers)

def getmailaddresses(msg, name):
    """retrieve addresses from header, 'name' supposed to be from, to,  ..."""
    addrs=email.utils.getaddresses(msg.get_all(name, []))
    for i, (name, addr) in enumerate(addrs):
        if not name and addr:
            # only one string! Is it the address or is it the name ?
            # use the same for both and see later
            name=addr
        try:
            # address must be latin1 only
            addr=addr.encode('latin1')
        except UnicodeError:
            addr=''
        else:
            # address must match address regex
            if not email_address_re.match(addr):
                addr=''
        addrs[i]=(getmailheader(name), addr)
    return addrs

def get_filename(part):
    """Many mail user agents send attachments with the filename in
    the 'name' parameter of the 'content-type' header instead
    of in the 'filename' parameter of the 'content-disposition' header.
    """
    filename=part.get_param('filename', None, 'content-disposition')
    if not filename:
        filename=part.get_param('name', None) # default is 'content-type'

    if filename:
        # RFC 2231 must be used to encode parameters inside MIME header
        filename=email.Utils.collapse_rfc2231_value(filename).strip()

    if filename and isinstance(filename, str):
        # But a lot of MUA erroneously use RFC 2047 instead of RFC 2231
        # in fact anybody miss use RFC2047 here !!!
        filename=getmailheader(filename)

    return filename

def _search_message_bodies(bodies, part):
    """recursive search of the multiple version of the 'message' inside
    the the message structure of the email, used by search_message_bodies()"""

    type=part.get_content_type()
    if type.startswith('multipart/'):
        # explore only True 'multipart/*'
        # because 'messages/rfc822' are also python 'multipart'
        if type=='multipart/related':
            # the first part or the one pointed by start
            start=part.get_param('start', None)
            related_type=part.get_param('type', None)
            for i, subpart in enumerate(part.get_payload()):
                if (not start and i==0) or (start and start==subpart.get('Content-Id')):
                    _search_message_bodies(bodies, subpart)
                    return
        elif type=='multipart/alternative':
            # all parts are candidates and latest is best
            for subpart in part.get_payload():
                _search_message_bodies(bodies, subpart)
        elif type in ('multipart/report',  'multipart/signed'):
            # only the first part is candidate
            try:
                subpart=part.get_payload()[0]
            except IndexError:
                return
            else:
                _search_message_bodies(bodies, subpart)
                return

        elif type=='multipart/signed':
            # cannot handle this
            return

        else:
            # unknown types must be handled as 'multipart/mixed'
            # This is the peace of code could probably be improved, I use a heuristic :
            # - if not already found, use first valid non 'attachment' parts found
            for subpart in part.get_payload():
                tmp_bodies=dict()
                _search_message_bodies(tmp_bodies, subpart)
                for k, v in tmp_bodies.iteritems():
                    if not subpart.get_param('attachment', None, 'content-disposition')=='':
                        # if not an attachment, initiate value if not already found
                        bodies.setdefault(k, v)
            return
    else:
        bodies[part.get_content_type().lower()]=part
        return

    return

def search_message_bodies(mail):
    """search message content into a mail"""
    bodies=dict()
    _search_message_bodies(bodies, mail)
    return bodies

def get_mail_contents(msg):
    """split an email in a list of attachments"""

    attachments=[]

    # retrieve messages of the email
    bodies=search_message_bodies(msg)
    # reverse bodies dict
    parts=dict((v,k) for k, v in bodies.iteritems())

    # organize the stack to handle deep first search
    stack=[ msg, ]
    while stack:
        part=stack.pop(0)
        type=part.get_content_type()
        if type.startswith('message/'):
            # ('message/delivery-status', 'message/rfc822', 'message/disposition-notification'):
            # I don't want to explore the tree deeper her and just save source using msg.as_string()
            # but I don't use msg.as_string() because I want to use mangle_from_=False
            from email.Generator import Generator
            fp = StringIO.StringIO()
            g = Generator(fp, mangle_from_=False)
            g.flatten(part, unixfrom=False)
            payload=fp.getvalue()
            filename='.eml'
            attachments.append(Attachment(part, filename=filename, type=type, payload=payload, charset=part.get_param('charset'), description=part.get('Content-Description'), disposition=part.get('Content-Disposition')))
        elif part.is_multipart():
            # insert new parts at the beginning of the stack (deep first search)
            stack[:0]=part.get_payload()
        else:
            payload=part.get_payload(decode=True)
            charset=part.get_param('charset')
            filename=get_filename(part)

            disposition=None
            if part.get_param('inline', None, 'content-disposition')=='':
                disposition='inline'
            elif part.get_param('attachment', None, 'content-disposition')=='':
                disposition='attachment'

            attachments.append(Attachment(part, filename=filename, type=type, payload=payload, charset=charset, content_id=part.get('Content-Id'), description=part.get('Content-Description'), disposition=part.get('Content-Disposition'), is_body=parts.get(part)))

    return attachments

def decode_text(payload, charset, default_charset):
    if charset:
        try:
            return payload.decode(charset), charset
        except UnicodeError:
            pass

    if default_charset and default_charset!='auto':
        try:
            return payload.decode(default_charset), default_charset
        except UnicodeError:
            pass

    for chset in [ 'ascii', 'utf-8', 'utf-16', 'windows-1252', 'cp850', 'latin1', 'latin5', 'cp949', 'cp1252' ]:
        try:
            return payload.decode(chset), chset
        except UnicodeError:
            pass

    return payload, None

def process_mailbox(M):
    """
    Do something with emails messages in the folder.
    For the sake of this example, print some headers.
    """

    rv, data = M.search(None, "ALL")
    if rv != 'OK':
        print ("No messages found!")
        return

    msg_ids = ','.join(data[0].split(' '))
    emaildata = list()
    for num in data[0].split():
        rv, data = M.fetch(num, '(RFC822)')
        if rv != 'OK':
            print ("ERROR getting message"), num
            return

        default_charset = 'latin1'
        # Get data from OJ! email
        outermsg = data[0][1]
        outmsg = ''.join(outermsg);
        outermsg = decode_email_s(outmsg)
        outermessageid = str(outermsg["header"]["header"]["message-id"][0]).translate(None,",<,>")
        outerdate = str(outermsg["header"]["header"]["date"][0])
        outerFromFix = str(outermsg['header']['header']['from'][0]).replace('"','').translate(None, ",<,>")
        outerFrom = mimify.mime_decode_header(outerFromFix).decode("latin1")
        outemailTo = str(outermsg["header"]["header"]["to"][0]).replace("sintef@mailrisk.no", '')
        outfixTo = str(outemailTo).replace('"','').translate(None, "<,>")
        outerTo = ' '.join(outfixTo.split())
        outersubjectFix = str(outermsg["header"]["header"]["subject"][0])
        outersubject = mimify.mime_decode_header(outersubjectFix).decode("latin1")

        # Get data from OJ! email attachment
        inneremail = email.message_from_string(data[0][1])
        attachments = get_mail_contents(inneremail)
        #print attachments[5]
        for attach in attachments:
            # dont forget to be careful to sanitize 'filename' and be carefull
            # for filename collision, to before to save :
            #print '\tfilename=%r is_body=%s type=%s charset=%s desc=%s size=%d' % (attach.filename, attach.is_body, attach.type, attach.charset, attach.description, 0 if attach.payload==None else len(attach.payload))
            if attach.type == ('message/rfc822'):
                innmsg = attach.payload

                position = re.search("Received: from",innmsg)
                position2 = re.search("From:",innmsg)
                if position:
                    position2 = re.search("From:",innmsg)
                    index = position.start(0)
                    innmsg = innmsg[index:]
                    innermsg = decode_email_s(innmsg)
                    metadata = ' '.join(map(str, innermsg["header"]["header"]["received"]))

                else:
                    if position2:
                        index = position2.start(0)
                        innmsg = innmsg[index:]
                        metadata = None
                        innermsg = decode_email_s(innmsg)

                bodyfix = str(innermsg["body"]).translate(None, ",<,>,[,],{,},'")
                body = bodyfix.replace("\\r","").replace("\\n"," ")

                emaildatas = {'outermessageid' : outermessageid,
                'outerdate' : outerdate,
                'outerFrom' : outerFrom,
                'outerTo' : outerTo,
                'outersubject' : outersubject,
                'metadata' : metadata,
                'body' : body,
                }

                check_list = str(innermsg["header"]).encode("utf-8")
                header_list = innermsg["header"]["header"]

                data_mapinner = {
                'received_domain' : "received_domain",
                'received_ip' : "received_ip",
                'received_foremail' : "received_foremail",
                'received_email' : "received_email",
                }

                for each_elem in check_list:
                    if each_elem not in ["received_domain", "received_ip", "received_foremail","received_email"]:
                        data_mapinner [each_elem.replace("-","")] = each_elem

                for each_elem in data_mapinner:
                    if innermsg["header"].has_key(data_mapinner[each_elem]):
                        emaildatas[each_elem] = ', '.join(innermsg["header"][data_mapinner[each_elem]])
                emaildata.append(emaildatas)

                data_map = {
                'innermessageid' : "message-id",
                'innerdate' : "date",
                'innerFrom' : "from",
                'innerTo' : "to",
                'innersubject' : "subject",
                }

                for each_elem in header_list:
                    if each_elem not in ["subject","to","from","date","message-id","received"]:
                        data_map [each_elem.replace("-","")] = each_elem

                for each_elem in data_map:
                    if innermsg["header"]["header"].has_key(data_map[each_elem]):
                        emaildatas[each_elem] = ', '.join([ unicode(t[0], t[1] or default_charset) for t in decode_header(str(innermsg["header"]["header"][data_map[each_elem]][0]).replace('"','').translate(None, ",<,>,[,]")) ])
                emaildata.append(emaildatas)

                mongoData = emaildatas
                mongoDb.insert(mongoData)

    if emaildata:
        print dumps(emaildata)
    # Move processed email to Inbox.Processed
    if msg_ids:
        if moveEmailsOnComplete:
            result, copy = M.copy(msg_ids, 'Inbox.Processed')
            if result == 'OK':
                M.store(msg_ids, '+FLAGS', '\\Deleted')
                M.expunge()
        pass
    else:
        print ("No emails available")

try:
    rv, data = M.login(EMAIL_ACCOUNT, EMAIL_PASSWORD)
except imaplib.IMAP4.error:
    print ("LOGIN FAILED!!!")
    sys.exit(1)

rv, mailboxes = M.list()
if rv == 'OK':
    print ("Mailboxes:")

rv, data = M.select(EMAIL_FOLDER)
if rv == 'OK':
    print ("Processing mailbox...\n")
    process_mailbox(M)
    M.close()
else:
    print ("ERROR: Unable to open mailbox "), rv

M.logout()
