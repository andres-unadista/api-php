<?php
namespace App\Models;

use App\Config\ResponseHTTP;
use App\Config\Security;
use PDOException;

class UserModel extends Model
{
  private string $name;
  public string $table = 'user';
  private string $dni;
  private string $email;
  private string $rol;
  private string $password;
  private string $token;
  private string $dateCreated;

  public function login(string $password, string $email)
  {
    try {
      $userEmail = parent::find('email', '=', $email);
      if (!$userEmail) {
        return ResponseHTTP::status_400('invalid email or password');
      }
      if (Security::verifyPassword($password, $userEmail['password'])) {
        $payload = ['id_token' => $userEmail['id_token']];
        $data = [
          'name'  => $userEmail['name'],
          'rol'   => $userEmail['rol'],
          'token' => Security::createJWT(Security::secretKey(), $payload)
        ];
        return ResponseHTTP::status_200($data);
      }
      return ResponseHTTP::status_400('invalid email or password');
    } catch (PDOException $e) {
      error_log('UserModel->login', $e->getMessage());
      return ResponseHTTP::status_500();
    }
  }
  public function save(array $data)
  {
    try {
      $userDNI = parent::find('dni', '=', $data['dni']);
      $userEmail = parent::find('email', '=', $data['email']);
      $data['password'] = Security::createPassword($data['password']);
      $data['id_token'] = hash('sha512', $data['dni'] . $data['email']);
      $data['date_birth'] = date('Y-m-d');
      unset($data['confirm_password']);
      if ($userDNI) {
        return ResponseHTTP::status_400('DNI has already been taken');
      }
      if ($userEmail) {
        return ResponseHTTP::status_400('Email has already been taken');
      }
      return $this->insertUser($data);
    } catch (PDOException $e) {
      error_log('UserModel->insertUser', $e->getMessage());
      return ResponseHTTP::status_500();
    }
  }

  public function insertUser($data)
  {
    $query = "INSERT INTO user(name, dni, email, rol, password, id_token, date_birth) VALUES (:name,:dni,:email,:rol,:password,:id_token,:date_birth)";
    $stm = $this->conn->prepare($query);
    $stm->execute($data);
    if ($stm->rowCount() > 0) {
      return ResponseHTTP::status_201('User created');
    }
    return ResponseHTTP::status_500('Internal error when inserting user');
  }
}