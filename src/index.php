<?php
require_once(dirname(__DIR__).'/src/vendor/autoload.php');
header("Access-Control-Allow-Origin: *");
session_start();

use \App\Constructors\Calendar;
use \App\Constructors\Forms;
use App\Internal\Label;
use App\Internal\Employee;
use App\Internal\Event;
use App\Constructors\Actions;
use App\Internal\Timesheet;
use App\Internal\FiscalYear;

$IEvent = new Event();
$ILabel = new Label();
$IEmployee = new Employee();
$ITimesheet = new Timesheet();

date_default_timezone_set('America/Los_Angeles');

if ($_SERVER["REQUEST_METHOD"] != "GET" || isset($_GET['context'])) {
  (new Actions($IEvent, $IEmployee, $ITimesheet, $ILabel))->execute();
} 
$_SERVER["REQUEST_URI"] = strtok($_SERVER["REQUEST_URI"], '?');

// $_SESSION['loggedin'] = true;
// $_SESSION['error'] = "test alert";

require_once(dirname(__DIR__).'/src/Views/head.html');

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  $forms = new Forms(
    $ILabel->get(),
    $IEvent->getByType(false, $_SESSION['id']),
    $IEmployee->get()
  );
  $calendar = new Calendar($ITimesheet, $forms, $_GET['week'] ?? null, $_GET['year'] ?? null, null);
  require_once('./Views/Calendar.php');
} else {  
  $forms = new Forms([], [], []);
  require_once('./Views/Login.php');
}

if (isset($_SESSION['error'])) {
  echo $forms->draw_alert($_SESSION['error']);  
  unset($_SESSION['error']);
}

echo '<div id="iframe" class="modal"></div>';
// require_once(dirname(__DIR__).'/src/Views/test.php');
require_once(dirname(__DIR__).'/src/Views/footer.html');
?>
