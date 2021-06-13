<?php
use App\Internal\Employee;

$employee = new Employee();

## Tests get employees (one and all) ##
// var_dump($employee->getById(1));
// var_dump($employee->get());

## Tests employees creation ##
$employee_info = [
    'username' => 'test12345',
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
var_dump($employee_info);

$employee->createEmployee($employee_info);

?>