<?php
namespace App\Controllers;

class Controller
{
  private string $validate_rol = '%^[1|2|3]$%';
  private string $validate_number = '%^[0-9]+$%';
  private string $validate_text = '%^\w+$%';
  final public function getArrayRules($rules): array
  {
    $rules = explode('|', $rules);
    $rules = array_map(
      function ($rule) {
        if (strpos($rule, ':')) {
          return explode(':', $rule);
        }
        return $rule;
      }
      ,
      $rules
    );
    return $rules;
  }

  final public function validateArrayRule($rules, $value, $key, $data): array
  {
    $errors = [];
    foreach ($rules as $rule) {
      if (is_string($rule)) {
        if ($rule === 'required' && empty($value)) {
          $errors[] = "Input $key is required";
          return $errors;
        }
        if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
          $errors[] = "$key has invalid format email";
        }
        if ($rule === 'number' && !preg_match($this->validate_number, $value)) {
          $errors[] = "Input $key must have a numeric value";
        }
        if ($rule === 'rol' && !preg_match($this->validate_rol, $value)) {
          $errors[] = "Input $key must have a valid role";
        }
      } else {
        if ($rule[0] === 'length' && mb_strlen($value) < $rule[1]) {
          $errors[] = "Input $key must be $rule[1] characters long";
        }
        if ($rule[0] === 'equal' && $data[$rule[1]] !== $value) {
          $errors[] = "Input $key must be equal to $rule[1]";
        }
      }
    }
    return $errors;
  }

  final public function validateInputs(array $rules, $data): array
  {
    $validateInputs = [];
    $copyRules = $rules;

    foreach ($copyRules as $key => $rule) {
      $copyRules = $this->getArrayRules($rule);
      if ($copyRules[0] === 'required' && empty($data[$key])) {
        $validateInputs[] = "Input $key is required";
      }
    }
    if (count($validateInputs) > 0) {
      return $validateInputs;
    }


    array_map(function ($input, $key) use ($rules, $data, &$validateInputs) {
      $errors = [];
      $rules = $this->getArrayRules($rules[$key]);
      $errors = $this->validateArrayRule($rules, $input, $key, $data);

      if (count($errors) > 0) {
        $validateInputs = array_merge($validateInputs, $errors);
      }
    }, $data, array_keys($data));

    return $validateInputs;
  }
}