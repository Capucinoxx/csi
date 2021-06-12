<?php
namespace App\HTML;

class Form {
  private $data;

  private $errors;

  public function __construct($data, array $errors) {
      $this->data = $data;
      $this->errors = $errors;
  }

  public function input (string $key, string $label, string $value = "", string $type = "text"): string {
    $extra_class = $type == "color" ? "input-color " : "";
    return <<<HTML
      <div class="form-group">
        <label for="field{$key}">{$label}</label>
        <input type="{$type}" id="field{$key}" class="{$extra_class}{$this->getInputClass($key)}" name="{$key}" value="{$value}" required>
        {$this->getErrorFeedback($key)}
      </div>
    HTML;
  }

  public function number (string $key, string $label, string $value = "00.01"): string {
    return <<<HTML
      <div class="form-group">
        <label for="field{$key}">{$label}</label>
        <input class="{$this->getInputClass($key)}" type="number" min="0.01" step="0.01" max="2500" value="{$value}">
      </div>
    HTML;
  }

  public function select (string $key, string $label, array $options = [], string $value = ""): string {
    $optionsHTML = [];
    foreach($options as $k => $v) {
      $selected = $v == $value ? " selected" : "";
      $optionsHTML[] = "<option value=\"$v\" $selected>$v</option>";
    }
    $optionsHTML = implode('', $optionsHTML);
    return <<<HTML
      <div class="form-group">
        <label for="field{$key}">{$label}</label>
        <select id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}" required>{$optionsHTML}</select>
        {$this->getErrorFeedback($key)}
      </div>
    HTML;
  }

  public function date (string $key, string $label, string $value = ""): string {
    return <<<HTML
      <div class="form-group">
        <label for="field{$key}">{$label}</label>
        <input type="date" id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}" value="{$value}" required>
        {$this->getErrorFeedback($key)}
      </div>
    HTML;
  }

  private function getInputClass (string $key): string {
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