<?php 

namespace App\Constructors;
use App\Internal\Event;
use DateTime;

class Input {
  private $colors = [
    "#FF1493", "#FF4500", "#FFA500", "#FFD700", "#EE82EE", "#FF00FF", "#8A2BE2", "#7B68EE", "#ADFF2F", "#20B2AA", "#1E90FF", "#4169E1" 
  ];

  public function __construct() {}

  public function FieldWithLabel(string $label, string $key, string $type, ?string $extra_class = "", ?string $value = null) {
    $id = $this->generateRandomString();
    switch($type) {
      case 'textarea':
        $input = "<textarea  id='{$id}' class='form__input' name='{$key}'></textarea>";
        break;
      case 'time':
        $input = "<input id='{$id}' type='{$type}' class='form__input' name='{$key}' max='23:59' placeholder=' '/>";
        break;
      case 'checkbox':
        $input = "{$label} <input id='{$id}' type='{$type}' class='form__input' name='{$key}' placeholder=' '/>";
        break;
      default:
        $input = "<input id='{$id}' type='{$type}' class='form__input' name='{$key}' placeholder=' '/>";
    }

    return <<<HTML
      <div class="form__div block {$extra_class}">
        {$input}
        <label for="{$id}"  class="form__label">{$label}</label>
      </div>
    HTML;
  }

  public function FieldRowWithLabel(string $label, string $key, string $type) {
    return <<<HTML
      <div class='form__div block full field-row'>
        <div class='flex-between'>
          <label for="{$key}">{$label}</label>
          <input type='{$type}' class='form__input' name='{$key}' placeholder=' '/>
        </div>
      </div>
    HTML;
  }

  public function FieldWithPattern(string $label, string $key, string $type, ?string $extra_class = "", ?string $value = null, ?string $pattern = "") {
    return <<<HTML
      <div class="form__div block {$extra_class}">
        <input type='{$type}' pattern="{$pattern}" class='form__input' name='{$key}' placeholder=' '/>
        <label for="{$key}"  class="form__label">{$label}</label>
      </div>
    HTML;
  }

  protected function Choice(int $id, ?string $color = null, string $title): string {
    return <<<HTML
      <li class="choice flex" data-id="{$id}">
        <div class="flex-center mr-2" style="flex: 0 1 0">
          <i class="round" style="background: {$color}"></i>
        </div>
        <div class="flex-y-center">
          <span>{$title}</span>
        </div>
        <div class="flex110">
          <i class="delete-btn flex-y-center fas fa-ban"></i>
        </div>
      </li>
    HTML;
  }

  protected function FieldFile(string $label, string $key, ?string $value = null): string {
    return <<<HTML
      <div class="full flex-x">
        <input type="file" name="{$key}"/>
        <label for="{$key}">{$label}</label>
        <img src="#" alt="signature preview" />
      </div>
    HTML;
  }

  protected function ColorsChoice(): string {
    $html = "";
    
    foreach($this->colors as $color) {
      $data_color = str_replace("#", "", $color);
      $html .= "<div data-color='{$color}' style='background: {$color}' class='color__choices'></div>";
    }

    return <<<HTML
      <div class="full title-section colors-choice">
        <span class="underline color-choice__title">Selection de couleur rapide</span>
        <div class="flex">
          {$html}
        </div>
      </div>
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
          <input name="{$key}" type="color"/>
        </div>
      </div>
    HTML;
  }

  protected function Dropdown(string $label, string $key, ?array $options = [], ?string $k = ""): string {
    $optionsHTML = [];

    foreach($options as $option) {
      $id_event = $option['id_event'] ?? '';
      $optionsHTML[] = "<li><span data-id='{$id_event}'>{$option[$k]}</span></li>";
    }
    $optionsHTML = implode('', $optionsHTML);
    
    return <<<HTML
      <div class="dropdown filled form__div full">
        <ul>
          {$optionsHTML} 
        </ul>
        <input type="text" name="{$key}" class="form__input" placeholder=" "/>
        <label class="form__label">{$label}</label>
      </div>
    HTML;
  }

  private function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
  }
}

class Forms extends Input {
  private $employees;
  private $labels;
  private $events;

  public function __construct(?array $labels = [], ?array $events = [], ?array $employees = [], ?array $leaveEvents = []) {
    $this->employees = $employees;
    $this->labels = $labels;
    $this->events = $events;
    $this->leaves = $leaveEvents;
  }

  /**
   * Fonction imprimant la fenêtre comportant les erreurs
   */
  public function draw_alert(string $message): string {
    return <<<HTML
      <div class="alert-notice">
        {$message}
        <span onclick="this.parentElement.style.display='none';">&times;</span>
        
      </div>
    HTML;
  }

