<?php
namespace App\Internal;
use App\Internal\DataBase;
use App\Internal\Employee;
use \PDO;


class FiscalYear extends DataBase {

  public function __construct() {
    parent::__construct();
    $this->table_name = "FiscalYears";
  }

  public function get() {
    return ($this->select())->fetch(PDO::FETCH_ASSOC);
  }

  public function restartYear($params) {
    $this->newFiscalYear($params);

    $event = new Event();
    return $event->updateRef();
  }

  public function newFiscalYear($params) {
    if($params['end'] < $params['start']) {
      return [
        "error" => "La date de fin d'année fiscale est plus grande que la date de début d'année fiscale."
      ];
    } 
    $params['created_at'] = time()*1000;
    $this->insert($params);

    return true;
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
