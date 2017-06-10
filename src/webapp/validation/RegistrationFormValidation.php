<?php

namespace analysiswebapp\webapp\validation;

use analysiswebapp\webapp\models\User;

class RegistrationFormValidation
{

    private $validationErrors = [];

    public function __construct($username, $password, $first_name, $last_name, $email)
    {
        return $this->validate($username, $password, $first_name, $last_name, $email);
    }

    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($username, $password, $first_name, $last_name, $email)
    {
        if(!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
            $this->validationErrors[] = 'Username can only contain letters and numbers';
        }

        if(!preg_match('/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,25}$/', $password)) {
            $this->validationErrors[] = 'Wrong password combination';
        }

        if(empty($first_name)) {
            $this->validationErrors[] = "Please write in your first name";
        }

         if(empty($last_name)) {
            $this->validationErrors[] = "Please write in your last name";
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->validationErrors[] = "Invalid email format on email";
        }
    }
}
