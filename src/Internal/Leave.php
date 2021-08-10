<?php
namespace App\Internal;
use App\Internal\DataBase;
use App\Internal\Employee;
use App\Internal\FiscalYear;
use App\Internal\Timesheet;
use \PDO;


class Leave extends DataBase {

  public function __construct() {
    parent::__construct();
    $this->table_name ="Events";
  }

  public function createLeaves($id) {
    $this->insert($id);
  }

  protected function insert($id) {
      
    # Date de création en unixtimestamp
    $date = time()*1000;

    # Insertion des congés
    $sql = "
      INSERT INTO Events(id_employee, id_label, id_leave, title, max_hours, created_at)
      VALUES (:id, 1, 1, 'Congé mobile', 0, :date),
            (:id, 1, 2, 'Heures maladie', 0, :date),
            (:id, 1, 3, 'Congé parental', 0, :date),
            (:id, 1, 4, 'Temps accumulé', 0, :date),
            (:id, 1, 5, 'Vacances', 0, :date),
            (:id, 1, 6, 'Congé férié', 0, :date);
    ";

    $query = $this->db_connection->prepare($sql);
    $query->execute([':id' => $id, ':date' => $date]);
  } 

  public function deleteByEmployeesID($id) {
    $sql = "
    UPDATE Events 
    SET deleted_at = :deleted_at
    WHERE id_employee = :id_employee
    ";

    $query = $this->db_connection->prepare($sql);
    return $query->execute(
      [
        ':deleted_at' => (time()*1000),
        ':id_employee' => $id
      ]
    );
  }

