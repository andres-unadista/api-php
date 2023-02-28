<?php

namespace App\Models;

use App\DB\Connection;
use PDO;

class Model
{
  public string $table = '';
  protected PDO $conn;

  public function __construct()
  {
    Connection::getConnection();
    $this->conn = Connection::$conn;
  }
  final public function find($column, $cond, $value)
  {
    $query = "SELECT * FROM {$this->table} WHERE {$column} {$cond} :{$column}";

    $stm = $this->conn->prepare($query);
    $stm->execute([
      $column => $value
    ]);
    return $stm->fetch();
  }
  final public function findAll($columns = '*')
  {
    $query = "SELECT {$columns} FROM {$this->table}";

    $stm = $this->conn->prepare($query);
    $stm->execute();
    return $stm->fetchAll();
  }
}