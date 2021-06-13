<?php
use App\Internal\Employee;

$employee = new Employee();

## Tests get employees (one and all) ##
// var_dump($employee->getByID(10));
// var_dump($employee->get());

## Tests employees creation ##
/*
$employee_info = [
    'username' => 'test1234567',
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
var_dump($employee->createEmployee($employee_info));
*/

## Tests suppression de l'employé ##
$employee->deleteEmployee(10);
var_dump($employee->getByID(10));
?>