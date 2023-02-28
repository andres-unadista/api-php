<?php
namespace App\Controllers;

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
  final public function login(string $endpoint)
  {
    if (
      strtolower($this->method) === 'post' &&
      str_replace('/', '', $endpoint) === $this->route
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

  final public function post(string $endpoint)
  {
    if (strtolower($this->method) === 'post' && str_replace('/', '', $endpoint) === $this->route) {
      header('Content-Type: application/json');
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