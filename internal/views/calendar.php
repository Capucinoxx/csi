<?php 
  /*  CONSTANTES
   * -------------------------------------------------
   * création des tableaux de références des jours de la semaine
   * et des mois de l'année
   -----------------------------------------------------------*/
  $week_day = array(
    'dim', 
    'lun', 
    'mar', 
    'mer', 
    'jeu', 
    'ven', 
    'sam'
  );
  $months = array(
    'Janvier',
    'Février',
    'Mars',
    'Avril',
    'Mai',
    'Juin',
    'Juillet',
    'Août',
    'Septembre',
    'Octobre',
    'Novembre',
    'Décembre'
  );

  /* Gestion date
   * -------------------------------------------------
   * gestion des dates de la semaine courrante. Cela peut être
   * soit la semaine en cours ou celle définie dans les paramêtres
   * de l'URI
   -----------------------------------------------------------*/
  $dt = new DateTime; // référentiel de date

  // si date est spécifié dans les params, changer le référentiel
  isset($_GET['year']) && isset($_GET['week'])
    ? $dt->setISODate($_GET['year'], $_GET['week'] )
    : $dt->setISODate($dt->format('o'), $dt->format('W'));

  $year = $dt->format('o');
  $week = $dt->format('W');

  $dt_clone = clone $dt;
  // définition du dimanche commençant la semaine et du samedi finissant la semaine
  $sunday = clone $dt_clone->modify(
    ('Saturday' == $dt_clone->format('l')) ? 'Sunday this week' : 'Sunday last week'
  );
  $saturday = clone $dt_clone->modify('Saturday next week');

  // diminue d'une semaine de celle affichée
  // retourne les arguments pour l'URI sour forme week={}&year={}
  function decreaseHandler($date) {
    $last_week = (clone $date)->modify('last week');

    return "week=".($last_week->format('W'))."&year=".($last_week->format('o'));
  };

  // augmente d'une semaine de celle affichée
  // retourne les arguments pour l'URI sour forme week={}&year={}
  function increaseHandler($date) {
    $next_week = (clone $date)->modify('next week');

    return "week=".($next_week->format('W'))."&year=".($next_week->format('o'));
  };

  /* Gestion des heures
   * -------------------------------------------------
   * formattage des heures pour le visuel utilisateur
   -----------------------------------------------------------*/

   // formatte l'heure pour retourner sour format hh:mm
   function format_date($date) {
     $hour = floor($date) < 10 ? '0'.floor($date) : floor($date);
     $minute = ($date - $hour) * 60 < 10 ? '0'.round(($date - $hour) * 60) : round(($date - $hour) * 60);

     return $hour.":".$minute;
   };
?>

<div class="wrapper">
   <div class="wrapper-title">
      <h2>Gestionnaire d'horaire</h2>
   </div>
   <div class="wrapper-hidden">
    <div class="banner">

      <div class="banner__actions">
        <a 
          href="<?php echo $_SERVER['PHP_SELF'].'?'.decreaseHandler($dt); ?>" 
          class="arrow left"
        >
          <i></i>
          <svg><use xlink:href="#circle"></svg>
        </a>

        <a class="btn">
        Semaine courante
        </a>

        <a href="
          <?php echo $_SERVER['PHP_SELF'].'?'.increaseHandler($dt); ?>
        " class="arrow">
          <i></i>
          <svg><use xlink:href="#circle"></svg>
        </a>
      </div>

      <div class="banner_inf">
        <?php 
          $start_month = $sunday->format('n');
          $end_month = $saturday->format('n');

          echo "du ".($start_month == $end_month
            ? $sunday->format('d')." au ".$saturday->format('d')." ".$months[$start_month - 1].", ".$sunday->format('Y')
            : $sunday->format('d')." ".$months[$start_month - 1]." au ".$saturday->format('d')." ".$months[$end_month - 1].", ".$sunday->format('Y'));
        ?>
      </div>
    </div>

    <ul>
      <?php foreach (range(12,46) as $value): ?>
        <li>
          <span>
            <?php echo format_date($value / 2.0); ?>
          </span>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

