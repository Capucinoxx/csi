<?php
require_once(dirname(__DIR__).'/html/vendor/autoload.php');
header("Access-Control-Allow-Origin: *");
session_start();

require_once(dirname(__DIR__).'/html/Views/head.html');

use \App\Constructors\Forms;

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  $forms = new Forms([], [], []);
  echo $forms->FieldWithLabel('Début année fiscale', 'start', 'date');
  echo $forms->FieldWithLabel('Fin de l\'année fiscale', 'end', 'date');

  echo "
  <script>
    const formData = new FormData()
    formData.append('context', 'changeFiscalYear)
    document.querySelectorAll('input').forEach(
      (input) => {
        formData.append(input.getAttribute('name'), (new Date(input.value)).getTime())
      }
    )

    fetch(window.location, { method: 'post', body: formData }).then(() => window.location = window.location)
  </script>
  ";

} else {  
  $forms = new Forms([], [], []);
  require_once('./Views/Login.php');
}