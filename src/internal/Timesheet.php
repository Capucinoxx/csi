<?php
namespace App\Internal;
use App\Internal\Database;

class Timesheet extends Database {
  public function __construct() {}

  public function get(int $id, int $from, int $to) {
    $this->queryTimesheet($id, $from, $to);
  }

  private function queryTimesheet(int $id, int $from, int $to) {
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

    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id, ':from' => $from, ':to' => $to]);
    return $stmt;
  }
}

?>