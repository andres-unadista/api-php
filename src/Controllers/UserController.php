<?php
namespace App\Controllers;

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

      $validate = $this->validateInputs($rulesValidate, $this->data);
      if (count($validate) > 0) {
        echo json_encode($validate);
      } else {
        echo json_encode($this->data);
      }
      exit;
    }
  }
}