  public function get($id_employee) {
    $sql = "
    SELECT 
      events.id as id_event, 
      id_employee, 
      events.title as title_event, 
      events.created_at,
      events.deleted_at, 
      id_leave,
      max_hours
    FROM Events events
    LEFT JOIN Labels labels
      ON events.id_label = labels.id
    WHERE 
      events.deleted_at IS NULL AND
      events.id_employee = :id_employee;
    ";

    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ":id_employee" => $id_employee
      ]
    );
    
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  private function getIDLeave($id_event) {
    $sql = "SELECT id_leave FROM Events WHERE id = :id_event";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id_event' => $id_event
      ]
    );

    $data = $query->fetch(PDO::FETCH_ASSOC);

    return $data['id_leave'];
  }

  public function getRemainingHours($id_employee, $at) {

    $leaves_array = $this->get($id_employee);
    foreach($leaves_array as $leave) {
      $hours_left = '-';
      
      $leave_taken =  floatval($this->getCurrentHours($leave['id_event'], $id_employee, $leave['id_leave'], $at));
      if($leave['id_leave'] != 4 && $leave['id_leave'] != 6) 
        $hours_left = floatval($leave['max_hours']) - $leave_taken;

      $total_hours[$leave['title_event']] = [
        'Heures prises' => $leave_taken,
        'Heures restantes' => $hours_left
      ];
    }

    return $total_hours;
  }

  private function getCurrentHours($id_event, $id_employee, $id_leave, $at) {
    # Aller chercher le début et la fin de l'année fiscale en cours
    $fiscal_year = new FiscalYear();
    $fiscal_year_data = $fiscal_year->getMatchingFiscalYear($at);

    $sql = "
    SELECT COALESCE(SUM(hours_invested), 0) as total_hours_invested
    FROM Timesheets 
    WHERE id_event = :id_event AND
          id_employee = :id_employee AND
          deleted_at IS NULL";
                  
    if($id_leave == 4) { // Temps accumulé
      $sql .= ";";
      $query = $this->db_connection->prepare($sql);
      $query->execute(
        [
          ':id_event' => $id_event,
          ':id_employee' => $id_employee,
        ]
      );
      // return 0;
    } else {
      $sql .= " AND at BETWEEN :start_fiscal_year AND :end_fiscal_year;";
      $query = $this->db_connection->prepare($sql);
      $query->execute(
        [
          ':id_event' => $id_event,
          ':id_employee' => $id_employee,
          ':start_fiscal_year' => $fiscal_year_data['start'],
          ':end_fiscal_year' => $fiscal_year_data['end']
        ]
      );
    }

    if($query->rowCount() == 0) {
      return 0;
    }
    $row = $query->fetch(PDO::FETCH_ASSOC);

    return $row['total_hours_invested'];
  }

  private function getLeaveMaxHours($id_employee, $id_event) {
    # Pour un employé régulier 
    $sql = "SELECT max_hours FROM Events WHERE id_employee = :id_employee AND id = :id_event;";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id_employee' => $id_employee,
        ':id_event' => $id_event
      ]
    );
    $data = $query->fetch(PDO::FETCH_ASSOC);

    return $data['max_hours'];
  }

  private function getRegularLeaveMaxHours($id_leave) {
    # Pour un employé au prorata
    $sql = "SELECT hours FROM RegularLeave WHERE id = :id;";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id' => $id_leave
      ]
    );
    $data = $query->fetch(PDO::FETCH_ASSOC);

    return $data['hours'];
  }

  private function getWeeksWorked($id_employee) {
    $sql = "
    SELECT 
      week(from_unixtime(created_at/1000)) as start_week, 
      week(CURRENT_DATE()) AS current_week 
    FROM Employees 
    WHERE id = :id;";
  
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id' => $id_employee
      ]
    );
    
    $data = $query->fetch(PDO::FETCH_ASSOC);

    $diff_weeks = ($data['current_week'] - 1) - $data['start_week']; 

    if($diff_weeks < 0) {
      return 52 + $diff_weeks;
    }
    else {
      return $diff_weeks;
    }
  }

  public function getHoursInserted($params) {
    $timesheet = new Timesheet();
    $data = $timesheet->getByID($params['id']);

    return $data['hours_invested'];
  }

  public function validateLeave($data, $isUpdate) {
    extract($data);
    
    # Get ID du leave
    $id_leave = $this->getIDLeave($id_event);

    if($id_leave == 6) { // Congé férié
      # Aucune vérification pour les congés fériés
      return true;
    }

    # Nombre d'heures prises à date
    $current_hours = $this->getCurrentHours($id_event, $id_employee, $id_leave, $data['at']);
    
    # Si c'est un update, get le nombre d'heures inserted
    $hours_inserted = 0;
    if($isUpdate) $hours_inserted = $this->getHoursInserted($data); 

    # Nombre d'heures totales si on tient en compte les nouvelles heures rentrées
    $total_hours = $hours_invested + $current_hours - $hours_inserted; 
    
    if ($id_leave == 4) { // Temps accumulé
      # Vérifier que le temps accumulé ne soit pas plus petit que -14 si on lui aditionne le nouveau temps
      if($total_hours < -14){
        return [
          "error" => "Impossible de rentrer les heures : le temps accumulé total ne peut pas dépasser -14 heures."
        ];
      }
      return true;
    } 

    # Get statut de l'employé
    $employee = new Employee();
    $employee_status = $employee->getEmployeeStatus($id_employee);
    
    if($employee_status == 1) { // regular employee
      # Vérifier que ca dépasse pas le nombre d'heures dispo.
      $max_hours = floatval($this->getLeaveMaxHours($id_employee, $id_event));

      if($total_hours > $max_hours) {
        return [
          "error" => "Impossible de rentrer les heures."
        ];
      }

    } else {
      # Au prorata employee
      # Calculer le nombre d'heures dispo et vérifier si la somme des heures courantes et les heures rentrées les dépassent 
      $max_hours = $this->getRegularLeaveMaxHours($id_leave);
      $weeks_worked = $this->getWeeksWorked($id_employee);
      $hours_permitted = ($max_hours/52) * $weeks_worked;
      
      if($total_hours > $hours_permitted) {
        return [
          "error" => "Impossible de rentrer les heures. Les heures permises sont : " . $hours_permitted . ".\n"
        ]; 
      }
    }
  
    return true;
  }
  
}

?>
