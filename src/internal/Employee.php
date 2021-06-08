<?php
namespace App\Internal;
use App\Internal\Database;

class Employee {

  private function getId($username) {

    $sql = "SELECT id FROM employees WHERE username = :username";
    $query = $this->db->prepare($sql);
    $query->execute($username);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    return $row['id'];
  }

  public function createEmployee($params) {
		# Vérifier si l'employé existe déjà
		

    # Création de l'employé
		$username = $this->insert();
		
    # Création des congés de l'employé
    $this->createLeaves($username);
	}

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
    $query = $this->db->prepare($sql);
    $query->execute($params);

		return $params['username'];
  }

  private function createLeaves($username) {
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
           (:id, 1, 6, 'Congé férié', 0, :date);";

    $query = $this->db->prepare($sql);
    $query->execute(['id' => $id, 'date' => $date]);
  } 
}

?>
