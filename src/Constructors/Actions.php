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
        $this->execute_post();
      break;

      case "PUT":
        $this->execute_put();
      break;

      case "DELETE":
        $this->execute_delete();
      break;
    }
  }

  private function execute_post() {
    if (isset($_POST["context"])) {
      $this->{$_POST["context"]}();
    }
  }

  private function execute_put() {
    if (isset($_POST["context"])) {
      $this->{$_POST["context"]}();
    }
  }

  private function execute_delete() {
    if (isset($_POST["context"])) {
      $this->{$_POST["context"]}();
    }
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

    ($this->ITimesheet)->createTimesheet([
      'id_event' => $_POST['id_event'],
      'id_employee' => $_SESSION['id'],
      'start' => $start,
      'end' => $end,
      'at' => intval(date('U', strtotime($_POST['date']))),
      'hours_invested' => $end - $start,
      'description' => $_POST['description']
    ]);
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