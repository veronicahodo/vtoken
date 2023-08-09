<?php

// vtokens.php

// My class for dealing with token management.
// DOES NOT do any form of real authentication
require_once('vcrud.php');

class VTOKEN {
  private $token;  // Magic token itself
  private $userId;  // userId of the token owner
  private $expiration;  // when the token is no longer valid
  private $timeLimit;  // how long in the future to set expiration
  
  
  function __construct($timeLimit = 30000) {
    $this->token = bin2hex(random_bytes(32));
    $this->userId = 0;
    $this->expiration = '';
  }

  function generateToken($userId, CRUD $c) {
    // This is what we call 
    $this->userId = $userId;
    $this->expiration = date('YmdHis',strtotime("+".$this->timeLimit." seconds",strtotime(date("Y-m-d H:i:s")));
    $this->userId = $userId;

    $c->delete('tokens',[
               ['userId','=',$userId],
               ['expiration','<',date('YmdHis')]
               ]);
    $c->create('tokens',[
                 'userId' => $this->userId,
                 'expiration' => $this->expiration,
                 'token' => $this->token
               ]);
    
  }
}
