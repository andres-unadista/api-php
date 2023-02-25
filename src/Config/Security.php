<?php

namespace App\Config;

use Dotenv\Dotenv;

class Security
{
  final public static function secretKey()
  {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
    $dotenv->load();
    return $_ENV['SECRET_KEY'];
  }

  final public static function createPassword(string $pass)
  {
    $pass = password_hash($pass, PASSWORD_DEFAULT);
    return $pass;
  }

  final public static function verifyPassword(string $pass, string $passHash)
  {
    $verify = password_verify($pass, $passHash);
    return $verify;
  }
}