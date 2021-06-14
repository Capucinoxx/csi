<?php 
  use App\HTML\Form;
  $form = new Form();
?>

<div id="edit-modal" class="modal visible">
  <div class="modal-dialog">
    <nav class="tabbar">
      <span class="modal-title"></span>
      <button type="button" class="btn btn-reverse m-1 mb-0">
        <span>x</span>
      </button>
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