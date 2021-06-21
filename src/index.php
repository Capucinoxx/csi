<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="./assets/style/app.css">
  
</head>
<body>
  <?php 
    require_once(dirname(__DIR__).'/html/vendor/autoload.php');
    
    use \App\Views\Calendar;
    use \App\Views\Forms;
    use App\HTML\Form;
    use App\Internal\Label;
    use App\Internal\Employee;
    use App\Internal\Event;

    $formGenerator = new Form();
    $calendar = new Calendar($_GET['week'] ?? null, $_GET['year'] ?? null, $projects_week);
    
    $forms = new Forms(
      (new Label())->get(),
      (new Event())->get(1),
      (new Employee())->get()
    );

    // set logged_in tkn
    // $_SESSION['loggedin'] = true;

    // isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true
    //   ? require_once('./Views/Calendar.php')
    //   : require_once('./Views/Login.php');
  ?>

  <div class="flex wrapper">
    <div class="flex-x p-2">
      <section class="controls-panel">
        <div class="panel-option">
          <button>
            <i class="fas fa-plus"></i>
            Ajout
          </button>
        </div>
        <div class="panel-option">
          <button id="btn-trigger-gestion">
            <i class="fas fa-cog"></i>
            Gestion
          </button>
          <ul class="gestion-options">
            <li>
              <i data-modal="gestion-labels" class="gestion-option fas fa-tag"></i>
            </li>
            <li>
              <i data-modal="gestion-users" class="gestion-option fas fa-users"></i>
            </li>
            <li>
              <i data-modal="gestion-projects" class="gestion-option fas fa-archive"></i>
            </li>
          </ul>
        </div>
      </section>
      <?= $calendar->draw_monthly_calendar() ?>
        <span class="employee-name">Jacty Milena</span>
      <div class="flex-y-end" style="flex: 1 1 auto">
        <div class="panel-option">
          <button>
            <i class="fas fa-sign-out-alt"></i>
            Déconnexion
          </button>
        </div>
      </div>
    </div>
    <div class="flex-grow-2 ml-3">
      <?= $calendar->draw_weekly_calendar() ?>
    </div>
  </div>

  <?= $forms->draw("libellé", "gestion-labels") ?>
  <?= $forms->draw("employé", "gestion-users") ?>

  <script src="./assets/add.js"></script>
</body>
</html>