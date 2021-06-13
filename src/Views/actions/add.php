<?php 
  use App\HTML\Form;

  $form = new Form();
?>

<div class="modal visible">
  <div class="modal-dialog">
    <div class="carousel">
      <ul class="carousel__tags">
        <li><a href="#slide-1">Évennement</a></li>
        <li><a href="#slide-2">Employée</a></li>
        <li><a href="#slide-3">Projet</a></li>
        <li><a href="#slide-4">Libellé</a></li>
      </ul>

      <div class="carousel__elements">
        <section id="slide-1" class="carousel__element">
          formulaire ajout heures
        </section>
        <section id="slide-2" class="carousel__element">
          <form action="">
            <?= $form->formField('fas fa-user', 'Nom d\'utilisateur', 'username') ?>
            <?= $form->formField('', 'Prénom', 'first_name') ?>
            <?= $form->formField('', 'Nom de famille', 'last_name', true) ?>
            <?= $form->formField('fas fa-user-tag', 'Rôle', 'role') ?>
            <?= $form->formField('fas fa-calendar-check', 'Date d\'entrée en poste', 'created_at', true) ?>
            <?= $form->formField('fas fa-hand-holding-usd', 'Taux horaire', 'rate')?>
            <?= $form->formField('', 'Taux AMC', 'rate_AMC') ?>
            <?= $form->formField('', 'Taux CSI', 'rate_CSI') ?>
            <div class="flex-end mt-2">
              <button class="save-button">Enregistrer</button>
            </div>
          </form>
        </section>
        <section id="slide-3" class="carousel__element">
          <form action="">
            <?= $form->formField('fas fa-stream', 'Référence', 'ref') ?>
            <?= $form->formField('', 'Titre', 'title') ?>
            <?= $form->formField('fas fa-tag', 'Libellé', 'id_label', true)?>
            <?= $form->formField('fas fa-hourglass-half', 'Nombre limite d\'heures journalières', 'max_hours_per_day') ?>
            <?= $form->formField('', 'Nombre limite d\'heures hebdomadaire', 'max_hours_per_week') ?>
            <div class="flex-end mt-2">
              <button class="save-button">Enregistrer</button>
            </div>
          </form>
        </section>
        <section id="slide-4" class="carousel__element">
          formulaire ajout libellé
          <?= $form->formField('', 'Ajouter un titre', 'title', true) ?>
          <?= $form->formField('fas fa-palette', 'Couleur', 'color') ?>
        </section>
      </div>
    </div>
  </div>
</div>