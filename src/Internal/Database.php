<?php
namespace App\Internal;
use \PDO;

class Database {
  public function __construct() {
    $this->db = App::get('db')->getConnection();
  }
}