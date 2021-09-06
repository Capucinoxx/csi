<?php
use App\Internal\Employee;
use App\Internal\Label;
use App\Internal\Event;
use App\Internal\Timesheet;
use App\Internal\FiscalYear;
use App\Internal\Leave;

$employee = new Employee();


$event = new Event();

var_dump($event->getById(10));


## Tests get employees (one and all) ##
var_dump($employee->getByID(20));
//var_dump($employee->get());

## Tests employees creation ##

$employee_info = [
    'username' => 'testadmin',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'password' => '1234',
    'role' => 1,
    'regular' => true,
    'rate' => 45.48,
    'rate_AMC' => 44.25,
    'rate_CSI' => 46.25,
    'created_at' => 1474848000000,
    'deleted_at' => null
];
// var_dump($employee_info);
//var_dump($employee->createEmployee($employee_info));

?>
