<?php

namespace analysiswebapp\webapp;

use analysiswebapp\webapp\Hash;

class Token
{
  public function __construct()
  {
      if(!$this->isTokenSet()) {
        $_SESSION['token'] = $this->createToken();
      }
  }
  public function isTokenSet() {
    return isset($_SESSION['token']) == true;
  }

  public function validate($usr){
    return $_SESSION['token'] == $usr;
  }

  public function getToken() {
    return $_SESSION['token'];
  }

  public function createToken(){
    $randNumber = openssl_random_pseudo_bytes(16); //generate random string
    $randNumber = bin2hex($randNumber); //convert from binary to hex

    $hash = hash("sha512", $randNumber); //i guess no salt is needed

    return $hash;
  }

}
