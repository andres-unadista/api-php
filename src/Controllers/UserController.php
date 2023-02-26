<?php
namespace App\Controllers;

class UserController
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
      echo json_encode($this->data);
      exit;
    }
  }
}