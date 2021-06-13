<?php
namespace App\Internal;
use App\Internal\Database;
use App\Internal\Leave;
use \PDO;


class Employee extends Database {

  public function __construct() {
    parent::__construct();
    $this->table_name = 'employees';
  }

  public function getByID($id) {
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

  public function deleteEmployee($id) {
    # Suppression de l'employé
    $this->delete($id);

    # Suppression des congés de l'employé
    $leaves = new Leave();
    $leaves->deleteByEmployeesID($id);
  }

  public function createEmployee($params) {
		# Vérifier si l'employé existe déjà
		if(!$this->exists($params['username'])) {
      # Création de l'employé
      $username = $this->insert($params);
      
      # Création des congés de l'employé
      $this->insertLeaves($username);

      return true;
    }

    return (object) [
      "error" => "Un employé existe déjà avec le nom d'utilisateur {$params['username']}."];
	}

  public function getEmployee($id) {
    $result = $this->selectOne($id);

    return ($result ? $result : false);
  }

  ##  QUERIES ##

  private function exists($username) {
    # Vérifier si le username de l'employé existe déjà
    $sql = "
    SELECT id  
    FROM employees
    WHERE username = :username";
    $query = $this->db_connection->prepare($sql);
    $query->execute(['username' => $username]);

    return ($query->rowCount() > 0);
  }

  private function select($isOne) {
    $sql = "
    SELECT 
      id, username, 
      first_name, last_name, 
      role, regular, 
      rate, rate_AMC, 
      rate_CSI, created_at, 
      deleted_at 
    FROM employees WHERE deleted_at IS NULL";

    if(!$isOne) {
      $query = $this->db_connection->prepare($sql);
      $query->execute();
    } else {
      $sql .= " AND id = :id;";
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

    # Insert leaves
    $leaves = new Leave();
    $leaves->insert($id);
  }
}

?>
