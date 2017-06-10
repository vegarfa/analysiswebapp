<?php

namespace analysiswebapp\webapp\validation;

class EditUserFormValidation
{
    private $validationErrors = [];

    public function __construct($email)
    {
        $this->validate($email);
    }

    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($email)
    {
        $this->validateEmail($email);
    }

    private function validateEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->validationErrors[] = "Invalid email format on email";
        }
    }
}
