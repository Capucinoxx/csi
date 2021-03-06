<?php
namespace App\Internal;
use App\Internal\DataBase;
use App\Internal\Employee;
use App\Internal\Event;
use \PDO;

class Timesheet extends DataBase {
  public function __construct() {
    parent::__construct();
    $this->table_name = 'Timesheets';
  }

  public function get(int $id, int $from, int $to) {
    $result = $this->select($id, $from, $to);
    
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getByID($id) {
    return ($this->selectByID($id))->fetch(PDO::FETCH_ASSOC);
  }

  public function deleteTimesheet($id) {
    // $timesheet = $this->getByID($id);
    // $sql = "
    // SELECT id_leave, max_hours
    // FROM Events
    // WHERE id_event = :id_event";

    
    // $query = $this->db_connection->prepare($sql);
    // $query->execute(
    //   [
    //     ':id_event' => $timesheet['id_event']
    //   ]
    // );
    // $event_data = $query->fetch(PDO::FETCH_ASSOC);

    // if($event_data['id_leave'] == 4) {
    //   $data_to_update = [
    //     'id' => $timesheet['id_event'],
    //     'max_hours' => 
    //   ];
    // }

    $this->delete($id);
  }

  public function createTimesheet($params) {
    $params['at'] *= 1000;
    $response = $this->validateInsertion($params, false);

    if(isset($response['error'])) {
      return $response;
    }
    
    $this->insert($params);
    return true;
  }

  public function updateTimesheet($params) {
    $params['at'] *= 1000;

    $response = $this->validateInsertion($params, true);

    if(isset($response['error'])) {
      return $response;
    }
    
    $this->update($params);
    return true;
  }

  public function print($data) {
    $fh = fopen(dirname(__FILE__).'/pdfContent/timesheet.html', 'w'); 
    ob_start();
    include dirname(__FILE__).'/pdfContent/timesheetHtmlStr.php';
    $content = ob_get_clean();
    fwrite($fh, $content);
    fclose($fh);
    
    return $content;
  }

  ## QUERIES ##

  public function getEventsInfo($data) {
    $sql = "
    SELECT 
      id_event, 
      COALESCE(ref, '0') AS ref, 
      events.title, 
      amc, 
      id_label, 
      labels.title as title_label
    FROM Timesheets 
    LEFT JOIN Events events ON id_event = events.id 
    LEFT JOIN Labels labels ON id_label = labels.id
    WHERE 
      Timesheets.id_employee = :id_employee AND
      at BETWEEN UNIX_TIMESTAMP(:from)*1000 AND UNIX_TIMESTAMP(:to)*1000
      AND Timesheets.deleted_at IS NULL
    GROUP BY id_event
    ORDER BY id_event ASC;";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id_employee' => $data['id_employee'],
        ':from' => $data['from'],
        ':to' => $data['to']
      ]
    );

    return $query;
  }

  public function getHoursData($data, $id_event) {
    $sql = "
    SELECT 
      DAY(FROM_UNIXTIME(at/1000, '%Y-%m-%d')) as day, 
      SUM(hours_invested) as hours, 
      description 
    FROM Timesheets
    WHERE id_employee = :id_employee AND 
          deleted_at IS NULL AND
          at BETWEEN UNIX_TIMESTAMP(:from)*1000 AND 
          UNIX_TIMESTAMP(:to)*1000 AND 
          id_event = :id_event
    GROUP BY FROM_UNIXTIME(at/1000, '%Y-%m-%d')
    ORDER BY at ASC;";

    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id_employee' => $data['id_employee'],
        ':id_event' => $id_event,
        ':from' => $data['from'],
        ':to' => $data['to']
      ]
    );

    return $query;
  }

  public function getTotalHours($at, $id_employee) {
    $sql = "
    SELECT SUM(hours_invested) as total_hours 
    FROM Timesheets
    WHERE FROM_UNIXTIME(at/1000, '%Y-%m-%d') = :at AND 
          id_employee = :id_employee AND 
          deleted_at IS NULL;";

    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id_employee' => $id_employee,
        ':at' => $at,
      ]
    );

    return $query;
  }

  public function getAMCHours($at, $id_employee, $id_label) {
    $sql = "
      SELECT SUM(hours_invested) as hours FROM Timesheets
        LEFT JOIN events ON events.id = id_event
        LEFT JOIN labels ON labels.id = id_label
          WHERE FROM_UNIXTIME(at/1000, '%Y-%m-%d') = :at AND 
                timesheets.id_employee = :id_employee AND
                id_label = :id_label;";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':at' => $at,
        ':id_employee' => $id_employee,
        ':id_label' => $id_label
      ]
    );

    return $query;
  }

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
      FROM Timesheets timesheets
        LEFT JOIN Events events ON (timesheets.id_event = events.id)
        LEFT JOIN Employees employees ON (timesheets.id_employee = employees.id)
        LEFT JOIN Labels labels ON (events.id_label = labels.id)
      WHERE timesheets.id_employee = :id AND 
            at BETWEEN (:from)*1000 AND (:to)*1000 AND
            timesheets.deleted_at IS NULL
        ORDER BY id_employee ASC;
    ";

    $query = $this->db_connection->prepare($sql);
    $query->execute([':id' => $id_employee, ':from' => $from, ':to' => $to]);
    
    return $query;
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

  public function validateInsertion($data, $isUpdate) {
    # Validation de l'??v??nement
    $event = new Event();

    if(!$event->isValid($data['id_event'], $data['at'])) {
      
      return [
        "error" => "??v??nement invalide"
      ];
    }

    # Validation de l'employ??
    $employee = new Employee();

    if(!$employee->isValid($data['id_employee'], $data['at'])) {
      
      return [
          "error" => "Employ?? invalide"
      ];
    }

    $response = $event->validateHours($data, $isUpdate);
    if(isset($response['error'])) {
      return $response;
    }

    return true;
  }

}

?>
