<?php

use App\Config\ResponseHTTP;
use App\Controllers\UserController;

$method = $_SERVER['REQUEST_METHOD'];
$route = $_GET['route'];
$params = explode('/', $route);
$data = json_decode(file_get_contents('php://input'), true);
$headers = getallheaders();
$user = new UserController(
  method: $method,
  params: $params,
  route: $route,
  data: $data,
  headers: $headers
);

$user->post('/user');

$user->getAll("/user");

$user->delete("/user");
$user->updatePassword("/user");

$user->getOne("/user/$params[1]");

echo json_encode(ResponseHTTP::status_404());