<?php
namespace App\Internal;
use App\Internal\Database;
use \PDO;

class Timesheet extends Database {
  public function __construct() {
    parent::__construct();
    $this->table_name = 'timesheets';
  }

  public function get(int $id, int $from, int $to) {
    $result = $this->select($id, $from, $to);
    
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getByID($id) {
    return ($this->selectByID($id))->fetchAll(PDO::FETCH_ASSOC);
  }

  public function createTimesheet($params) {

  }

  ## QUERIES ##
  private function select($id_employee, $from, $to) {
    $sql = "
      SELECT 
        timesheets.id AS id, 
        id_event, 
        timesheets.id_employee, 
        id_label, 
        at, 
        hours_invested, 
        start, 
        end, 
        description, 
        ref, 
        events.title AS event_title,
        employees.created_at AS employee_created_at,
        max_hours_per_day,
        max_hours_per_week,
        max_hours,
        events.deleted_at AS event_deleted_at,
        username,
        first_name,
        last_name,
        role,
        rate_AMC,
        rate_CSI,
        employees.deleted_at AS employee_deleted_at,
        labels.title AS labels_title,
        color,
        events.created_at AS event_created_at
      FROM timesheets
        JOIN events ON (timesheets.id_event = events.id)
        JOIN employees ON (timesheets.id_employee = employees.id)
        JOIN labels ON (events.id_label = labels.id)
      WHERE timesheets.id_employee = :id AND at BETWEEN :from AND :to
        ORDER BY id_employee ASC;
    ";

    $query = $this->db_connection->prepare($sql);
    $query->execute([':id' => $id_employee, ':from' => $from, ':to' => $to]);
    
    return $query;
  }

  private function selectByID($id) {
    $sql = "SELECT * FROM timesheets WHERE id = :id;";
    
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ":id" => $id
      ]
    );

    return (($query->errorCode() == "23000") ? false : $query);
 }

 public function validateInsertion($data) {
  # Validation de l'évènement
  $event = new Event();

  if(!$event->isValid($data['id_event'], $data['at'])) {
    
    return (object) [
      "error" => "Évènement invalide"
    ];
  }

  # Validation de l'employé
  $employee = new Employee();

  if(!$employee->isValid($data['id_employee'], $data['at'])) {
    
    return (object) [
        "error" => "Employé invalide"
    ];
  }

  $response = $event->validateHours($data);
  if($response == true) {
    return $response;
  }

  return false;
}

}

?>