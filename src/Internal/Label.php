<?php
namespace App\Internal;
use App\Internal\DataBase;
use App\Internal\Event;
use \PDO;


class Label extends DataBase {

  public function __construct() {
    parent::__construct();
    $this->table_name = 'Labels';
  }

  public function get() {
    # Retourne le résultat en format dictionnaire
    return ($this->select())->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getIDByEvent($id_event) {
    $sql = "SELECT id_label FROM Events WHERE id = :id";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ":id" => $id_event
      ]
    );
    $row = $query->fetch(PDO::FETCH_ASSOC);
    
    return intval($row['id_label']);
  }

  public function createLabel($params) {
    # Vérifier si le label existe déjà
    if(!$this->exists($params['title'])) {
      # Création du label
      $params['created_at'] = time()*1000;
      $this->insert($params);

      return true;
    }

    return [
      "error" => "Un libellé existe déjà avec le titre {$params['title']}."
    ];
  }

  public function deleteLabel($id) {
    if($id != 1) {
      # Suppression du labels
      $this->delete($id);
      
      # Suppression des events associés au label
      $event = new Event();
      $event->eraseEventsLabel($id);

      return true;
    }

    return [
      "error" => "Ce libellé ne peut pas être supprimé."
    ];
  }

  ## QUERIES ##
  private function select() {
    $sql = "SELECT * FROM Labels WHERE deleted_at IS NULL;";

    $query = $this->db_connection->prepare($sql);
    $query->execute();

    return $query;
  }

  private function exists($title) {
    # Vérifier si le titre du label existe déjà
    $sql = "
    SELECT id  
    FROM Labels
    WHERE title = :title";
    $query = $this->db_connection->prepare($sql);
    $query->execute([':title' => $title]);

    return ($query->rowCount() > 0);
  }

  public function getByID($id) {
    $sql = "
    SELECT *  
    FROM Labels
    WHERE id = :id";

    $query = $this->db_connection->prepare($sql);
    $query->execute([':id' => $id]);

    return $query->fetch(PDO::FETCH_ASSOC);
  }

}

?>
