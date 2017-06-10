<?php

namespace analysiswebapp\webapp\models;

class Emails
{
    protected $emailsID = null;
    protected $outermessageid;
    protected $outerTo;
    protected $outerFrom;
    protected $outersubject;
    protected $outerdate;
    protected $innermessageid;
    protected $innerdate;
    protected $innerFrom;
    protected $innerTo;
    protected $innersubject;
    protected $receivedspf;
    protected $contentlanguage;
    protected $listunsubscribe;
    protected $authenticationresults;
    protected $mimeversion;
    protected $threadtopic;
    protected $dkimsignature;
    protected $replyto;
    protected $received;
    protected $threadindex;
    protected $contenttype;
    protected $dispositionnotificationto;
    protected $importance;
    protected $returnreceiptto;
    protected $xmstnefcorrelator;
    protected $xautoresponsesuppress;
    protected $xoriginatororg;
    protected $xmailersid;
    protected $xmshasattach;
    protected $xmsexchangeorganizationscl;
    protected $xmailersentby;
    protected $xmsexchangeorganizationauthas;
    protected $xmsexchangeorganizationauthsource;
    protected $xforefrontantispamreport;
    protected $xmailerlid;
    protected $xpriority;
    protected $xantiabuse;
    protected $xsourcedir;
    protected $xphpscript;
    protected $xsource;
    protected $xsourceargs;

    function __construct($outermessageid, $outerTo, $outerFrom, $outersubject, $outerdate, $innermessageid, $innerdate, $innerFrom, $innerTo, $innersubject, $receivedspf, $contentlanguage,
                          $listunsubscribe, $authenticationresults, $mimeversion, $threadtopic, $dkimsignature, $replyto, $received,$threadindex, $contenttype, $dispositionnotificationto,
                          $importance, $returnreceiptto,$xmstnefcorrelator, $xautoresponsesuppress, $xoriginatororg, $xmailersid, $xmshasattach,$xmsexchangeorganizationscl, $xmailersentby,
                          $xmsexchangeorganizationauthas, $xmsexchangeorganizationauthsource, $xforefrontantispamreport, $xmailerlid, $xpriority, $xantiabuse, $xsourcedir, $xphpscript, $xsource, $xsourceargs)
    {
      $this->outermessageid = $outermessageid;
      $this->outerTo = $outerTo;
      $this->outerFrom = $outerFrom;
      $this->outersubject = $outersubject;
      $this->outerdate = $outerdate;
      $this->innermessageid = $innermessageid;
      $this->innerdate = $innerdate;
      $this->innerFrom = $innerFrom;
      $this->innerTo = $innerTo;
      $this->innersubject = $innersubject;
      $this->receivedspf = $receivedspf;
      $this->contentlanguage = $contentlanguage;
      $this->listunsubscribe = $listunsubscribe;
      $this->authenticationresults = $authenticationresults;
      $this->mimeversion = $mimeversion;
      $this->threadtopic = $threadtopic;
      $this->dkimsignature = $dkimsignature;
      $this->replyto = $replyto;
      $this->received = $received;
      $this->threadindex = $threadindex;
      $this->contenttype = $contenttype;
      $this->dispositionnotificationto = $dispositionnotificationto;
      $this->importance = $importance;
      $this->returnreceiptto = $returnreceiptto;
      $this->xmstnefcorrelator = $xmstnefcorrelator;
      $this->xautoresponsesuppress = $xautoresponsesuppress;
      $this->xoriginatororg = $xoriginatororg;
      $this->xmailersid = $xmailersid;
      $this->xmshasattach = $xmshasattach;
      $this->xmsexchangeorganizationscl = $xmsexchangeorganizationscl;
      $this->xmailersentby = $xmailersentby;
      $this->xmsexchangeorganizationauthas = $xmsexchangeorganizationauthas;
      $this->xmsexchangeorganizationauthsource = $xmsexchangeorganizationauthsource;
      $this->xforefrontantispamreport = $xforefrontantispamreport;
      $this->xmailerlid = $xmailerlid;
      $this->xpriority = $xpriority;
      $this->xantiabuse = $xantiabuse;
      $this->xsourcedir = $xsourcedir;
      $this->xphpscript = $xphpscript;
      $this->xsource = $xsource;
      $this->xsourceargs = $xsourceargs;
    }

    public function getEmailsId() {
        return $this->emailsID;
    }

    public function setEmailsId($emailsID) {
        $this->emailsID = $emailsID;
        return $this;
    }

    public function getoutermessageid() {
        return $this->outermessageid;
    }

