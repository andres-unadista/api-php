<?php
namespace App\DB;

use App\Config\ResponseHTTP;
use Dotenv\Dotenv;

use PDO;
use PDOException;

class Connection
{
  private static $dns;
  private static $user;
  private static $password;
  private static $opt;

  public function __construct()
  {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
    $dotenv->load();

    $data_db = [
      'name'     => $_ENV['DB_NAME'],
      'user'     => $_ENV['DB_USER'],
      'password' => $_ENV['DB_PASSWORD'],
      'host'     => $_ENV['DB_HOST'],
      'port'     => $_ENV['DB_PORT'],
    ];

    self::$dns = "mysql:host={$data_db['host']}; port={$data_db['port']}; dbname={$data_db['name']}";
    self::$user = $data_db['user'];
    self::$password = $data_db['password'];
    self::$opt = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
  }
  final public static function getConnection()
  {
    try {
      new static ();
      $conn = new PDO(self::$dns, self::$user, self::$password, self::$opt);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      error_log('Conexión exitosa');
    } catch (PDOException $e) {
      error_log('Error connection: ' . $e->getMessage());
      die(ResponseHTTP::status_500());
    }
  }
}