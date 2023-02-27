<?php

namespace App\Model;

use App\DB\Connection;
use PDO;

class Model
{
  public static string $table = '';
  protected static PDO $conn;
  final public static function find($column, $cond, $value)
  {
    $table = self::$table;
    $query = "SELECT * FROM {$table} WHERE {$column} {$cond} :{$column}";
    Connection::getConnection();
    $db = Connection::$conn;
    $stm = $db->prepare($query);
    $stm->execute([
      $column => $value
    ]);
    return $stm->fetch();
  }
}