    public function setoutermessageid($outermessageid) {
        $this->outermessageid = $outermessageid;
        return $this;
    }

    public function setouterto($outerTo) {
        $this->outerTo = $outerTo;
        return $this;
    }

    public function getouterto() {
        return $this->outerTo;
    }

    public function getouterfrom() {
        return $this->outerFrom;
    }

    public function setouterfrom($outerFrom) {
        $this->outerFrom = $outerFrom;
        return $this;
    }

    public function getoutersubject(){
        return $this->outersubject;
    }

    public function setoutersubject($outersubject){
        $this->outersubject = $outersubject;
    }
    public function getouterdate() {
        return $this->outerdate;
    }

    public function setouterdate($outerdate) {
        $this->outerdate = $outerdate;
        return $this;

    }
    public function getinnermessageid() {
        return $this->innermessageid;
    }

    public function setinnermessageid($innermessageid) {
        $this->innermessageid = $innermessageid;
        return $this;
    }

    public function setinnerto($innerTo) {
        $this->innerTo = $innerTo;
        return $this;
    }

    public function getinnerto() {
        return $this->innerTo;
    }

    public function getinnerfrom() {
        return $this->innerFrom;
    }

    public function setinnerfrom($innerFrom) {
        $this->innerFrom = $innerFrom;
        return $this;
    }

    public function getinnersubject(){
        return $this->innersubject;
    }

    public function setinnersubject($innersubject){
        $this->innersubject = $innersubject;
    }

    public function getinnerdate() {
        return $this->innerdate;
    }

    public function setinnerdate($innerdate) {
        $this->innerdate = $innerdate;
        return $this;
    }

    public function getreceivedspf() {
        return $this->receivedspf;
    }

    public function setreceivedspf($receivedspf) {
        $this->receivedspf = $receivedspf;
        return $this;
    }

    public function getcontentlanguage() {
        return $this->contentlanguage;
    }

    public function setcontentlanguage($contentlanguage) {
        $this->contentlanguage = $contentlanguage;
        return $this;
    }

    public function getlistunsubscribe() {
        return $this->listunsubscribe;
    }

    public function setlistunsubscribe($listunsubscribe) {
        $this->listunsubscribe = $listunsubscribe;
        return $this;
    }

    public function getauthenticationresults() {
        return $this->authenticationresults;
    }

    public function setauthenticationresults($authenticationresults) {
        $this->authenticationresults = $authenticationresults;
        return $this;
    }

    public function getmimeversion() {
        return $this->mimeversion;
    }

    public function setmimeversion($mimeversion) {
        $this->mimeversion = $mimeversion;
        return $this;
    }

    public function getthreadtopic() {
        return $this->threadtopic;
    }

    public function setthreadtopic($threadtopic) {
        $this->threadtopic = $threadtopic;
        return $this;
    }

    public function getdkimsignature() {
        return $this->dkimsignature;
    }

    public function setdkimsignature($dkimsignature) {
        $this->dkimsignature = $dkimsignature;
        return $this;
    }

    public function getreplyto() {
        return $this->replyto;
    }

    public function setreplyto($replyto) {
        $this->replyto = $replyto;
        return $this;
    }

    public function getreceived() {
        return $this->received;
    }

    public function setreceived($received) {
        $this->received = $received;
        return $this;
    }

    public function getthreadindex() {
        return $this->threadindex;
    }

    public function setthreadindex($threadindex) {
        $this->threadindex = $threadindex;
        return $this;
    }

    public function getcontenttype() {
        return $this->contenttype;
    }

    public function setcontenttype($contenttype) {
        $this->contenttype = $contenttype;
        return $this;
    }

    public function getdispositionnotificationto() {
        return $this->dispositionnotificationto;
    }

    public function setdispositionnotificationto($dispositionnotificationto) {
        $this->dispositionnotificationto = $dispositionnotificationto;
        return $this;
    }

    public function getimportance() {
        return $this->importance;
    }

    public function setimportance($importance) {
        $this->importance = $importance;
        return $this;
    }

    public function getreturnreceiptto() {
        return $this->returnreceiptto;
    }

    public function setreturnreceiptto($returnreceiptto) {
        $this->returnreceiptto = $returnreceiptto;
        return $this;
    }

    public function getxmstnefcorrelator() {
        return $this->xmstnefcorrelator;
    }

    public function setxmstnefcorrelator($xmstnefcorrelator) {
        $this->xmstnefcorrelator = $xmstnefcorrelator;
        return $this;
    }

    public function getxautoresponsesuppress() {
        return $this->xautoresponsesuppress;
    }

