<?php
namespace App\Internal;
use App\Internal\DataBase;
use App\Internal\Employee;
use \PDO;


class Leave extends DataBase {

  public function __construct() {
    parent::__construct();
    $this->table_name = "FiscalYears";
  }

  public function get() {

    return ($this->select())->fetch(PDO::FETCH_ASSOC);
  }

  ## QUERIES ## 

  private function select() {
    $sql = "
    SELECT 
      start as start_fiscal_year, 
      end as end_fiscal_year 
    FROM FiscalYears 
    ORDER BY id DESC 
    LIMIT 1;";
    $query = $this->db_connection->prepare($sql);
    $query->execute();

    return $query;
  }

}
?>
