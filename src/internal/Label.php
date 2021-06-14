<?php
namespace App\Internal;
use App\Internal\Database;
use \PDO;


class Label extends Database {

  public function __construct() {
    parent::__construct();
    $this->table_name = 'labels';
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
    FROM labels
    WHERE title = :title";
    $query = $this->db_connection->prepare($sql);
    $query->execute([':title' => $title]);

    return ($query->rowCount() > 0);
  }

  private function insert($params) {
    $sql = sprintf(
      "INSERT INTO labels (%s) VALUES (%s)" , 
      implode(', ', array_keys($params)),
      ':' . implode(', :', array_keys($params))
    );
    $query = $this->db_connection->prepare($sql);
    $query->execute($params);
  }

}

?>
