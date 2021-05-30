<?php
use App\HTML\Form;

$errors = [];
$employee = null;

$form = new Form($employee, $errors);
?>
<div id="modal-employee" class="modal">
  <div class="modal-dialog">
    <div class="card-title"> Édition de Pierre-Ivette Georgette</div>
    <button type="button" class="btn flex-center close" aria-label="close">
        <span aria-hidden="true">x</span>
      </button>
    <form class="grid" action="">
      <?= $form->input('username', 'Nom d\'utilisateur') ?>
      <?= $form->input('first_name', 'Prénom') ?>
      <?= $form->input('last_name', 'Nom de famille') ?>
      <?= $form->select('role', 'Rôle', array("admin", "user")) ?>
      <?= $form->date('created_at', 'Date d\'entrée en poste') ?>

      <div class="grid-full">
        <?= $form->number('rate', 'Taux horaire') ?>
        <?= $form->number('rate_AMC', 'Taux AMC') ?>
        <?= $form->number('rate_CSI', 'Taux CSI') ?>
      </div>
    </form>
  </div>
</div>
