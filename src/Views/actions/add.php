<?php 
  use App\HTML\Form;

  $roles = [
    'user' => 'Utilisateur',
    'admin' => 'Administrateur'
  ];

  $labels = [];

  $projects = [];

  $form = new Form();
?>

<div class="modal visible">
  <div class="modal-dialog">
    <div class="carousel">
      <div class="flex-end px-18">
        <button type="button" class="btn flex-center" aria-label="close">
            <span aria-hidden="true">x</span>
        </button>
      </div>

      <ul class="carousel__tags">
        <li><a href="#slide-1">Évennement</a></li>
        <li><a href="#slide-2">Employée</a></li>
        <li><a href="#slide-3">Projet</a></li>
        <li><a href="#slide-4">Libellé</a></li>
      </ul>

      <div class="carousel__elements">
        <section id="slide-1" class="carousel__element">
          <form action="">
            <?= $form->formFieldOptions('fas fa-archive', 'Projet', 'id_event', $projects, true) ?>
            <?= $form->formFieldFromTo('fas fa-clock', ['De', 'A'], ['from', 'to'], true) ?>
            <?= $form->formFiedTextArea('fas fa-comment-dots', 'Description', 'description') ?>
            <div class="flex-end mt-2">
              <button class="save-button">Enregistrer</button>
            </div>
          </form>
        </section>
        <section id="slide-2" class="carousel__element">
          <form action="">
            <?= $form->formField('fas fa-user', 'Nom d\'utilisateur', 'username', 'text') ?>
            <?= $form->formField('', 'Prénom', 'first_name', 'text') ?>
            <?= $form->formField('', 'Nom de famille', 'last_name', 'text', true) ?>
            <?= $form->formFieldOptions('fas fa-user-tag', 'Rôle', 'role', $roles) ?>
            <?= $form->formField('fas fa-calendar-check', 'Date d\'entrée en poste', 'created_at', 'date', true) ?>
            <?= $form->formField('fas fa-hand-holding-usd', 'Taux horaire', 'rate', 'number')?>
            <?= $form->formField('', 'Taux AMC', 'rate_AMC', 'number') ?>
            <?= $form->formField('', 'Taux CSI', 'rate_CSI', 'number') ?>
            <div class="flex-end mt-2">
              <button class="save-button">Enregistrer</button>
            </div>
          </form>
        </section>
        <section id="slide-3" class="carousel__element">
          <form action="">
            <?= $form->formField('fas fa-stream', 'Référence', 'ref', 'text') ?>
            <?= $form->formField('', 'Titre', 'title', 'text') ?>
            <?= $form->formFieldOptions('fas fa-tag', 'Libellé', 'id_label', $labels, true)?>
            <?= $form->formField('fas fa-hourglass-half', 'Nombre limite d\'heures journalières', 'max_hours_per_day', 'number') ?>
            <?= $form->formField('', 'Nombre limite d\'heures hebdomadaire', 'max_hours_per_week', 'number') ?>
            <div class="flex-end mt-2">
              <button class="save-button">Enregistrer</button>
            </div>
          </form>
        </section>
        <section id="slide-4" class="carousel__element">
          <form action="">
            <?= $form->formField('', 'Ajouter un titre', 'title', 'text', true) ?>
            <?= $form->formFieldColor('fas fa-palette', 'Couleur', 'color') ?>
            <div class="flex-end mt-2">
              <button class="save-button">Enregistrer</button>
            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
</div>