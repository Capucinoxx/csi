<?php
require_once(dirname(__DIR__).'/src/vendor/autoload.php');

session_start();

use \App\Constructors\Calendar;
use \App\Constructors\Forms;
use App\Internal\Label;
use App\Internal\Employee;
use App\Internal\Event;
use App\Constructors\Actions;
use App\Internal\Timesheet;

$IEvent = new Event();
$ILabel = new Label();
$IEmployee = new Employee();
$ITimesheet = new Timesheet();

if ($_SERVER["REQUEST_METHOD"] != "GET") {
  (new Actions($IEvent, $IEmployee, $ITimesheet))->execute();
}

// $_SESSION['loggedin'] = true;
// $_SESSION['error'] = "test alert";

require_once(dirname(__DIR__).'/src/Views/head.html');
var_dump($ITimesheet->get($_SESSION['id']));
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  $forms = new Forms(
    $ILabel->get(),
    $IEvent->get($_SESSION['id']),
    $IEmployee->get()
  );
  $calendar = new Calendar($forms, $_GET['week'] ?? null, $_GET['year'] ?? null, null);
  require_once('./Views/Calendar.php');
} else {  
  $forms = new Forms([], [], []);
  require_once('./Views/Login.php');
}

if (isset($_SESSION['error'])) {
  echo $forms->draw_alert($_SESSION['error']);  
}




require_once(dirname(__DIR__).'/src/Views/footer.html');
?>
