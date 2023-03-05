<?php

use App\Config\ResponseHTTP;
use App\Controllers\ProductController;

$method = $_SERVER['REQUEST_METHOD'];
$route = $_GET['route'];
$params = explode('/', $route);
$data = $_POST ?? json_decode(file_get_contents('php://input'), true);
$headers = getallheaders();
$product = new ProductController(
  $method,
  $route,
  $params,
  $data,
  $headers
);

$product->save('/product');

echo json_encode(ResponseHTTP::status_404());