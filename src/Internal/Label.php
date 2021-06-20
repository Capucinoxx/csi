<?php
namespace App\Internal;
use App\Internal\Database;
use App\Internal\Event;
use \PDO;


class Label extends Database {

  public function __construct() {
    parent::__construct();
    $this->table_name = 'Labels';
  }

  public function get() {
    # Retourne le résultat en format dictionnaire
    return ($this->select())->fetchAll(PDO::FETCH_ASSOC);
  }

  public function createLabel($params) {
    # Vérifier si le label existe déjà
    if(!$this->exists($params['title'])) {
      # Création du label
      $this->insert($params);

      return true;
    }

    return (object) [
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

    return (object) [
      "error" => "Ce libellé ne peut pas être supprimé."
    ];
  }

  ## QUERIES ##
  private function select() {
    $sql = "SELECT * FROM labels;";

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

}

?>
