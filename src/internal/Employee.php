<?php
namespace App\Internal;
use App\Internal\Database;
use \PDO;


class Employee extends Database {

  public function getById($id) {
    # Retourne le résultat en format dictionnaire
    return ($this->select($id))->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get() {
    # Retourne le résultat en format dictionnaire
    return ($this->select(false))->fetchAll(PDO::FETCH_ASSOC);
  }


  private function getId($username) {

    $sql = "SELECT id FROM employees WHERE username = :username";
    $query = $this->db_connection->prepare($sql);
    $query->execute(['username' => $username]);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    return $row['id'];
  }

  public function createEmployee($params) {
		# Vérifier si l'employé existe déjà
		

    # Création de l'employé
		$username = $this->insert($params);
		
    # Création des congés de l'employé
    $this->insertLeaves($username);
	}

  public function getEmployee($id) {
    $result = $this->selectOne($id);

    return ($result ? $result : false);
  }

  ##  QUERIES ##

  private function select($isOne) {
    $sql = "
    SELECT 
      id, username, 
      first_name, last_name, 
      role, regular, 
      rate, rate_AMC, 
      rate_CSI, created_at, 
      deleted_at 
    FROM employees";

    if(!$isOne) {
      $query = $this->db_connection->prepare($sql);
      $query->execute();
    } else {
      $sql .= " WHERE id = :id;";
      $query = $this->db_connection->prepare($sql);
      $query->execute(['id' => $isOne]);
    }

    return (($query->errorCode() == "23000") ? false : $query);
  }

  private function insert($params) {
    # Hashage du mot de passe
    $hashed_password = password_hash($params['password'], PASSWORD_DEFAULT);
    $params['password'] = $hashed_password;

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

  private function insertLeaves($username) {
  	# Id de l'employé créé
    $id = $this->getId($username);

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
