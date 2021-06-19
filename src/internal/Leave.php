<?php
namespace App\Internal;
use App\Internal\Database;
use \PDO;


class Leave extends Database {

  public function __construct() {
    parent::__construct();
    $this->table_name ="events";
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
    UPDATE events 
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

  public function getIDLeave($id_event) {
    $sql = "SELECT id_leave FROM events WHERE id = :id_event";
    $query = $this->db->prepare($sql);
    $query->execute(
      [
        ':id' => $id_event
      ]
    );

    $data = $query->fetch(PDO::FETCH_ASSOC);

    return $data['id_leave'];
  }

  public function validateLeave($data) {
    extract($data);

    $id_leave = $this->getIdLeave($id_event);
    $employee_status = $this->getEmployeeStatus($id_employee);
    $current_hours = $this->getCurrentHours($id_event, $id_employee, $id_leave);
    $total_hours = $hours_invested + $current_hours; // heures totales si on rentre les nouvelles heures


    if($id_leave == 6) {
      return false;
    }

    if ($id_leave == 4) { // temps accumulé
      // vérifier que le temps accumulé ne soit pas plus petit que -14 si on lui aditionne le nouveau temps
      $total_hours = $current_hours + $hours_invested;
      if($total_hours < -14){
        return "Impossible de rentrer les heures : le temps accumulé total ne peut pas dépasser -14 heures.";
      }
    } else if($employee_status == 1) {
      // regular employee
      // vérifier que ca dépasse pas le nombre d'heures dispo.
      $max_hours = $this->getLeaveMaxHours($id_employee, $id_event);
      if($total_hours > $max_hours) {
        return "Impossible de rentrer les heures.";
      }
    } else {
      // au prorata employee
      // calculer le nombre d'heures dispo et vérifier si la somme des heures courantes et les heures rentrées les dépassent 
      $max_hours = $this->getRegularLeaveMaxHours($id_leave);
      $weeks_worked = $this->getWeeksWorked($id_employee);
      $hours_permitted = ($max_hours/52) * $weeks_worked;
      
      if($total_hours > $hours_permitted) {
        return "Impossible de rentrer les heures. Les heures permises sont : " . $hours_permitted . ".\n";
      }
    }
  }
  
}

?>
