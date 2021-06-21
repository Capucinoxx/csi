<?php
namespace App\Internal;
use App\Internal\DataBase;
use App\Internal\Employee;
use \PDO;


class Event extends DataBase {

  public function __construct() {
    parent::__construct();
    $this->table_name = 'Events';
  }

  public function get($id_employee) {
    # Retourne le résultat en format dictionnaire
    return ($this->select($id_employee, false))->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getByID($id) {
    # Retourne le résultat en format dictionnaire
    return ($this->select($id, true))->fetchAll(PDO::FETCH_ASSOC);
  }

  public function createEvent($params) {
    $params['created_at'] = time()*1000;
    $result = $this->insert($params);

    if(($result->errorCode() == "23000")) {
    # Retourner une erreur si le libellé n'existe pas

      return [
        "error" => "Ce libellé n'a pas été créé."
      ];
    }

    return true;
  }

  public function deleteEvent($id) {
    $this->delete($id);
  }

  ## QUERIES ##
  private function select($id, $isOne) {
    $sql = "
    SELECT 
      events.id as id_event, 
      id_label,
      id_employee, 
      ref, 
      events.title as title_event, 
      events.created_at,
      events.deleted_at, 
      max_hours_per_day, 
      max_hours_per_week, 
      max_hours, 
      labels.title as title_label, 
      color
    FROM Events events 
    LEFT JOIN Labels labels 
      ON events.id_label = labels.id
    WHERE 
      events.deleted_at IS NULL 
    ";

    if ($isOne) {
      # Select one events
      $sql .= 
      " AND events.id = :id
        ORDER BY labels.id ASC;";
    } else {
      # Select all events
      $sql .= 
      " AND (id_employee IS NULL OR id_employee = :id)
        ORDER BY labels.id ASC;";
    }
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ":id" => $id
      ]
    );
    
    return $query;
  }

  public function eraseEventsLabel($id_label) {
    if($id_label != 1) {
      $sql = "
      UPDATE Events 
      SET id_label = null
      WHERE id_label = :id_label";

      $query = $this->db_connection->prepare($sql);
      $query->execute(
        [
          ":id_label" => $id_label
        ]
      );
    }
  }

  public function getCurrentHoursPerDay($id_project, $id_employee, $at) {
    # Aller chercher le nombre d'heures à date par jour
    $sql = "
    SELECT 
      SUM(hours_invested) as hours_per_day
    FROM Timesheets
    WHERE 
      from_unixtime(at/1000, '%Y, %D, %M') = from_unixtime({$at}/1000, '%Y, %D, %M') 
      AND id_employee = :id_employee
      AND id_event = :id_project;";

    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id_employee' => $id_employee,
        ':id_project' => $id_project
      ]
    );

    $row = $query->fetch(PDO::FETCH_ASSOC);

    if($row != false) {
      return $row['hours_per_day'];
    } 
    
    return 0;
  }

  public function getCurrentHoursPerWeek($id_project, $id_employee, $at) {
    # Aller chercher le nombre d'heures à date cette semaine
    $sql = "
    SELECT 
      SUM(hours_invested) AS hours_per_week 
    FROM Timesheets
    WHERE 
      id_event = :id_project 
      AND id_employee = :id_employee
      AND week(from_unixtime(at/1000)) = week(from_unixtime({$at}/1000));
    ";

    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id_employee' => $id_employee,
        ':id_project' => $id_project
      ]
    );

    if($query->rowCount() == 0) {
      return 0;
    }
    $row = $query->fetch(PDO::FETCH_ASSOC);

    return $row['hours_per_week'];
  }

  public function getLimitHours($id) {
    $sql = "
    SELECT max_hours_per_day, max_hours_per_week 
    FROM Events
    WHERE id = :id;";

    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id' => $id
      ]
    );

    return $query->fetch(PDO::FETCH_ASSOC);
  }

  public function validateProject($data) {
    extract($data);

    # Get nombre limite d'heures par jour et par semaine
    $limit_hours = $this->getLimitHours($id_event);

    # Get nombre d'heures travaillées par jour et par semaine
    $hours_per_day = $this->getCurrentHoursPerDay($id_event, $id_employee, $at);
    $hours_per_week = $this->getCurrentHoursPerWeek($id_event, $id_employee, $at);

    # Vérifier si le total des heures rentrées dépassent la limite d'heures par jour
    if(($hours_invested + $hours_per_day) > $limit_hours['max_hours_per_day']) {
      
      return [
        "error" => "Impossible de rentrer les heures : la limite d'heures par jour a été dépassée."
      ];
    }
    # Vérifier si le total des heures rentrées dépassent la limite d'heures par semaine
    if(($hours_invested + $hours_per_week) > $limit_hours['max_hours_per_week']) {

      return [
        "error" => "Impossible de rentrer les heures : la limite d'heures par semaine a été dépassée."
      ];
    }

    return true;
  }

  public function validateHours($data) {
    extract($data);

    $label = new Label();
    $id_label = $label->getIDByEvent($id_event);

    if($id_label != 1) {
      // c'est un projet
      $response = $this->validateProject($data);
      if(isset($response['error'])) {
        
        return $response;
      }
    } else {
      // c'est un leave
      $leave = new Leave();
      $response = $leave->validateLeave($data);

      if(isset($response['error'])) {

        return $response;
      }
    }
    
    if($start > $end) {
      return [
        "error" => "Impossible de rentrer les heures : l'heure du début de l'activité est plus grande que l'heure de fin."
      ];  
    }
    
    return true;
  }  

}

?>
