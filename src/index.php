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
    use App\HTML\Form;

    

    $projects_week = array(
      array(
        (object) ['title' => 'Journée québécoise', 'start' => 12.5, 'end' => 15.5, 'color' => '#32a88d']
      ),
      array(),
      array(),
      array(),
      array(
        (object) ['title' => 'test', 'start' => 6.5, 'end' => 12.0, 'color' => '#eb4034'],
        (object) ['title' => 'test', 'start' => 13, 'end' => 18, 'color' => '#eb4034']
      ),
      array(
        (object) ['title' => 'test', 'start' => 6.5, 'end' => 18.5, 'color' => '#eb4034']
      ),
      array()
    );

    $formGenerator = new Form();
    $calendar = new Calendar($_GET['week'] ?? null, $_GET['year'] ?? null, $projects_week);

    $projects = [];

    // set logged_in tkn
    // $_SESSION['loggedin'] = true;

    // isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true
    //   ? require_once('./Views/Calendar.php')
    //   : require_once('./Views/Login.php');
  ?>

  <div class="flex wrapper">
    <div class="flex-yp-2">
      <section class="controls-panel">
        <div class="panel-option">
          <button>
            <i class="fas fa-plus"></i>
            Ajout
          </button>
        </div>
        <div class="panel-option">
          <button>
            <i class="fas fa-cog"></i>
            Édition
          </button>
        </div>
      </section>
      <?= $calendar->draw_monthly_calendar() ?>
    </div>
    <div class="flex-grow-2 ml-3">
      <?= $calendar->draw_weekly_calendar() ?>
    </div>
  </div>



  <script src="./assets/add.js"></script>
</body>
</html>