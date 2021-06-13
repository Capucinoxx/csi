<?php
namespace App\HTML;

class Form {
  private $data;

  private $errors;

  public function __construct($data = null, array $errors = []) {
      $this->data = $data;
      $this->errors = $errors;
  }


  public function formField(string $icon, string $label, string $key, string $type, bool $border = false): string {
    $class = $border ? ' border-bottom' : '';
    return <<<HTML
      <div class="flex-field{$class}">
        <div class="flex-center">
          <i class="{$icon}"></i>
        </div>
        <div class="form__div">
          <input type="{$type}" class="form__input" placeholder=" ">
          <label for="" name="{$key}" class="form__label">{$label}</label>
        </div>
      </div>
    HTML;
  }

  public function formFieldColor(string $icon, string $label, string $key, bool $border = false): string { 
    $class = $border ? ' border-bottom' : '';
    return <<<HTML
      <div class="flex-field{$class}">
        <div class="flex-center">
          <i class="{$icon}"></i>
        </div>
        <div class="form__div">
          <input type="color" class="form__input input-color" placeholder=" ">
          <label for="" name="{$key}" class="form__label">{$label}</label>
        </div>
      </div>
    HTML;
  }

  public function formFieldOptions(string $icon, string $label, string $key, array $options, bool $border = false): string {
    $optionsHTML = [];
    $optionsHTML[] = "<option value=\" \" selected disabled hidden></option>";
    foreach($options as $k => $v) {
      $optionsHTML[] = "<option value=\"$k\">$v</option>";
    }
    $optionsHTML = implode('', $optionsHTML);
    return <<<HTML
      <div class="flex-field{$class}">
        <div class="flex-center">
          <i class="{$icon}"></i>
        </div>
        <div class="form__div">
          <select class="form__input" name="{$key}">{$optionsHTML}</select>
          <label for="" name="{$key}" class="form__label">{$label}</label>
        </div>
      </div>
    HTML;
  }



  public function input (string $key, string $value = "", string $type = "text", string $placeholder = ""): string {
    $extra_class = $type == "color" ? "input-color " : "";
    return <<<HTML
      <div class="form-group">
        <input 
          type="{$type}" 
          id="field{$key}" 
          class="{$extra_class}{$this->getInputClass($key)}" 
          name="{$key}" 
          value="{$value}" 
          autocomplete="off"
          placeholder="{$placeholder}"
          required

        >
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