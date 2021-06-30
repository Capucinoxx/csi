<?php

namespace App\Constructors;

class Actions {
  private $IEvent;
  private $Iemployee;

  public function __construct($IEvent, $IEmployee, $ITimesheet) {
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
   * ajout un 
   */
  private function addTimesheetEvent() {
    $start = $this->convertTime($_POST['start']);
    $end = $this->convertTime($_POST['end']);

    $resp = ($this->ITimesheet)->createTimesheet([
      'id_event' => $_POST['id_event'],
      'id_employee' => $_SESSION['id'],
      'start' => $start,
      'end' => $end,
      'at' => intval(date('U', strtotime($_POST['date']))),
      'hours_invested' => $end - $start,
      'description' => $_POST['description']
    ]);

    var_dump($resp);
    die();
  }

  private function editTimesheetEvent() {
    $start = $this->convertTime($_POST['start']);
    $end = $this->convertTime($_POST['end']);
    var_dump('update');

    $rep = ($this->ITimesheet)->update([
      'id' => $_POST['id'],
      'id_event' => $_POST['id_event'],
      'id_employee' => $_SESSION['id'],
      'start' => $start,
      'end' => $end,
      'at' => intval(date('U', strtotime($_POST['date']))),
      'hours_invested' => $end - $start,
      'description' => $_POST['description']
    ]);

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
}

?>