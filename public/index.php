<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Config\ResponseHTTP;
use App\Config\ErrorLog;

ErrorLog::activateErrorLog();

if (isset($_GET['route']) && $_GET['route'] !== 'index.php') {
  $url = explode('/', $_GET['route']);
  $routes = ['auth', 'user'];
  $file = __DIR__;
  $file = str_replace('\\', '/', $file);

  $file = dirname($file) . '/src/Routes/' . $url[0] . '.php';

  if (!in_array($url[0], $routes)) {
    echo 'Route not exists';
    exit;
  }

  if (is_readable($file)) {
    require_once $file;
    exit;
  } else {
    echo ResponseHTTP::status_500();
  }

} else {
  echo ResponseHTTP::status_404();

}