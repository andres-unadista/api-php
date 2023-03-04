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
  public static $conn;

  public function __construct()
  {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
    $dotenv->load();

    $data_db = [
      'name'      => $_ENV['DB_NAME'],
      'user'      => $_ENV['DB_USER'],
      'server_db' => $_ENV['SERVER_DB'],
      'password'  => $_ENV['DB_PASSWORD'],
      'host'      => $_ENV['DB_HOST'],
      'port'      => $_ENV['DB_PORT'],
    ];

    if ($data_db['server_db'] === 'mysql') {
      self::$dns = "mysql:host={$data_db['host']}; port={$data_db['port']}; dbname={$data_db['name']}";
    }
    if ($data_db['server_db'] === 'sqlserver') {
      self::$dns = "sqlsrv:server={$data_db['host']}, {$data_db['port']}; database={$data_db['name']}";
    }
    self::$user = $data_db['user'];
    self::$password = $data_db['password'];
    self::$opt = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
  }
  final public static function getConnection(): void
  {
    try {
      new static ();
      $conn = new PDO(self::$dns, self::$user, self::$password, self::$opt);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      error_log('Conexión exitosa');
      self::$conn = $conn;
    } catch (PDOException $e) {
      error_log('Error connection: ' . $e->getMessage());
      die(ResponseHTTP::status_500());
    }
  }
}