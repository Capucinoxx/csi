<?php
require_once(dirname(__DIR__).'/src/vendor/autoload.php');
header("Access-Control-Allow-Origin: *");
session_start();

require_once(dirname(__DIR__).'/src/Views/head.html');

use \App\Constructors\Forms;
use App\Internal\FiscalYear;

if (isset($_POST['context']) && $_POST['context'] == 'changeFiscalYear') {
  $rep = (new FiscalYear())->restartYear([
    'start' => $_POST['start'] / 1000,
    'end' => $_POST['end'] / 1000
  ]);

  function check($obj) {
    if (isset($obj['error'])) {
      $_SESSION['error'] = $obj['error'];
    } else if (isset($obj->error)) {
      $_SESSION['error'] = $obj->error;
    } else {
      unset($_SESSION['error']);
    }
  }

  check($rep);
  print_r($rep);
  die();
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  $forms = new Forms([], [], []);

  echo "
  <div class='flex-center'>
    <section class='login-wrapper'>
  ";

  echo $forms->FieldWithLabel('Début année fiscale', 'start', 'date');
  echo $forms->FieldWithLabel('Fin de l\'année fiscale', 'end', 'date');
  echo "<button>Réinitialiser l'année fisclae</button>";

  echo "</section></div>";

  echo "
  <script>
    document.querySelector('button').addEventListener('click', () => {
      const formData = new FormData()
      formData.append('context', 'changeFiscalYear')
      document.querySelectorAll('input').forEach(
        (input) => {
          formData.append(input.getAttribute('name'), (new Date(input.value)).getTime())
        }
      )
  
      fetch(window.location, { method: 'post', body: formData }).then(() => window.location = window.location)
    })
  

  </script>
  </body>
</html>
  ";

} else {  
  $forms = new Forms([], [], []);
  require_once('./Views/Login.php');
}

?>

