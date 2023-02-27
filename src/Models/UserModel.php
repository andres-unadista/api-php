<?php
namespace App\Model;

class UserModel
{
  private static string $name;
  private static string $dni;
  private static string $email;
  private static string $rol;
  private static string $password;
  private static string $token;
  private static string $dateCreated;

  public static function getName(): string
  {
    return self::$name;
  }

  public static function setName(string $name)
  {
    self::$name = $name;
  }

  public static function getDni(): string
  {
    return self::$dni;
  }

  public static function setDni(string $dni)
  {
    self::$dni = $dni;
  }

  public static function getEmail(): string
  {
    return self::$email;
  }

  public static function setEmail(string $email)
  {
    self::$email = $email;
  }

  public static function getRol(): string
  {
    return self::$rol;
  }

  public static function setRol(string $rol)
  {
    self::$rol = $rol;
  }

  public static function getPassword(): string
  {
    return self::$password;
  }

  public static function setPassword(string $password)
  {
    self::$password = $password;
  }

  public static function getToken(): string
  {
    return self::$token;
  }

  public static function setToken(string $token)
  {
    self::$token = $token;
  }

  public static function getDateCreated(): string
  {
    return self::$dateCreated;
  }

  public static function setDateCreated(string $dateCreated)
  {
    self::$dateCreated = $dateCreated;
  }

  public static function save()
  {
    Model::$table = 'user';
    $user = Model::find('dni', '=', '12345');
    return json_encode($user);

  }
}