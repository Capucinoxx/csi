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
    <li class="arrow nav-item" data-modal="">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 19.738 19.738" style="width: 24px important; height: 24px !important;">
        <path style="fill:#010002;" d="M18.18,19.738h-2c0-3.374-2.83-6.118-6.311-6.118s-6.31,2.745-6.31,6.118h-2   c0-4.478,3.729-8.118,8.311-8.118C14.451,11.62,18.18,15.26,18.18,19.738z"/>
        <path style="fill:#010002;" d="M9.87,10.97c-3.023,0-5.484-2.462-5.484-5.485C4.385,2.461,6.846,0,9.87,0   c3.025,0,5.486,2.46,5.486,5.485S12.895,10.97,9.87,10.97z M9.87,2C7.948,2,6.385,3.563,6.385,5.485S7.948,8.97,9.87,8.97   c1.923,0,3.486-1.563,3.486-3.485S11.791,2,9.87,2z"/>
      </svg>
    </li>
    <li class="arrow nav-item" data-modal="add-modal" data-action="Édition">
      <svg viewBox="0 0 512 511" xmlns="http://www.w3.org/2000/svg" style="width: 24px important; height: 24px !important;">
        <path d="m405.332031 256.484375c-11.796875 0-21.332031 9.558594-21.332031 21.332031v170.667969c0 11.753906-9.558594 21.332031-21.332031 21.332031h-298.667969c-11.777344 0-21.332031-9.578125-21.332031-21.332031v-298.667969c0-11.753906 9.554687-21.332031 21.332031-21.332031h170.667969c11.796875 0 21.332031-9.558594 21.332031-21.332031 0-11.777344-9.535156-21.335938-21.332031-21.335938h-170.667969c-35.285156 0-64 28.714844-64 64v298.667969c0 35.285156 28.714844 64 64 64h298.667969c35.285156 0 64-28.714844 64-64v-170.667969c0-11.796875-9.539063-21.332031-21.335938-21.332031zm0 0"/>
        <path d="m200.019531 237.050781c-1.492187 1.492188-2.496093 3.390625-2.921875 5.4375l-15.082031 75.4375c-.703125 3.496094.40625 7.101563 2.921875 9.640625 2.027344 2.027344 4.757812 3.113282 7.554688 3.113282.679687 0 1.386718-.0625 2.089843-.210938l75.414063-15.082031c2.089844-.429688 3.988281-1.429688 5.460937-2.925781l168.789063-168.789063-75.414063-75.410156zm0 0"/>
        <path d="m496.382812 16.101562c-20.796874-20.800781-54.632812-20.800781-75.414062 0l-29.523438 29.523438 75.414063 75.414062 29.523437-29.527343c10.070313-10.046875 15.617188-23.445313 15.617188-37.695313s-5.546875-27.648437-15.617188-37.714844zm0 0"/>
      </svg>
    </li>
    <li class="arrow nav-item" data-modal="">
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