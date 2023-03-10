<?php

use App\Config\ResponseHTTP;
use App\Controllers\UserController;

$method = $_SERVER['REQUEST_METHOD'];
$route = $_GET['route'];
$params = explode('/', $route);
$data = json_decode(file_get_contents('php://input'), true);
$headers = getallheaders();
$user = new UserController(
  $method,
  $route,
  $params,
  $data,
  $headers
);

$user->login('/auth');

echo json_encode(ResponseHTTP::status_404());