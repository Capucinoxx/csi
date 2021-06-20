<?php 

namespace App\Views;

class Input {
  public function __construct() {}

  protected function FieldWithLabel(string $label, string $key, string $type, ?string $value = null) {
    return <<<HTML
      <div class="form__div">
        <input type="{$type}" class="form__input" placeholder=" ">
        <label for="" name="{$key}" class="form__label">{$label}</label>
      </div>
    HTML;
  }

  protected function Choice(int $id, ?string $color = null, string $title): string {
    return <<<HTML
      <li class="flex" data-id="{$id}">
        <div class="flex-center mr-2" style="flex: 0 1 0">
          <i class="round" style="background: {$color}"></i>
        </div>
        <div class="flex-y-center">
          <span>{$title}</span>
        </div>
      </li>
    HTML;
  }
}

class Forms extends Input {

  public function __construct() {}

  public function draw(array $options, string $type): string {
    $optionsHTML = [];
    foreach($options as $option) {
      $optionsHTML[] = $this->Choice(intval($option['id']), $option['color'], $option['title']);
    }
    $optionsHTML = implode('', $optionsHTML);

    return <<<HTML
      <section class="manage__container">
        {$this->draw_header()}
        <div class="flex-end mt-2">
          <span class="notice">
            Veuillez sélectionner un {$type} pour pouvoir l'éditer, si jamais vous voulez 
            ajouter un {$type}, veuillez aller dans la section ajouter en haut de cette fenêtre
          </span>
        </div>
        <div class="manage__wrapper">
          {$this->FieldWithLabel("Recherche de {$type}", "key", "text")}
          <ul class="ml-4">
            {$optionsHTML}
          </ul>
        </div>
      </section>
    HTML;
  }

  private function draw_header(): string {
    return <<<HTML
      <div class="flex">
        <div class="manage__title flex-center is-active">
          <i class="fas fa-pen edit-btn"></i>
          Éditer
        </div>
        <div class="manage__title flex-center">
          <i class="fas fa-plus add-btn"></i>
          Ajouter
        </div>
      </div>
    HTML;
  }

  private function draw_list(bool $with_color): string {
    
    
    return <<<HTML


    HTML;
  }
}
