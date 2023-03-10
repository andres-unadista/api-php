<?php
namespace App\Models;

use App\Config\ResponseHTTP;
use App\Config\Security;
use PDOException;

class User extends Model
{
  public string $table = 'user';

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

  public function delete($idToken)
  {
    try {
      $query = "DELETE FROM user WHERE id_token = :id_token";
      $stm = $this->conn->prepare($query);
      $stm->execute([
        'id_token' => $idToken
      ]);
      if ($stm->rowCount() > 0) {
        return ResponseHTTP::status_204();
      }
      return ResponseHTTP::status_500('Internal error when deleting user');
    } catch (PDOException $e) {
      error_log('Error User::delete =>' . $e);
      return ResponseHTTP::status_500('Internal error when deleting user');
    }
  }

  public function updatePassword($data)
  {
    try {
      $user = parent::find('id_token', '=', $data['id_token']);
      if (!Security::verifyPassword($data['old_password'], $user['password'])) {
        error_log('Incorrect password');
        return ResponseHTTP::status_400();
      }
      $query = "UPDATE user SET password = :password WHERE id_token = :id_token";
      $stm = $this->conn->prepare($query);
      $stm->execute([
        'id_token' => $data['id_token'],
        'password' => Security::createPassword($data['password']),
      ]);
      if ($stm->rowCount() > 0) {
        return ResponseHTTP::status_200('User updated');
      }
      return ResponseHTTP::status_500('Internal error when updating user');
    } catch (PDOException $e) {
      error_log('Error User::updatePassword => ' . $e);
      return ResponseHTTP::status_500('Internal error when updating user');
    }

  }

  public function get(string $dni)
  {
    try {
      $user = parent::find('dni', '=', $dni);
      if ($user) {
        return ResponseHTTP::status_200($user);
      }
      return ResponseHTTP::status_404('DNI not found');
    } catch (PDOException $e) {
      error_log('Get user => ' . $e);
      return ResponseHTTP::status_500();
    }
  }

  public function getAll()
  {
    try {
      $columns = 'id_user, name, dni, email, rol, id_token';
      $users = parent::findAll($columns);
      return ResponseHTTP::status_200($users);
    } catch (PDOException $e) {
      error_log('Get users => ' . $e);
      return ResponseHTTP::status_500();
    }
  }
}