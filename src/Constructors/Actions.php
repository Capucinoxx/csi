<?php

namespace App\Constructors;

class Actions {
  private $IEvent;
  private $Iemployee;
  private $ITimesheet;
  private $ILabel;

  public function __construct($IEvent, $IEmployee, $ITimesheet, $ILabel) {
    $this->IEvent = $IEvent;
    $this->IEmployee = $IEmployee;
    $this->ITimesheet = $ITimesheet;
  }

  public function execute() {
    switch($_SERVER["REQUEST_METHOD"]) {
      case "POST":
        isset($_POST["context"]) && $this->{$_POST["context"]}();
      break;

      case "GET":
        isset($_GET["context"]) && $this->{$_GET["context"]}();
      break;
    }
  }

  private function getTimesheetById() {
    $rep = ($this->ITimesheet)->getByID($_POST['id']);

    echo json_encode($rep);
    die();
  }

  /**
   * enregistre les informations utilisateur dans les variables de session
   */
  private function connect() {
    // on se connecte
    $rep = ($this->IEmployee)->login([
      'username' => $_POST['username'],
      'password' => $_POST['password']
    ]);

    if (!isset($rep['error'])) {
      // on store les informations dans les variables de sessions
      $_SESSION['loggedin'] = true;

      isset($rep['id']) && $_SESSION['id'] = $rep['id'];
      isset($rep['first_name']) && $_SESSION['first_name'] = $rep['first_name'];
      isset($rep['last_name']) && $_SESSION['last_name'] = $rep['last_name'];
      isset($rep['role']) && $_SESSION['role'] = $rep['role'];
    } else {
      $_SESSION['loggedin'] = false;
      $_SESSION['error'] = $rep['error'];
    }
  }

  /**
   * fait la passerelle entre la demande fait en javascript
   * et la partie logique en ce qui attrait à l'ajout d'évennement
   * dans la feuille de temps
   */
  private function addTimesheetEvent() {
    $start = $this->convertTime($_POST['start']);
    $end = $this->convertTime($_POST['end']);

    $rep = ($this->ITimesheet)->createTimesheet([
      'id_event' => $_POST['id_event'],
      'id_employee' => $_SESSION['id'],
      'start' => $start,
      'end' => $end,
      'at' => intval(date('U', strtotime($_POST['date']))),
      'hours_invested' => $end - $start,
      'description' => $_POST['description']
    ]);

    $this->check($rep);
    var_dump($rep);
    die();
  }

  /**
   * fait la passerelle entre la demande fait en javascript
   * et la partie logique en ce qui attrait à la modification 
   * d'évennement dans la feuille de temps
   */
  private function editTimesheetEvent() {
    $start = $this->convertTime($_POST['start']);
    $end = $this->convertTime($_POST['end']);

    $rep = ($this->ITimesheet)->updateTimesheet([
      'id' => $_POST['id'],
      'id_event' => $_POST['id_event'],
      'id_employee' => $_SESSION['id'],
      'start' => $start,
      'end' => $end,
      'at' => intval(date('U', strtotime($_POST['date']))),
      'hours_invested' => $end - $start,
      'description' => $_POST['description']
    ]);

    $this->check($rep);
    var_dump($rep);
    die();
  }

  /**
   * fait la passerelle entre la demande fait en javascript
   * et la partie logique en ce qui attrait à l'ajout de libellé
   */
  private function addLabel() {
    $rep = ($this->ILabel)->createLabel([
      'title' => $_POST['title'],
      'color' => $_POST['color'],
      'amc' => $_POST['amc']
    ]);

    $this->check($rep);
    var_dump($rep);
    die();
  }

  /**
   * fait la passerelle entre la demande fait en javascript
   * et la partie logique en ce qui attrait à l'ajout d'employée
   */
  private function addEmployee() {
    $rep = ($this->IEmployee)->createEmployee([
      'username' => $_POST['username'],
      'first_name' => $_POST['first_name'],
      'last_name' => $_POST['last_name'],
      'password' => $_POST['password'],
      'role' => $_POST['role'],
      'rate' => floatval($_POST['rate']),
      'rate_AMC' => floatval($_POST['rate_amc']),
      'rate_CSI' => floatval($_POST['rate_csi']),
      'created_at' => intval((new Datetime())->format('U')) * 1000
    ]);

    $this->check($rep);
    var_dump($rep);
    die();
  }

  /**
   * fait la passerelle entre la demande fait en javascript
   * et la partie logique en ce qui attrait à la modification d'employée
   */
  private function editEmployee() {
    $rep = ($this->IEmployee)->update([
      'id' => $_POST['id'],
      'username' => $_POST['username'],
      'first_name' => $_POST['first_name'],
      'last_name' => $_POST['last_name'],
      'password' => $_POST['password'],
      'role' => $_POST['role'],
      'rate' => floatval($_POST['rate']),
      'rate_AMC' => floatval($_POST['rate_amc']),
      'rate_CSI' => floatval($_POST['rate_csi']),
      'updated_at' => intval((new Datetime())->format('U')) * 1000
    ]);

    $this->check($rep);
    var_dump($rep);
    die();
  }

    /**
   * fait la passerelle entre la demande fait en javascript
   * et la partie logique en ce qui attrait à l'ajout de projet
   */
  private function addEvent() {
    $labels = ($this->ILabel)->get();
    $id_label = -1;
    foreach ($labels as $label) {
      if ($label['title'] == $_POST['key']) {
        $id_label = $label['id'];
        break;
      }
    }

    $rep = ($this->IEvent)->createEvent([
      'ref' => $_POST['ref'],
      'id_label' => $id_label,
      'title' => $_POST['title'],
      'max_hours_per_day' => $_POST['max_hours_per_day'],
      'max_hours_per_Week' => $_POST['max_hours_per_week']
    ]);

    $this->check($rep);
    var_dump($rep);
    die();
  }

  /**
   * déconnection l'utilisateur de sa session
   */
  private function disconnect() {
    // unset all of the session variables
    $_SESSION = array();

    header("Refresh:0");

    session_destroy();
  }

  /**
   * ajoute un évennement pour l'utilisateur courant
   */
  private function addTimesheet() {
    header("Refresh:0");
  }

  private function convertTime(string $time): float {
    $parts = explode(':', $time);
    return $parts[0] + floor(($parts[1]/60)*100) / 100;
  }

  private function check($obj) {
    var_dump($obj);
    session_start();
    if (isset($obj['error'])) {
      $_SESSION['error'] = $obj['error'];
    } else if (isset($obj->error)) {
      $_SESSION['error'] = $obj->error;
    } else {
      unset($_SESSION['error']);
    }
  }
}

?>

