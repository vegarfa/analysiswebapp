<?php

namespace analysiswebapp\webapp\validation;

use analysiswebapp\webapp\models\Emails;

class EmailsValidation {

    private $validationErrors = [];

    public function __construct($messageID, $recipient, $sender, $subject)
    {
        return $this->validate($messageID, $recipient, $sender, $subject);
    }

    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($messageID, $recipient, $sender, $subject)
    {

        if(empty($messageID) or strlen($messageID) > 25) {
            $this->validationErrors[] = "MessageID needed";
        }

        if(empty($recipient) or strlen($recipient) > 25) {
            $this->validationErrors[] = "Recipient needed";
        }

        if(!isset($sender) || trim($sender) == "") {
            $this->validationErrors[] = "Sender needed, can only contain letters and numbers";
        }

        if(!isset($subject) || trim($subject) == "") {
            $this->validationErrors[] = "Subject needed, can only contain letters and numbers";
        }

        return $this->validationErrors;
    }

}