  /**
   * fonction imprimant la fenêtre du formulaire pour l'édition ou l'ajout
   * de projet / évennement / employé / libellé
   * @param string $type le sujet du formulaire
   * @param string $id Id de la section
   * @return string
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
      <div class="modal">
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
      </div>
    HTML;
  }

  protected function draw_section(string $title): string {
    return <<<HTML
      <div class='full title-section'>
        <span class="underline">{$title}</span>
      </div>
    HTML;
  }

  /**
   * Génère la fenêtre modale pour ajouter ou modifier un évennements au 
   * calendrier hebdomadaire
   * @return string
   */
  public function draw_timesheet_form(string $id, array $events): string {
    return <<<HTML
      <div id="{$id}" class="manage__container">
        <!-- <form method="POST" onsubmit="sendTimesheetEvent(event,this)" class="grid manage__wrapper"> -->
        <div class="grid manage__wrapper">
          <input name="id_event" type="hidden" />
          {$this->Dropdown("Projet", "project", $events, "title_event")}
          {$this->FieldWithLabel("Journée", "date", "date", "full")}
          <div></div>
          {$this->FieldWithLabel("Nombre d'heures", "hours_invested", "number")}
          {$this->FieldWithLabel("Heure de début", "start", "time")}
          {$this->FieldWithLabel("Heure de fin", "end", "time")}
          {$this->FieldWithLabel("Description", "description", "textarea", "full grid-height")}
          <div class="full flex-end mt-2 panel-option">
            <button class="save-btn mr-2">
              <i class="fas fa-check-circle"></i>
              Ajouter
            </button>
            <button class="close-btn">
              <i class="fas fa-times-circle"></i>
              Fermeture
            </button>
          </div>
        <!-- </form> -->
        </div>
      </div>
    HTML;
  }

  /**
   * génère le header du formulaire
   * @return string
   */
  private function draw_header(): string {
    return <<<HTML
      <div class="flex">
        <div class="manage__title flex-center is-active" data-ctx="edit">
          <i class="fas fa-pen edit-btn"></i>
          Éditer
        </div>
        <div class="manage__title flex-center" data-ctx="add">
          <i class="fas fa-plus add-btn"></i>
          Ajouter
        </div>
      </div>
    HTML;
  }

  /**
   * Génère la liste des options pour choisir quel sujet va être modifié
   * @param array $options Liste des choix
   * @param string $type Type du formulaire
   * @return string
   */
  private function draw_list(array $options, string $type): string {
    $optionsHTML = [];
    
    switch($type) {
      case "employé":
        foreach($options as $option) {
          $optionsHTML[] = $this->Choice(intval($option['id']), null, "{$option['last_name']}, {$option['first_name']}");
        }
      break;

      case "projet":
        foreach($options as $option) {
          $optionsHTML[] = $this->Choice(intval($option['id_event']), $option['color'], $option['title_event']);
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

  /**
   * Génère le formulaire pour l'ajout ou l'édition des libellés
   * @return string
   */
  private function draw_form_label(): string {
    return <<<HTML
      {$this->FieldWithLabel("Nom", "name", "text")}
      {$this->FieldColor("Couleur du libellé", "color", null)}
      {$this->FieldWithLabel("affaires mondiales Canada", "amc", "checkbox", "full flex-y-center-imp flex-between fz-14")}
      {$this->ColorsChoice()}
      
    HTML;
  }

  /**
   * Génère le formulaire pour l'ajout ou l'édition des employés
   * @return string
   */
  private function draw_form_employee(): string {
    $leaves = (new Event())->getByType(true, $_SESSION['id']);

    $leaveshtml = [];
    foreach($leaves as $leave) {
      $leaveshtml[] = $this->FieldRowWithLabel($leave['title_event'], str_replace(' ', '_', $leave['title_event']), 'number');
    }


    $leaveshtml = implode('', $leaveshtml);
    return <<<HTML
      {$this->FieldWithLabel("Nom d'utilisateur", "username", "text", "full")}
      {$this->FieldWithLabel("Prénom", "first_name", "text")}
      {$this->FieldWithLabel("Nom de famille", "last_name", "text")}
      {$this->FieldWithLabel("Mot de passe", "password", "password")}
      {$this->FieldWithLabel("Cet utilisateur est adminisatrateur", "role", "checkbox", "full flex-y-center-imp flex-between fz-14")}
      {$this->FieldWithLabel("Cet utilisateur est régulier", "regular", "checkbox", "full flex-y-center-imp flex-between fz-14")}
      {$this->FieldWithLabel("Taux régulier", "rate", "number")}
      {$this->FieldWithLabel("Taux AMC", "rate_amc", "number")}
      {$this->FieldWithLabel("Taux CSI", "rate_csi", "number")}
      {$this->FieldFile("Signature électronique de l'employée", "file_to_upload")}
      {$this->draw_section("Édition des congés")}
      <div id='employee-leave' class='grid full'>
        {$leaveshtml}
      </div>
    HTML;
  }

  /**
   * Génère le formulaire pour l'ajout ou l'édition des projets
   * @return string
   */
  private function draw_form_event(): string {
    // var_dump($this->labels);
    return <<<HTML
      {$this->Dropdown("Libellé", "label", $this->labels, "title")}
      {$this->FieldWithPattern("Référence", "ref", "text", null, null, "[A-Z]{2}[0-9]{2}[0-9]{4}")}
      {$this->FieldWithLabel("Titre", "title", "text", "full")}
      {$this->FieldWithLabel("Heures maximum par jour", "max_hours_per_day", "number", "full")}
      {$this->FieldWithLabel("Heures maximum par semaine", "max_hours_per_week", "number", "full")}
    HTML;
  }

  /**
   * Génère le bon formulaire selon le type
   * @param string $type Type de formulaire
   * @return string
   */
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
        <input name="id" type="hidden" />
        <div class="grid px-1 mb-4">
          {$form}
        </div>
      </div>
    HTML;
  }

}
