<?php

namespace App\Controllers;

use App\Config\ResponseHTTP;
use App\Config\Security;
use App\Models\Product;

class ProductController extends Controller
{
  final function save(string $endpoint)
  {
    if (trim($endpoint, '/') === $this->route && strtolower($this->method) === 'post') {
      Security::verifyToken($this->headers, Security::secretKey());

      $rules = [
        'name'        => 'required',
        'description' => 'required|desc',
        'stock'       => 'required|stock',
      ];

      $validateErrors = $this->validateInputs($rules, $this->data);
      if (count($validateErrors) > 0) {
        echo json_encode($validateErrors);
      } else {
        $productModel = new Product();
        $product = $productModel->save($this->data, $_FILES);
        echo json_encode($product);
      }
      exit;
    }
  }
}