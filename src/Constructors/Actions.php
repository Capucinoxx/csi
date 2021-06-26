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

    var_dump($rep);

    // on store les informations dans les variables de sessions
  }

  /**
   * déconnection l'utilisateur de sa session
   */
  private function disconnect() {
    // // unset all of the session variables
    $_SESSION = array();
  }

  /**
   * ajoute un évennement pour l'utilisateur courant
   */
  private function addTimesheet() {

  }
}

?>