<?php 
  use App\HTML\Form;

  $events = array();
  $labels = array();
?>

<div class="modal">
  <div class="modal-dialog">
    <section class="list-modal">
      <div class="card-title">Liste des projets</div>
      <button type="button" class="btn flex-center close" aria-label="close">
        <span aria-hidden="true">x</span>
      </button>
      <div class="searchList">
        <input class="searchbox form-control" type="text" />
        <div class="scroll">
          <ul class="list-container">
            <? foreach($events as $event): ?>
              <li class="flex list-item">
                <?= $event['title'] ?>
              </li>
            <? endforeach; ?>
          </ul>
        </div>
      </div>
    </section>

    <section class="form-modal">
      <div class="card-title">Édition du projet [...]</div>
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

      <form action="" class="grid">
        <?= $form->input('ref', 'Référence') ?>
        <?= $form->input('title', 'Titre') ?>
        <?= $form->select('id_label', 'Libellé', $labels) ?>
        
        <div class="grid-full">
          <?= $form->number('max_hours_per_day', 'Nombre limite d\'heures journalière') ?>
          <?= $form->number('max_hours_per_Week', 'Nombre limit d\'heures hebdomadaire') ?>
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