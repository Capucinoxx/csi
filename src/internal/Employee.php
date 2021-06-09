<?php
namespace App\Internal;
use App\Internal\Database;

class Employee extends Database {

  private function getId($username) {

    $sql = "SELECT id FROM employees WHERE username = :username";
    $query = $this->db_connection->prepare($sql);
    $query->execute($username);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    return $row['id'];
  }

  public function createEmployee($params) {
		# Vérifier si l'employé existe déjà
		

    # Création de l'employé
		$username = $this->insert();
		
    # Création des congés de l'employé
    $this->insertLeaves($username);
	}

  public function getEmployee($id) {
    $result = $this->selectOne($id);

    return ($result ? $result : false);
  }

  ##  QUERIES ##
  private function insert($params) {
    # garder temp_passwod??
    $hashed_password = password_hash($params['temp_password'], PASSWORD_DEFAULT);
    $params['temp_password'] = $hashed_password;

    # Insertion de l'employé
    $sql = sprintf(
    "INSERT INTO employees (%s) VALUES (%s)", 
    implode(', ', array_keys($params)),
    ':' . implode(', :', array_keys($params))
    );
    $query = $this->db_connection->prepare($sql);
    $query->execute($params);

		return $params['username'];
  }

  private function selectOne($id) {
    $sql = "
      SELECT 
        id, username, 
        first_name, last_name, 
        role, regular, 
        rate, rate_AMC, 
        rate_CSI, created_at, 
        deleted_at 
      FROM employees 
      WHERE id = {$id};";

    $query = $this->db->prepare($sql);
    $query->execute();

    // if($query->errorCode() == "23000") {
    //   return false;
    // } else {
    //   return $query;
    // }
    return (($query->errorCode() == "23000") ? false : $query);
  }

  private function select($isMany) {
    $sql = "
    SELECT 
      id, username, 
      first_name, last_name, 
      role, regular, 
      rate, rate_AMC, 
      rate_CSI, created_at, 
      deleted_at 
    FROM employees;";

    $query = $this->db->prepare($sql);
    $query->execute();

    // if($query->errorCode() == "23000") {
    //   return "Cet employé n'existe pas.";
    // } else {
    //   return $query;
    // }
    return (($query->errorCode() == "23000") ? false : $query);
  }

  private function insertLeaves($username) {
  	# Id de l'employé créé
    $id = $this->getId($params['username']);

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
}

?>
