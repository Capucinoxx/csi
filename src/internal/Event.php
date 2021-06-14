<?php
namespace App\Internal;
use App\Internal\Database;
use \PDO;


class Event extends Database {

  public function __construct() {
    parent::__construct();
    $this->table_name = 'events';
  }

}

?>