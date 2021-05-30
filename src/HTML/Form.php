<?php
namespace App\HTML;

class Form {
  private $data;

  private $errors;

  public function __construct($data, array $errors) {
      $this->data = $data;
      $this->errors = $errors;
  }

  public function input (string $key, string $label, string $value = ""): string {
    $type = $key === "password" ? "password" : "text";
    return <<<HTML
      <div class="form-group">
        <label for="field{$key}">{$label}</label>
        <input type="{$type}" id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}" value="{$value}" required>
        {$this->getErrorFeedback($key)}
      </div>
    HTML;
  }

  private function getInputClass (string $key): string 
  {
    $inputClass = 'form-control';
    if (isset($this->errors[$key])) {
        $inputClass .= ' is-invalid';
    }
    return $inputClass;
  }

  private function getErrorFeedback (string $key): string {
    if (isset($this->errors[$key])) {
      if (is_array($this->errors[$key])) {
        $error = implode('<br>', $this->errors[$key]);
      } else {
        $error = $this->errors[$key];
      }
      return '<div class="invalid-feedback">' . $error . '</div>';
    }
    return '';
  }
};