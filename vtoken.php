<?php

// vtokens.php

// My class for dealing with token management.
// DOES NOT do any form of real authentication

// Honestly, I don't know enough about instances but it feels like this could potentionally be one

// Version 0.1.0

require_once('vcrud.php');

class VTOKEN
{
  private $fields;
  private $token;  // Magic token itself
  private $userId;  // userId of the token owner
  private $expiration;  // when the token is no longer valid
  private $timeLimit;  // how long in the future to set expiration


  function __construct($timeLimit = 30000)
  {

    $this->fields['token'] = bin2hex(random_bytes(32));
    $this->fields['userId'] = 0;
    $this->fields['expiration'] = '';
  }

  function generateToken($userId, VCRUD $c)
  {
    $this->fields['userId'] = $userId;
    $this->fields['expiration'] = date('YmdHis', strtotime("+" . $this->timeLimit . " seconds", strtotime(date("Y-m-d H:i:s"))));
    $this->fields['token'] = bin2hex(random_bytes(32));

    $this->flushExpired($userId, $c);
    $c->create('tokens', $this->fields);
    return $this->fields['token'];
  }

  function flushExpired($userId, VCRUD $c)
  {
    $c->delete('tokens', [
      ['userId', '=', $userId],
      ['expiration', '<', date('YmdHis')]
    ]);
  }


  function flushAll($userId, VCRUD $c)
  {
    $c->delete('tokens', [['userId', '=', $userId]]);
  }


  function getUserIdFromToken($token, VCRUD $c)
  {
    // If the userId exists in the DB
    if ($user = $c->read('tokens', [['token', '=', $token]])) {
      // If not expired
      if ($user[0]['expiration'] > date('YmdHis')) {
        return $user[0]['userId'];
      } else {
        // expired
        return false;
      }
    } else {
      // userId doesn't exist
      return false;
    }
  }
}
