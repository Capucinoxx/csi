<?php
namespace App\Internal;
use App\Internal\DataBase;
use App\Internal\Employee;
use App\Internal\FiscalYear;
use \PDO;


class Leave extends DataBase {

  public function __construct() {
    parent::__construct();
    $this->table_name = "Events";
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

  private function getIDLeave($id_event) {
    $sql = "SELECT id_leave FROM Events WHERE id = :id_event";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id' => $id_event
      ]
    );

    $data = $query->fetch(PDO::FETCH_ASSOC);

    return $data['id_leave'];
  }

  private function getCurrentHours($id_event, $id_employee, $id_leave) {
    # Aller chercher le début et la fin de l'année fiscale en cours
    $fiscal_year = new FiscalYear();
    $fiscal_year_data = $fiscal_year->get();

    $sql = "
    SELECT SUM(hours_invested) as total_hours_invested
    FROM Timesheets 
    WHERE id_event = :id_event AND
          id_employee = :id_employee";
                  
    if($id_leave == 4) { // Temps accumulé
      $sql .= ";";
      $query = $this->db_connection->prepare($sql);
      $query->execute(
        [
          ':id_event' => $id_event,
          ':id_employee' => $id_employee
        ]
      );

    } else {
      $sql .= " AND at BETWEEN :start_fiscal_year AND :end_fiscal_year;";
      $query = $this->db_connection->prepare($sql);
      $query->execute(
        [
          ':start_fiscal_year' => $start_fiscal_year,
          ':end_fiscal_year' => $end_fiscal_year
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

  public function validateLeave($data) {
    extract($data);

    # Get ID du leave
    $id_leave = $this->getIDLeave($id_event);

    if($id_leave == 6) { // Congé férié
      # Aucune vérification pour les congés fériés
      return true;
    }

    # Get statut de l'employé
    $employee = new Employee();
    $employee_status = $employee->getEmployeeStatus($id_employee);

    # Nombre d'heures prises à date
    $current_hours = $this->getCurrentHours($id_event, $id_employee, $id_leave);
    
    # Nombre d'heures totales si on tient en compte les nouvelles heures rentrées
    $total_hours = $hours_invested + $current_hours; 

    if ($id_leave == 4) { // Temps accumulé
      # Vérifier que le temps accumulé ne soit pas plus petit que -14 si on lui aditionne le nouveau temps
      if($total_hours < -14){
        return [
          "error" => "Impossible de rentrer les heures : le temps accumulé total ne peut pas dépasser -14 heures."
        ];
      }

    } 
    if($employee_status == 1) { // regular employee
      # Vérifier que ca dépasse pas le nombre d'heures dispo.
      $max_hours = $this->getLeaveMaxHours($id_employee, $id_event);

      if($total_hours > $max_hours) {

        return [
          "error" => "Impossible de rentrer les heures."
        ];
      }
    } 
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
    
    return true;
  }
  
}

?>
