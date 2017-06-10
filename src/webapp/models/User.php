<?php

namespace analysiswebapp\webapp\models;

class User
{

    protected $userId  = null;
    protected $username;
    protected $hash;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $isAdmin = 0;
    protected $failed_logins = 0;
    protected $first_failed_login = 0;

    function __construct($username, $hash, $firstName, $lastName, $email, $failed_logins=null, $first_failed_login=null)
    {
        $this->username = $username;
        $this->hash = $hash;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->failed_logins = $failed_logins;
        $this->first_failed_login = $first_failed_login;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

     public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

     public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;

    }

    public function isAdmin()
    {
        return $this->isAdmin === '1';
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function getFailed_logins() {
       return $this->failed_logins;
   }

   public function setFailed_logins($failed_logins) {
       $this->failed_logins = $failed_logins;
   }

   public function getFirst_failed_login() {
      return $this->first_failed_login;
  }

  public function setFirst_failed_login($first_failed_login) {
      $this->first_failed_login = $first_failed_login;
  }

}
