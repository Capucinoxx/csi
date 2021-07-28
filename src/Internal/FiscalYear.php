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

  public function get($id) {
    return ($this->select($id))->fetch(PDO::FETCH_ASSOC);
  }

  public function getMatchingFiscalYear($at) {
    return ($this->selectMatchedTime($at))->fetch(PDO::FETCH_ASSOC);
  }

  public function restartYear($params) {
    $this->newFiscalYear($params);

    // $event = new Event();
    // return $event->updateRef();
  }

  private function newFiscalYear($params) {
    if($params['end'] < $params['start']) {
      return [
        "error" => "La date de fin d'année fiscale est plus grande que la date de début d'année fiscale."
      ];
    } 
    $params['created_at'] = time()*1000;
    $params['start'] *= 1000;
    $params['end'] *= 1000;

    $this->insert($params);

    return true;
  }

  ## QUERIES ## 

  private function select($id) {
    $sql = "
    SELECT 
      start as start_fiscal_year, 
      end as end_fiscal_year 
    FROM FiscalYears 
    WHERE id = :id;";
    $query = $this->db_connection->prepare($sql);
    $query->execute(
      [
        ':id' => $id
      ]
    );

    return $query;
  }

  private function selectMatchedTime($at) {
    $sql = "
    SELECT 
      id, 
      start, 
      end, 
      FROM_UNIXTIME(end, '%Y') AS year
    FROM FiscalYears
    WHERE (:at * 1000) BETWEEN start AND end";
    $query = $this->db_connection->prepare($sql);
    $query->execute( 
      [
        ':at' => $at
      ]
    );

    return $query;
  }

}
?>
