<?php

namespace App\Config;

class ResponseHTTP
{
  private static $message = [
    'status' => '',
    'message' => ''
  ];
  final static public function status_200($response)
  {
    return self::handleStatus(200, $response);
  }
  final static public function status_201($response = 'Resource created')
  {
    return self::handleStatus(201, $response);
  }
  final static public function status_400($response = 'Request in incorrect format')
  {
    return self::handleStatus(400, $response);
  }
  final static public function status_401($response = 'Unauthorized')
  {
    return self::handleStatus(401, $response);
  }
  final static public function status_404($response = 'Route not found')
  {
    return self::handleStatus(404, $response);
  }
  final static public function status_500($response = 'Internal server error')
  {
    return self::handleStatus(500, $response);
  }

  private static function handleStatus(int $code, string $message, $fail = false)
  {
    header('Content-Type: application/json');
    http_response_code($code);
    if ($fail) {
      self::$message['status'] = 'error';
    } else {
      self::$message['status'] = 'ok';
    }
    self::$message['message'] = $message;
    return json_encode(self::$message);
  }
}