<?php
namespace App\Internal;
use \PDO;

class DataBase {
  private PDO $db_connection;

  public function make() {
    try {
      $db_connection = new PDO (
            "{$_ENV['DB_DRIVER']}:host={$_ENV['host']};dbname={$_ENV['DB_NAME']}",
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
      );

      $sql = "SET NAMES 'utf8';";

      $query = $db_connection->prepare($sql);
      $query->execute();

    } catch(Exception $e) {
        dd($e->getMessage());
    }
  }

  public function getConnection() {
    return $this->db_connection;
  }
}
?>