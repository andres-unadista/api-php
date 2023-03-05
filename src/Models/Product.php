<?php

namespace App\Models;

use App\Config\ResponseHTTP;
use App\Config\Security;
use PDOException;

class Product extends Model
{
  public string $table = 'product';

  final public function save(array $product, $file)
  {
    $findProduct = parent::find('name', '=', $product['name']);
    if ($findProduct) {
      return ResponseHTTP::status_400('Product already exists');
    }

    try {
      $nameFile = hash('md5', $product['name']);
      $image = Security::uploadImage($file, 'product', $nameFile);
      $pathImage = $image['path'];
      $nameImage = $image['name'];
      $token = hash('md5', $product['name'] . $pathImage);

      $query = 'INSERT INTO product (name, description, stock, url, image_name, id_token) VALUES (:name, :description, :stock, :url, :image_name, :id_token)';
      $stm = $this->conn->prepare($query);
      $stm->execute([
        'name'        => $product['name'],
        'description' => $product['description'],
        'stock'       => $product['stock'],
        'image_name'  => $nameImage,
        'url'         => $pathImage,
        'id_token'    => $token
      ]);
      if ($stm->rowCount() > 0) {
        return ResponseHTTP::status_200('Registered product');
      } else {
        return ResponseHTTP::status_500('Error registering product');
      }
    } catch (PDOException $e) {
      error_log('Product::save => ' . $e);
      return ResponseHTTP::status_500('Error registering product');
    }
  }
}