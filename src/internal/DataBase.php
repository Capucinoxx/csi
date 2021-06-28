<?php
namespace App\Internal;
use \PDO;
use App\Utils\DotEnv;

class DataBase {
  protected $db_connection;
  protected $table_name;

  public function __construct() {

    try {
      (new DotEnv(__DIR__ . '/../.env'))->load();
      
      $this->db_connection = new PDO (
        "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
          $_ENV['DB_USERNAME'],
          $_ENV['DB_PASSWORD']
      );
      $this->db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $sql = "SET NAMES 'utf8';";

      $query = $this->db_connection->prepare($sql);
      $query->execute();

    } catch(Exception $e) {
      echo "Connection failed: " . $e -> getMessage();
    }
  }

  public function getDBConnection() {
    return $this->db_connection;
  }

  ## QUERIES ##
  protected function delete($id) {
    $sql = "
    UPDATE {$this->table_name} 
    SET deleted_at = :deleted_at
    WHERE id = :id";

    $query = $this->db_connection->prepare($sql);
    return $query->execute(
      [
        ':id' => $id,
        ':deleted_at' => (time()*1000)
      ]
    );
  }
  
  public function update($params) {
    end($params);
    $last_key = key($params);

    $sql = "UPDATE {$this->table_name} SET ";
    foreach ($params as $key => $value) {
      $sql .= sprintf("%s = '%s'", $key,  $value);

      if($last_key != $key) 
        $sql .= ", ";
    }
    $sql .= " WHERE id = :id;"; 

    $query = $this->db_connection->prepare($sql);
    return $query->execute(
      [
        ":id" => $params['id']
      ]
    );
  }

  protected function insert($params) {

    $sql = sprintf(
      "INSERT INTO {$this->table_name} (%s) VALUES (%s)", 
      implode(', ', array_keys($params)),
      ':' . implode(', :', array_keys($params))
    );

    $query = $this->db_connection->prepare($sql);
    $query->execute($params);

    return $query;
  }

  protected function isValid($id, $at) {
    $sql = "SELECT deleted_at FROM {$this->table_name} WHERE id = :id";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id' => $id
      ]
    );

    if($query->rowCount() == 0) {
      # Aucune donnée donc l'objet n'a pas été créé
      return false;
    } 

    $row = $query->fetch(PDO::FETCH_ASSOC);

    if($row['deleted_at'] && $at > $row['deleted_at']) {
      # L'évènement existe, mais il a été supprimé
      return false;
    }

    return true;
  }

}
?>
