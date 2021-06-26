<?php
use App\Internal\Employee;
use App\Internal\Label;
use App\Internal\Event;
use App\Internal\Timesheet;

$employee = new Employee();

## Tests get employees (one and all) ##
// var_dump($employee->getByID(10));
// var_dump($employee->get());

## Tests employees creation ##

$employee_info = [
    'username' => 'test123456101',
    'first_name' => 'Robert',
    'last_name' => 'Masson',
    'password' => '1234',
    'role' => 0,
    'regular' => true,
    'rate' => 45.48,
    'rate_AMC' => 44.25,
    'rate_CSI' => 46.25,
    'created_at' => 1474848000000,
    'deleted_at' => null
];
// var_dump($employee_info);
// var_dump($employee->createEmployee($employee_info));



## Tests suppression de l'employé ##

// $employee->deleteEmployee(11);
// var_dump($employee->getByID(11));


## Tests login ##
/*
var_dump($employee->getByID(9));
$employee_login = [
    'username' => 'test12345',
    'password' => '1234'
];
var_dump($employee->login($employee_login));
*/

## Tests update employee ##
/*
$employee_update= [
    'id' => '9',
    'first_name' => 'Robert',
    'last_name' => 'Fortin',
];
$employee->update($employee_update);
var_dump($employee->getByID(9));

*/

$label = new Label();

## Tests get labels ##
// var_dump($label->get());

## Test labels crearion ##
/*
$label_info = [
    "title" => "Test",
    "color" => "#FFFFF",
    "amc" => 1
];

var_dump($label->createLabel($label_info));
*/

## Test update labels ##
/*
$label_update = [
    "id" => 12,
    "title" => "Test",
    "color" => "#00000"
];
$label->update($label_update);
*/

## Test suppression des labels ##
// $label->deleteLabel(1);

$event =  new Event();

## Tests select events ##
// var_dump($event->get(1));
// var_dump($event->getByID(49));

## Tests events creation ##
/*
$project_info = [
    "ref" => "LE208804",
    "id_label" => "50",
    "title" => "Titre test",
    "max_hours_per_day" => "9",
    "max_hours_per_week" => "45"
];
var_dump($event->createEvent($project_info));
*/

## Tests delete event ##
// $event->deleteEvent(103);

## Tests update event ##
/*
$event_info = [
    "id" => 105,
    "id_label" => "2"
];
$event->update($event_info);
*/

$timesheet = new Timesheet();


## Tests get timesheet ##
# Get By id_employee, from, to
// var_dump($timesheet->get(100, 1, 2));
# Get employee by id
// var_dump($timesheet->getByID(1));
//var_dump($timesheet->get(1, 152394900, 1623949600));

## Tests timesheet creation ##
# Project
/*
$timsheet_info = [
    'id_event' => 1,
    'id_employee' => 1,
    'start' => 8,
    'end' => 10,
    'at' => 1623697500,
    'hours_invested' => 2,
    'description' => 'test'
];

var_dump($timesheet->createTimesheet($timsheet_info));

$timsheet_info = [
    'id_event' => 1,
    'id_employee' => 1,
    'start' => 12,
    'end' => 15,
    'at' => 1623697500,
    'hours_invested' => 2,
    'description' => 'test'
];

var_dump($timesheet->createTimesheet($timsheet_info));
*/
/*
$timsheet_info = [
    'id_event' => 1,
    'id_employee' => 1,
    'start' => 8,
    'end' => 15,
    'at' => 1623816401,
    'hours_invested' => 7,
    'description' => 'test'
];

var_dump($timesheet->createTimesheet($timsheet_info));
*/
/*
$data_print = [
    'id_employee' => 1,
    'from' => '2021-06-13',
    'to' => '2021-06-19'
]; 
$timesheet->print($data_print);
*/
?>

 <section class="test-data-dump">
  <pre><?php //print_r($timesheet->get(1, 1623557201, 1623949600)); ?></pre>
</section>

<style>
  .test-data-dump {
    height: 90vh;
    overflow-y: scroll;
    z-index: 100;
  }
</style> 
