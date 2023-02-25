<?php
use App\Config\Security;

var_dump(Security::secretKey());

$password = 'Hello';
$hash = Security::createPassword($password);

var_dump($hash);

if (Security::verifyPassword($password, $hash)) {
  var_dump('Contraseña válida');
} else {
  var_dump('Contraseña inválida');
}