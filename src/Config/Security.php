<?php

namespace App\Config;

use Dotenv\Dotenv;
use FFI\Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Security
{
  private static $jwt_data = [];
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

  final public static function createJWT(string $key, array $data)
  {
    $payload = [
      "iat"  => time(),
      "exp"  => time() + (60 * 60),
      "data" => $data
    ];

    $jwt = JWT::encode($payload, $key, 'HS256');
    return $jwt;
  }

  final public static function verifyToken(array $token, string $key)
  {
    if (!isset($token['Authorization'])) {
      die(json_encode(ResponseHTTP::status_400('Invalid token')));
    }

    try {
      $jwt = explode(" ", $token['Authorization']);
      $data = JWT::decode($jwt[1], new Key($key, 'HS256'));
      self::$jwt_data = $data;
      return $data;
    } catch (\Exception $e) {
      error_log('Token invalid => ' . $e);
      die(json_encode(ResponseHTTP::status_401()));
    }
  }

  final public static function getDataJWT()
  {
    $jwt_decoded = (array) self::$jwt_data;
    return $jwt_decoded;
  }
}