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

  public function updateEvent($params) {
    $pattern = "/[A-Z]{2}[0-9]{2}[0-9]{4}$/i";
    if(preg_match($pattern, $params['ref']) == 0) {
      return [
        "error" => "La référence du projet ne respecte pas le format."
      ];
    }

    $this->update($params);
  }

  public function createEvent($params) {
    $params['created_at'] = time()*1000;
    $pattern = "/[A-Z]{2}[0-9]{2}[0-9]{4}$/i";
    if(preg_match($pattern, $params['ref']) == 0) {
      return [
        "error" => "La référence du projet ne respecte pas le format."
      ];
    }

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
      SET id_label = 15
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

  public function getHoursInserted($params) {
      $timesheet = new Timesheet();
      $data = $timesheet->getByID($params['id']);

      return $data['hours_invested'];
  }

  public function validateProject($data, $isUpdate) {
    extract($data);

    # Get nombre limite d'heures par jour et par semaine
    $limit_hours = $this->getLimitHours($id_event);

    # Si c'est un update, get le nombre d'heures inserted
    $hours_inserted = 0;
    if($isUpdate) $hours_inserted = $this->getHoursInserted($data); 

    # Get nombre d'heures travaillées par jour et par semaine
    $hours_per_day = $this->getCurrentHoursPerDay($id_event, $id_employee, $at) - $hours_inserted;
    $hours_per_week = $this->getCurrentHoursPerWeek($id_event, $id_employee, $at) - $hours_inserted;

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

  public function validateHours($data, $isUpdate) {
    extract($data);

    $label = new Label();
    $id_label = $label->getIDByEvent($id_event);

    if($id_label == 15) {
      return [
        "error" => "Le libellé de ce projet a été supprimé. Veillez associer ce projet à un autre libellé."
      ];
    }

    if($id_label == 1) {
      // c'est un leave
      $leave = new Leave();
      $response = $leave->validateLeave($data, $isUpdate);

      if(isset($response['error'])) {

        return $response;
      }
    } else {
      // c'est un projet
      $response = $this->validateProject($data, $isUpdate);
      if(isset($response['error'])) {
        
        return $response;
      }
    }
    
    if($start > $end || $hours_invested < 0) {
      return [
        "error" => "Impossible de rentrer les heures : l'heure du début de l'activité est plus grande que l'heure de fin."
      ];  
    }

    if($start == $end) {
      return [
        "error" => "Impossible de rentrer les heures : l'heure du début de l'activité est égale à l'heure de fin."
      ]; 
    }
    
    return true;
  }  

  public function updateRef() {
    # Aller chercher toute es références à modifier
    $sql = "SELECT id, ref FROM events WHERE id_label != 1;";
    $query = $this->db_connection->prepare($sql);
    $query->execute();
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach($data as $row) {
      $array = str_split($row['ref']);
      $newYear = ($array[2] . $array[3]) + 1;
      $newRef = $array[0] . $array[1] . $newYear;

      for($i = 4; $i < count($array); $i++) {
        $newRef = $newRef . $array[$i]; 
      }
      
      $sql = "UPDATE Events SET ref = :newRef WHERE id = :id;";
      $query = $this->db_connection->prepare($sql);
      $query->execute(
        [
          ':newRef' => $newRef,
          ':id' => $row['id']
        ]
      );
    }
  }

}

?>
