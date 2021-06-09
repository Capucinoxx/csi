<?php
  use App\HTML\Form;

  $employees = array();

  $errors = [];
  $employee = null;

  $form = new Form($employee, $errors);
?>

<div id="modal-employees" class="modal" style="display: none">
  <div class="modal-dialog">
    <section class="list-modal">
      <div class="card-title">Liste des employées</div>
      <button type="button" class="btn flex-center close" aria-label="close">
        <span aria-hidden="true">x</span>
      </button>
      <div class="searchlist">
        <input class="searchbox form-control" type="text" />
        <div class="scroll">
          <ul class="list-container">
            <? foreach($employees as $e): ?>
              <? if(!$e['employee']['deleted_at']): ?>
                <li class="flex employee">
                    <?= $e['employee']['last_name'].', '.$e['employee']['first_name'] ?>
                </li>
              <? endif; ?>
            <? endforeach; ?>
            <li class="flex employee list-item"><span>toto</span></li>
            <li class="flex employee list-item"><span>tato</span></li>
          </ul>
        </div>
      </div>
    </section>
 
    <section class="form-modal">
      <div class="card-title"> Édition de Pierre-Ivette Georgette</div>
      <div class="flex-y close">
        <div class="gotoList btn flex-center" style="font-weight: 900">
          <div class="box-animation">
            <svg viewBox="0 0 60.123 60.123" width="10px" height="10px" fill="#1e2235">
              <path d="M57.124,51.893H16.92c-1.657,0-3-1.343-3-3s1.343-3,3-3h40.203c1.657,0,3,1.343,3,3S58.781,51.893,57.124,51.893z"/>
              <path d="M57.124,33.062H16.92c-1.657,0-3-1.343-3-3s1.343-3,3-3h40.203c1.657,0,3,1.343,3,3   C60.124,31.719,58.781,33.062,57.124,33.062z"/>
              <path d="M57.124,14.231H16.92c-1.657,0-3-1.343-3-3s1.343-3,3-3h40.203c1.657,0,3,1.343,3,3S58.781,14.231,57.124,14.231z"/>
              <circle cx="4.029" cy="11.463" r="4.029"/>
              <circle cx="4.029" cy="30.062" r="4.029"/>
              <circle cx="4.029" cy="48.661" r="4.029"/>
            </svg>
            &#8593;
          </div>
        </div>
        <button type="button" class="btn flex-center" aria-label="close" style="font-weight: 900">
          <span aria-hidden="true">x</span>
        </button>
      </div>
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

        <div
          class="flex"
          style="position: absolute; bottom: 0; right: 0;"
        >
          <button type="submit" class="fat-btn">
            Édité
          </button>
        </div>
        
      </form>
    </section>
  </div>
</div>
