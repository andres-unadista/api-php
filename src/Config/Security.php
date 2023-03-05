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

  final public static function uploadFile(array $setupFile)
  {
    $name = $setupFile['input'];
    $path = $setupFile['path'];
    $maxSize = $setupFile['maxSize'];
    $typeFiles = $setupFile['types'];
    //Recogemos el file enviado por el formulario
    $file = $_FILES[$name]['name'];
    //Si el file contiene algo y es diferente de vacio
    if (isset($file) && $file != "") {
      //Obtenemos algunos datos necesarios sobre el file
      $type = $_FILES[$name]['type'];
      $size = $_FILES[$name]['size'];
      $temp = $_FILES[$name]['tmp_name'];
      //Se comprueba si el file a cargar es correcto observando su extensión y tamaño
      $isValidFile = false;
      foreach ($typeFiles as $validType) {
        if (strpos($type, $validType) !== false) {
          $isValidFile = true;
        }
      }
      if (!$isValidFile || $size > $maxSize) {
        return ['status' => false, 'msg'    => 'file format incorrect'];
      } else {
        //Si la imagen es correcta en tamaño y tipo
        //Se intenta subir al servidor
        if (move_uploaded_file($temp, $path)) {
          return [
            'status' => true,
            'msg'    => 'file upload'
          ];

        } else {
          return [
            'status' => false,
            'msg'    => 'error uploading file'
          ];

        }
      }
    } else {
      return [
        'status' => false,
        'msg'    => 'File empty'
      ];

    }
  }
  final public static function uploadImage($files, $nameInput, $nameFile)
  {

    if ($files[$nameInput]) {
      $typeFile = explode('.', $files[$nameInput]['name']);
      $completeName = "{$nameFile}.{$typeFile[1]}";
      $pathImage = str_replace('\\', '/', dirname(__DIR__, 2)) . "/public/images/{$completeName}";
      $dataFile = [
        'name'    => $completeName,
        'input'   => $nameInput,
        'path'    => $pathImage,
        'maxSize' => 500000,
        'types'   => ['jpeg', 'gif', 'jpg']
      ];
      $uploadFile = self::uploadFile($dataFile);
      if ($uploadFile['status']) {
        return $dataFile;
      } else {
        die(json_encode(ResponseHTTP::status_400($uploadFile['msg'])));
      }
    } else {
      die(json_encode(ResponseHTTP::status_400('File name not found')));
    }
  }
}