<?php
require_once(dirname(__DIR__).'/html/vendor/autoload.php');

// session_start();

use \App\Constructors\Calendar;
use \App\Constructors\Forms;
use App\Internal\Label;
use App\Internal\Employee;
use App\Internal\Event;
use App\Constructors\Actions;

$IEvent = new Event();
$ILabel = new Label();
$IEmployee = new Employee();

$_SESSION['loggedin'] = true;

if ($_SERVER["REQUEST_METHOD"] != "GET") {
  (new Actions())->execute();
}

$calendar = new Calendar($_GET['week'] ?? null, $_GET['year'] ?? null, $projects_week);


require_once(dirname(__DIR__).'/html/Views/head.html');
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  $forms = new Forms(
    $ILabel->get(),
    $IEvent->get(1),
    $IEmployee->get()
  );
  require_once('./Views/Calendar.php');
} else {
  $forms = new Forms([], [], []);
  require_once('./Views/Login.php');
}

require_once(dirname(__DIR__).'/html/Views/footer.html');
?>