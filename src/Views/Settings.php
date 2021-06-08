<?php
  $open = false;

  function setOpen($state) {
    $open = $state;
  }

  // liste des actions disponible
  $actions = array();

  if (isset($_SESSION['role'])) {
    array_push($actions, array('profil', '<i class="fas fa-user></i>'));

    if ($_SESSION['role'] == 'admin') {
      array_push($actions, array('employées', '<i class=""></i>'));
      array_push($actions, array('projets', '<i class=""></i>'));
      array_push($actions, array('libellés', '<i class=""></i>'));
      array_push($actions, array('fin d\'année', '<i class=""></i>'));
    }
  }
?>

<nav class="navbar">
  <ul class="navbar-nav">
    <li class="arrow nav-item">
      t
    </li>
    <li class="arrow nav-item">
      p
    </li>
    <li class="arrow nav-item">
      s

      <div class="dropdown hidden">
        <div class="menu-primary">
          <div class="menu">
            <?php foreach($actions as $action): ?>
              <span><?= $action[1] ?></span>
              <?= $action[0] ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </li>
  </ul>
</nav>