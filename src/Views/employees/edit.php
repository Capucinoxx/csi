<?php
use App\HTML\Form;

$errors = [];
$employee = null;

$form = new Form($employee, $errors);
?>

<form action="">
  <?= $form->input('first_name', 'Prénom') ?>
  <?= $form->input('last_name', 'Nom de famille') ?>
</form>