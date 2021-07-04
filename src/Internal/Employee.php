<?php
namespace App\Internal;
use App\Internal\DataBase;
use App\Internal\Leave;
use \PDO;


class Employee extends DataBase {

  public function __construct() {
    parent::__construct();
    $this->table_name = 'Employees';
  }

  public function getByID($id) {
    # Retourne le résultat en format dictionnaire
    return ($this->select($id))->fetch(PDO::FETCH_ASSOC);
  }

  public function get() {
    # Retourne le résultat en format dictionnaire
    return ($this->select(false))->fetchAll(PDO::FETCH_ASSOC);
  }

  private function getId($username) {
    $sql = "SELECT id FROM Employees WHERE username = :username";
    $query = $this->db_connection->prepare($sql);
    $query->execute([':username' => $username]);
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
      $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
      $params['created_at'] = time()*1000;
      if(!$this->insert($params)) {
        return [
          "error" => "Erreur lors de la création de l'employé."
        ];
      }
     
      # Création des congés de l'employé
      $this->insertLeaves($params['username']);

      return true;
    }

    return [
      "error" => "Un employé existe déjà avec le nom d'utilisateur {$params['username']}."
    ];
	}

  public function login($params) {
    # Valider que l'employé existe
    if(!$this->exists($params['username'])) {
      return [
        "error" => "Nom d'utilisateur incorrect."
      ];
    }
      
    # Valider que l'employé n'a pas été supprimé
    $isDeleted = $this->deleted($params['username']);

    if(!$isDeleted) {
      # Validation du mot de passe
      if($this->passwordValidation($params)) {
        return $this->userLogged($params['username']);
      }
      
      return false;
    }

    return $isDeleted;
  }

  ##  QUERIES ##
  private function passwordValidation($params) {
    $sql = "SELECT password FROM Employees WHERE username = :username;";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':username' => $params['username']
      ]
    );
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if(!password_verify($params['password'], $row['password'])) {

      return [
        "error" => "Mot de passe incorrect."
      ];
    }

    return true;
  }

  private function userLogged($username) {
    $sql = "
    SELECT 
      id,
      first_name,
      last_name,
      role
    FROM Employees 
    WHERE username = :username;";

    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':username' => $username
      ]
    );

    return $query->fetch(PDO::FETCH_ASSOC);
  }

  private function deleted($username) {
    $sql = "SELECT deleted_at, id FROM Employees WHERE username = :username;";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':username' => $username
      ]
    );

    if($query->rowCount() != 0) {
      $row = $query->fetch(PDO::FETCH_ASSOC);

      if($row['deleted_at'] && (time()*1000)> $row['deleted_at']) {
        return [ 
          "error" => "Cet usager a été supprimé le " . date("Y-m-d", $row['deleted_at']/1000)
        ];
      }
    } 

    if($query->errorCode() == "23000") {
      return [ 
        "error" => "Nom d'utilisateur incorrect."
      ];
    }

    return false;
  }

  private function exists($username) {
    # Vérifier si le username de l'employé existe déjà
    $sql = "
    SELECT id  
    FROM Employees
    WHERE username = :username";
    $query = $this->db_connection->prepare($sql);
    $query->execute([':username' => $username]);

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
    FROM Employees WHERE deleted_at IS NULL";

    if(!$isOne) {
      # Select all employees
      $query = $this->db_connection->prepare($sql);
      $query->execute();
    } else {
      # Select one employee
      $sql .= " AND id = :id;";
      $query = $this->db_connection->prepare($sql);
      $query->execute([':id' => $isOne]);
    }

    return (($query->errorCode() == "23000") ? false : $query);
  }

  public function getEmployeeStatus($id_employee) {
    $sql = "SELECT regular FROM Employees WHERE id = :id;";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id' => $id_employee
      ]
    );

    $data = $query->fetch(PDO::FETCH_ASSOC);

    return $data['regular'];
  }

  private function insertLeaves($username) {
  	# Id de l'employé créé
    $id = $this->getId($username);

    # Insert leaves
    $leaves = new Leave();
    $leaves->createLeaves($id);
  }
}

?>
