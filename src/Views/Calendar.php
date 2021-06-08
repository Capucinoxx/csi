<?php 
  /*  CONSTANTES
   * -------------------------------------------------
   * création des tableaux de références des jours de la semaine
   * et des mois de l'année
   -----------------------------------------------------------*/
  $week_days = array(
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

  /* Liste des évennements
   * -------------------------------------------------
   * 
   -----------------------------------------------------------*/
  // prend les données des évenneemnts de la semaine représentée
  // $events = json_decode(
  //   Employee::getTimesheet(
  //      $_SESSION['id'], 
  //      $sunday->getTimestamp() * 1000,
  //      $saturday->getTimestamp() * 1000
  //   ), true
  // );

?>

<div class="wrapper">
  <? require_once(dirname(__FILE__) . '/Settings.php'); ?>
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

        <a 
          href="<?php echo $_SERVER['SCRIPT_NAME']; ?>"
          class="fat-btn"
        >
        Semaine courante
        </a>

        <a 
          href="<?php echo $_SERVER['PHP_SELF'].'?'.increaseHandler($dt); ?>" 
          class="arrow"
        >
          <i></i>
          <svg><use xlink:href="#circle"></svg>
        </a>
      </div>

      <div class="banner__inf">
        <?php 
          $start_month = $sunday->format('n');
          $end_month = $saturday->format('n');

          echo "du ".($start_month == $end_month
            ? $sunday->format('d')." au ".$saturday->format('d')." ".$months[$start_month - 1].", ".$sunday->format('Y')
            : $sunday->format('d')." ".$months[$start_month - 1]." au ".$saturday->format('d')." ".$months[$end_month - 1].", ".$sunday->format('Y'));
        ?>
      </div>
    </div>

    <div class="schedule__events">
      <div class="scroll">
        <div style="position: relative">
          <ul class="py-30">
            <?php foreach (range(6, 23) as $value): ?>
              <li 
                class="schedule__row <?php echo $value == 12 ? 'midi' : ''?>">
                <span>
                  <?php echo format_date($value); ?>
                </span>
              </li>
            <?php endforeach; ?>
          </ul>

          <ul class="ml-60 z-10" style="align-items: stretch;">
            <?php for($i = 0, $d = clone $sunday; $i < 7; $i++, $d->modify('+1 day')): ?>
              <li class="schedule__group">
                <div class="flex-center">
                  <?php echo $week_days[$i]." ".$d->format('d'); ?>
                </div>

                <ul class="h-100">


                </ul>
              </li>
            <?php endfor; ?>
          </ul>

        </div>
      </div>
    </div>
  </div>
</div>

