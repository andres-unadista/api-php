<?php
namespace App\Controllers;

use App\Config\Security;
use App\Models\UserModel;

class UserController extends Controller
{
  public function __construct(
    private string $method,
    private string $route,
    private array $params,
    private $data,
    private $headers
  )
  {
  }


  /* GET USERS */
  final public function getAll(string $endpoint)
  {
    if (strtolower($this->method) === 'get' && trim($endpoint, '/') === $this->route) {
      Security::verifyToken($this->headers, Security::secretKey());
      $userModel = new UserModel();
      $user = $userModel->getAll();
      echo json_encode($user);
      exit;
    }
  }
  /* DELETE USER */
  final public function delete(string $endpoint)
  {
    if (strtolower($this->method) === 'delete' && trim($endpoint, '/') === $this->route) {
      Security::verifyToken($this->headers, Security::secretKey());
      $userModel = new UserModel();
      $idToken = $this->data['id_token'];
      $user = $userModel->delete($idToken);
      echo json_encode($user);
      exit;
    }
  }
  /* UPDATE USER */
  final public function updatePassword(string $endpoint)
  {
    if (strtolower($this->method) === 'patch' && trim($endpoint, '/') === $this->route) {
      Security::verifyToken($this->headers, Security::secretKey());

      $rulesValidate = [
        "id_token"     => 'required',
        "old_password" => 'required|length:8',
        "password"     => 'required|length:8',
      ];

      $validateErrors = $this->validateInputs($rulesValidate, $this->data);
      if (count($validateErrors) > 0) {
        echo json_encode($validateErrors);
      } else {
        $userModel = new UserModel();
        $user = $userModel->updatePassword($this->data);
        echo json_encode($user);
      }
      exit;
    }
  }
  final public function getOne(string $endpoint)
  {
    if (strtolower($this->method) === 'get' && trim($endpoint, '/') === $this->route) {
      Security::verifyToken($this->headers, Security::secretKey());
      $data = [
        "dni" => $this->params[1]
      ];

      $rulesValidate = [
        "dni" => 'required|number'
      ];

      $validateErrors = $this->validateInputs($rulesValidate, $data);
      if (count($validateErrors) > 0) {
        echo json_encode($validateErrors);
      } else {
        $userModel = new UserModel();
        $user = $userModel->get(dni: $data['dni']);
        echo json_encode($user);
      }
      exit;
    }
  }

  /* LOGIN */
  final public function login(string $endpoint)
  {
    if (
      strtolower($this->method) === 'post' &&
      trim($endpoint, '/') === $this->route
    ) {
      $rulesValidate = [
        'email'    => 'required|email',
        'password' => 'required',
      ];
      $validateErrors = $this->validateInputs($rulesValidate, $this->data);
      if (count($validateErrors) > 0) {
        echo json_encode($validateErrors);
      } else {
        $email = strtolower($this->data['email']);
        $password = $this->data['password'];
        $userModel = new UserModel();
        $user = $userModel->login(password: $password, email: $email);
        echo json_encode($user);
      }
      exit;
    }
  }

  /* INSERT USER */
  final public function post(string $endpoint)
  {
    if (strtolower($this->method) === 'post' && trim($endpoint, '/') === $this->route) {
      $rulesValidate = [
        'name'             => 'required|string',
        'dni'              => 'required|number',
        'email'            => 'required|email',
        'rol'              => 'required|rol',
        'password'         => 'required|length:8',
        'confirm_password' => 'required|equal:password',
      ];

      $validateErrors = $this->validateInputs($rulesValidate, $this->data);
      if (count($validateErrors) > 0) {
        echo json_encode($validateErrors);
      } else {
        $userModel = new UserModel();
        $user = $userModel->save($this->data);
        echo json_encode($user);
      }
      exit;
    }
  }
}