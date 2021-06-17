<?php 
  use App\HTML\Form;
  $form = new Form();
?>

<div id="edit-modal" class="modal">
  <div class="modal-dialog">
    <nav class="tabbar">
      <div class="flex-between">
        <span class="modal-title title-mid-text"></span>
        <button type="button" class="btn btn-reverse m-1 mb-0 cmb">
          <span>x</span>
        </button>
      </div>
    </nav>

    <div class="carousel__elements">
      <section class="carousel__element">
        <form action="">
          <?= $form->formFieldOptions('fas fa-archive', 'Projet', 'id_event', $projects, true) ?>
          <?= $form->formFieldFromTo('fas fa-clock', ['De', 'A'], ['from', 'to'], true) ?>
          <?= $form->formFiedTextArea('fas fa-comment-dots', 'Description', 'description') ?>
          <div class="flex-end mt-2">
            <button class="save-button">Enregistrer</button>
          </div>
        </form>
      </section>
    </div>
  </div>
</div>