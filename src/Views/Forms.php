<?php 

namespace App\Views;

class Input {
  public function __construct() {}

  protected function FieldWithLabel(string $label, string $key, string $type, ?string $extra_class = "", ?string $value = null) {
    return <<<HTML
      <div class="form__div block {$extra_class}">
        <input type="{$type}" class="form__input" placeholder=" ">
        <label for="" name="{$key}" class="form__label">{$label}</label>
      </div>
    HTML;
  }

  protected function Choice(int $id, ?string $color = null, string $title, ?array $informations = []): string {
    return <<<HTML
      <li class="choice flex" data-id="{$id}" data-informations="{$informations}">
        <div class="flex-center mr-2" style="flex: 0 1 0">
          <i class="round" style="background: {$color}"></i>
        </div>
        <div class="flex-y-center">
          <span>{$title}</span>
        </div>
      </li>
    HTML;
  }

  /**
   * Génère le rendu HTML pour un champ de couleur
   * @param string $label Nom que l'on veut voir sur le visuel
   * @param string $key Nom de l'input
   * @param string $value Valeur du champ
   */
  protected function FieldColor(string $label, string $key, ?string $value = null): string {
    return <<<HTML
      <div class="flex-align-center h-45">
        <label for="" class="mr-2 fz-14">{$label}</label>
        <div class="input-color-container">
          <input type="color"/>
        </div>
      </div>
    HTML;
  }
}

class Forms extends Input {
  private $employees;
  private $labels;
  private $events;

  public function __construct(?array $labels = [], ?array $events = [], ?array $employees = []) {
    $this->employees = $employees;
    $this->labels = $labels;
    $this->events = $events;
  }

  /**
   * fonction imprimant la fenêtre du formulaire pour l'édition ou l'ajout
   * de projet / évennement / employé / libellé
   * @param string $type le sujet du formulaire
   * @param string $id Id de la section
   */
  public function draw(string $type, string $id): string {
    $options = [];

    switch ($type) {
      case "libellé":
        $options = $this->labels;
        break;
      case "projet":
        $options = $this->events;
        break;
      case "employé":
        $options = $this->employees;
        break; 
    }

    return <<<HTML
      <section id="{$id}" class="manage__container">
        {$this->draw_header()}
        <div class="flex-end mt-2">
          <span class="notice">
            Veuillez sélectionner un {$type} pour pouvoir l'éditer, si jamais vous voulez 
            ajouter un {$type}, veuillez aller dans la section ajouter en haut de cette fenêtre
          </span>
        </div>
        <div class="manage__wrapper">
          {$this->FieldWithLabel("Recherche de {$type}", "key", "text")}
          <div class="flex-wrapper">
            <div class="choices ml-4">
              <ul class="scroll">
                {$this->draw_list($options, $type)}
              </ul>
            
            </div>
            {$this->draw_form($type)}
          </div>
          <div class="full flex-end mt-2 panel-option">
            <button class="save-btn mr-2">
              <i class="fas fa-check-circle"></i>
              Sauvegarder
            </button>
            <button class="close-btn">
              <i class="fas fa-times-circle"></i>
              Fermeture
            </button>
          </div>
        </div>
      </section>
    HTML;
  }

  /**
   * génère le header du formulaire
   */
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

  /**
   * Génère le 
   */
  private function draw_list(array $options, string $type): string {
    $optionsHTML = [];
    
    switch($type) {
      case "employé":
        foreach($options as $option) {
          $optionsHTML[] = $this->Choice(intval($option['id']), null, "{$option['last_name']}, {$option['first_name']}");
        }
      break;

      default:
      foreach($options as $option) {
        $optionsHTML[] = $this->Choice(intval($option['id']), $option['color'], $option['title']);
      }
      break;
    }
    
    $optionsHTML = implode('', $optionsHTML);

    return $optionsHTML;
  }

  private function draw_form_label(): string {
    return <<<HTML
      {$this->FieldWithLabel("Nom", "name", "text")}
      {$this->FieldColor("Couleur du libellé", "color", null)}
    HTML;
  }

  private function draw_form_employee(): string {
    return <<<HTML
      {$this->FieldWithLabel("Nom d'utilisateur", "username", "text", "full")}
      {$this->FieldWithLabel("Prénom", "first_name", "text")}
      {$this->FieldWithLabel("Nom de famille", "last_name", "text")}
    HTML;
  }

  private function draw_form_event(): string {
    return <<<HTML

    HTML;
  }

  private function draw_form(string $type): string {
    $form = "";

    switch($type) {
      case "libellé":
        $form = $this->draw_form_label();
        break;
      case "projet":
        $form = $this->draw_form_event();
        break;
      case "employé":
        $form = $this->draw_form_employee();
    }

    return <<<HTML
      <div class="edit-form mt-3">
        <div class="flex-start title-section mb-2">
          <div>
            <span class="underline">Édition de</span>
            <span class="name"></span>
          </div>
        </div>
        <div class="grid px-1">
          {$form}
        </div>
      </div>
    HTML;
  }
}
