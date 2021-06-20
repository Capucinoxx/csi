<?php
namespace App\Internal;
use App\Internal\Database;
use \PDO;

class Timesheet extends Database {
  public function __construct() {
    parent::__construct();
    $this->table_name = 'Timesheets';
  }

  public function get(int $id, int $from, int $to) {
    $result = $this->select($id, $from, $to);

    if(!$result && isset($result['error']))
      return $result;
    
      return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getByID($id) {
    return ($this->selectByID($id))->fetchAll(PDO::FETCH_ASSOC);
  }

  ## QUERIES ##
  private function select(int $id_employee, int $from, int $to) {
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
      FROM Timesheets timesheets
        JOIN Events events ON (timesheets.id_event = events.id)
        JOIN Employees employees ON (timesheets.id_employee = employees.id)
        JOIN Labels labels ON (events.id_label = labels.id)
      WHERE timesheets.id_employee = :id AND at BETWEEN :from AND :to
        ORDER BY id_employee ASC;
    ";

    $query = $this->db_connection->prepare($sql);
    $query->execute([':id' => $id_employee, ':from' => $from, ':to' => $to]);
    
    if($query->rowCount() != 0) {
      return $query;
    } 
    return (object) [
      "error" => "Erreur lors de la création de la carte."
    ];
  }

  private function selectByID($id) {
    $sql = "SELECT * FROM Timesheets WHERE id = :id;";
    
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ":id" => $id
      ]
    );

    return (($query->errorCode() == "23000") ? false : $query);
 }

}

?>