<?php
namespace App\Internal;
use App\Internal\Database;
use App\Internal\Employee;
use \PDO;


class Event extends Database {

  public function __construct() {
    parent::__construct();
    $this->table_name = 'events';
  }

  public function get($id_employee) {
    # Retourne le résultat en format dictionnaire
    return ($this->select($id_employee, false))->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getByID($id) {
    # Retourne le résultat en format dictionnaire
    return ($this->select($id, true))->fetchAll(PDO::FETCH_ASSOC);
  }

  public function createEvent($params) {
    $result = $this->insert($params);

    if(($query->errorCode() == "23000")) {
    # Retourner une erreur si le libellé n'existe pas

      return (object) [
        "error" => "Ce libellé n'a pas été créé."
      ];
    }

    return true;
  }

  ## QUERIES ##
  private function select($id, $isOne) {
    $sql = "
    SELECT 
      events.id as id_event, 
      id_label,
      id_employee, 
      ref, 
      events.title as title_event, 
      events.created_at,
      events.deleted_at, 
      max_hours_per_day, 
      max_hours_per_week, 
      max_hours, 
      labels.title as title_label, 
      color
    FROM events 
    JOIN labels 
      ON events.id_label = labels.id
    WHERE 
      events.deleted_at IS NULL 
    ";

    if ($isOne) {
      # Select one events
      $sql .= 
      " AND events.id = :id
        ORDER BY labels.id ASC;";
    } else {
      # Select all events
      $sql .= 
      " AND (id_employee IS NULL OR id_employee = :id)
        ORDER BY labels.id ASC;";
    }
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ":id" => $id
      ]
    );
    
    return $query;
  }

}

?>
