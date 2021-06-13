<?php
  $open = false;

  function setOpen($state) {
    $open = $state;
  }

  // liste des actions disponible
  $actions = array();

  if (isset($_SESSION['role'])) {
    array_push($actions, array('profil', '<i class="fas fa-user"></i>'));

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
    <li class="btn big-btn-reverse" data-modal="add-modal" data-action="Édition" style="margin-right: 14px;">
      <i class="fas fa-edit"></i>
    </li>
    <li class="btn big-btn-reverse">
      <i class="fas fa-print"></i>
    </li>
  </ul>
</nav>