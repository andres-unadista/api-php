<?php

namespace App\Config;

class ResponseHTTP
{
  private static $message = [
    'status'  => '',
    'message' => ''
  ];

  final static public function headerHttpDev($method)
  {
    if ($method === 'OPTIONS') {
      exit(0);
    }
    echo header('Access-Control-Allow-Origin: *');
    echo header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, PUT');
    echo header('Allow: GET, OPTIONS, PUT, POST, DELETE, PATCH');
    echo header('Access-Control-Allow-Headers: X-API-KEY, Origin, Authorization, X-Request-With, Content-Type, Accept');
    echo header('Content-Type: application/json');
  }

  final static public function headerHttpPro($method, $origin)
  {
    if (!isset($origin)) {
      die(json_encode(ResponseHTTP::status_401()));
    }

    $list = [''];

    if (in_array($origin, $list)) {
      if ($method === 'OPTIONS') {
        echo header('Access-Control-Allow-Origin:' . $origin);
        echo header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, PUT');
        echo header('Access-Control-Allow-Headers: X-API-KEY, Origin, Authorization, X-Request-With, Content-Type, Accept');
        exit(0);

      } else {
        echo header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, PUT');
        echo header('Allow: GET, OPTIONS, PUT, POST, DELETE, PATCH');
        echo header('Access-Control-Allow-Headers: X-API-KEY, Origin, Authorization, X-Request-With, Content-Type, Accept');
        echo header('Content-Type: application/json');
      }
    } else {
      die(json_encode(ResponseHTTP::status_401()));
    }
  }
  final static public function status_200($response)
  {
    return self::handleStatus(200, $response);
  }
  final static public function status_201($response = 'Resource created')
  {
    return self::handleStatus(201, $response);
  }
  final static public function status_204($response = '')
  {
    return self::handleStatus(204, $response);
  }
  final static public function status_400($response = 'Request in incorrect format')
  {
    return self::handleStatus(400, $response, true);
  }
  final static public function status_401($response = 'Unauthorized')
  {
    return self::handleStatus(401, $response, true);
  }
  final static public function status_404($response = 'Route not found')
  {
    return self::handleStatus(404, $response, true);
  }
  final static public function status_500($response = 'Internal server error')
  {
    return self::handleStatus(500, $response, true);
  }

  private static function handleStatus(int $code, string|array $message, $fail = false)
  {
    http_response_code($code);
    if ($fail) {
      self::$message['status'] = 'error';
    } else {
      self::$message['status'] = 'ok';
    }
    self::$message['message'] = $message;
    return self::$message;
  }
}