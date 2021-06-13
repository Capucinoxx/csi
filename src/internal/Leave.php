<?php
namespace App\Internal;
use App\Internal\Database;
use \PDO;


class Leave extends Database {

  public function __construct() {
    parent::__construct();
    $this->table_name ="events";
  }

  public function insert($id) {
      
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
    $query->execute(['id' => $id, 'date' => $date]);
  } 

  public function deleteByEmployeesID($id) {
    $sql = "
    UPDATE events 
    SET deleted_at = :deleted_at
    WHERE id_employee = :id_employee";
    var_dump($sql);
    $query = $this->db_connection->prepare($sql);
    return $query->execute(
      [
        'deleted_at' => (time()*1000),
        'id_employee' => $id
      ]
    );
  }
  
}

?>
