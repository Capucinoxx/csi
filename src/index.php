<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="./assets/style.css">
</head>
<body>

  <?php 
    require_once(dirname(__DIR__).'/html/vendor/autoload.php');
  
  ?>
  <?php require_once('./Views/Calendar.php'); ?>
  <?php require_once('./Views/employees/show.php'); ?>

  <script src="./assets/app.js"></script>
</body>
</html>