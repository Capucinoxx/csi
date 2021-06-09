<?php
namespace App\Internal;
use \PDO;
use App\Utils\DotEnv;

class DataBase {
  protected $db_connection;

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
}
?>