    public function setxautoresponsesuppress($xautoresponsesuppress) {
        $this->xautoresponsesuppress = $xautoresponsesuppress;
        return $this;
    }

    public function getxoriginatororg() {
        return $this->xoriginatororg;
    }

    public function setxoriginatororg($xoriginatororg) {
        $this->xoriginatororg = $xoriginatororg;
        return $this;
    }

    public function getxmailersid() {
        return $this->xmailersid;
    }

    public function setxmailersid($xmailersid) {
        $this->xmailersid = $xmailersid;
        return $this;
    }

    public function getxmshasattach() {
        return $this->xmshasattach;
    }

    public function setxmshasattach($xmshasattach) {
        $this->xmshasattach = $xmshasattach;
        return $this;
    }

    public function getxmsexchangeorganizationscl() {
        return $this->xmsexchangeorganizationscl;
    }

    public function setxmsexchangeorganizationscl($xmsexchangeorganizationscl) {
        $this->xmsexchangeorganizationscl = $xmsexchangeorganizationscl;
        return $this;
    }

    public function getxmailersentby() {
        return $this->xmailersentby;
    }

    public function setxmailersentby($xmailersentby) {
        $this->xmailersentby = $xmailersentby;
        return $this;
    }

    public function getxmsexchangeorganizationauthas() {
        return $this->xmsexchangeorganizationauthas;
    }

    public function setxmsexchangeorganizationauthas($xmsexchangeorganizationauthas) {
        $this->xmsexchangeorganizationauthas = $xmsexchangeorganizationauthas;
        return $this;
    }

    public function getxmsexchangeorganizationauthsource() {
        return $this->xmsexchangeorganizationauthsource;
    }

    public function setxmsexchangeorganizationauthsource($xmsexchangeorganizationauthsource) {
        $this->xmsexchangeorganizationauthsource = $xmsexchangeorganizationauthsource;
        return $this;
    }

    public function getxforefrontantispamreport() {
        return $this->xforefrontantispamreport;
    }

    public function setxforefrontantispamreport($xforefrontantispamreport) {
        $this->xforefrontantispamreport = $xforefrontantispamreport;
        return $this;
    }

    public function getxmailerlid() {
        return $this->xmailerlid;
    }

    public function setxmailerlid($xmailerlid) {
        $this->xmailerlid = $xmailerlid;
        return $this;
    }

    public function getxpriority() {
        return $this->xpriority;
    }

    public function setxpriority($xpriority) {
        $this->xpriority = $xpriority;
        return $this;
    }

    public function getxantiabuse() {
        return $this->xantiabuse;
    }

    public function setxantiabuse($xantiabuse) {
        $this->xantiabuse = $xantiabuse;
        return $this;
    }

    public function getxsourcedir() {
        return $this->xsourcedir;
    }

    public function setxsourcedir($xsourcedir) {
        $this->xsourcedir = $xsourcedir;
        return $this;
    }

    public function getxphpscript() {
        return $this->xphpscript;
    }

    public function setxphpscript($xphpscript) {
        $this->xphpscript = $xphpscript;
        return $this;
    }

    public function getxsource() {
        return $this->xsource;
    }

    public function setxsource($xsource) {
        $this->xsource = $xsource;
        return $this;
    }

    public function getxsourceargs() {
        return $this->xsourceargs;
    }

    public function setxsourceargs($xsourceargs) {
        $this->xsourceargs = $xsourceargs;
        return $this;
    }
	 
	  function getDbObject(){
		 $fields = ['outermessageid', 'outerTo', 'outerFrom', 'outersubject', 'outerdate', 'innermessageid', 'innerdate', 'innerFrom', 'innerTo', 'innersubject', 'receivedspf', 'contentlanguage',
'listunsubscribe', 'authenticationresults', 'mimeversion', 'threadtopic', 'dkimsignature', 'replyto', 'received','threadindex', 'contenttype', 'dispositionnotificationto',
'importance', 'returnreceiptto','xmstnefcorrelator', 'xautoresponsesuppress', 'xoriginatororg', 'xmailersid', 'xmshasattach','xmsexchangeorganizationscl', 'xmailersentby',
'xmsexchangeorganizationauthas', 'xmsexchangeorganizationauthsource', 'xforefrontantispamreport', 'xmailerlid', 'xpriority', 'xantiabuse', 'xsourcedir', 'xphpscript', 'xsource', 'xsourceargs'];
		 $dbObject  = [];
		 foreach($fields as $field){
			 if(!is_null($this->{$field})){
				 $dbObject[$field] = $this->{$field};
			 }
		 }
		 
		 return $dbObject;
	 }
}
