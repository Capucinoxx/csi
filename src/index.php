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

    // set logged_in tkn
    $_SESSION['loggedin'] = true;

    isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true
      ? require_once('./Views/Calendar.php')
      : require_once('./Views/Login.php');
  ?>

  <script src="./assets/app.js"></script>
</body>
</html>