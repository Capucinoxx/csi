<?php
  use App\HTML\Form;

  $employees = array();

  $errors = [];
  $employee = null;

  $form = new Form($employee, $errors);
?>

<div id="modal-employees" class="modal">
  <div class="modal-dialog">
    <section class="list-modal">
      <div class="card-title">Liste des employées</div>
      <button type="button" class="btn flex-center close" aria-label="close">
        <span aria-hidden="true">x</span>
      </button>
      <div>
        <input class="form-control" type="text" onkeyup="" />
        <div class="scroll">
          <ul class="list-container">
            <? foreach($employees as $e): ?>
              <? if(!$e['employee']['deleted_at']): ?>
                <li class="flex employee">
                  <span>
                    <?= $e['employee']['last_name'].', '.$e['employee']['first_name'] ?>
                  </span>
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
          <span>&#8593;</span>
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

<script type="text/javascript">
  const modal = document.querySelector('.modal-dialog')
  document.querySelectorAll('.employee').forEach(
    (employee) => employee.addEventListener('click', () => modal.classList.add('selected'))
  )

  document.querySelectorAll('.gotoList').forEach(
    (btn) => btn.addEventListener('click', (e) => {
      e.preventDefault()
      modal.classList.remove('selected')
    })
  )
</script>