<?php

namespace analysiswebapp\webapp\models;

class Emails {

	protected $emailsID = null;
	protected $emailData = [];

	function __construct($emailData) {
		$this->emailData = $emailData;
		if (isset($emailData->_id)) {
			$this->emailData->emailsID = $emailData->_id;
			$this->setEmailsId($emailData->_id);
		}
	}

	public function getEmailsId() {
		return $this->emailsID;
	}

	public function setEmailsId($emailsID) {
		$this->emailsID = $emailsID;
		return $this;
	}

	function getDbObject() {

		return $this->emailData;

		$fields = ['outermessageid', 'outerTo', 'outerFrom', 'outersubject', 'outerdate', 'innermessageid', 'body', 'received_email','innerdate','body','metadata','received_domain','received_ip','received_foremail','metadata', 'innerdate', 'innerFrom', 'innerTo', 'innersubject'];
		$dbObject = [];
		foreach ($fields as $field) {
			if (!is_null($this->{$field})) {
				$dbObject[$field] = $this->{$field};
			}
		}

		return $dbObject;
	}

}
