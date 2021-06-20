<?php 
    use App\HTML\Form;
    use App\Internal\Event;

    $formGenerator = new Form();

    $projects = [];

    $p = (new Event())->get(1);

?>

<div id="edit-event" class="modal">
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
          <?= $formGenerator->formFieldOptions('fas fa-archive', 'Projet', 'id_event', $projects, true) ?>
          <?= $formGenerator->formFieldFromTo('fas fa-clock', ['De', 'A'], ['from', 'to'], true) ?>
          <?= $formGenerator->formFiedTextArea('fas fa-comment-dots', 'Description', 'description') ?>
          <div class="flex-end mt-2">
            <button class="save-button">Enregistrer</button>
          </div>
        </form>
      </section>
    </div>
  </div>
</div>
