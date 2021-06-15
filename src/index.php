<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="./assets/style.css">
  
</head>
<body>

  <?php require_once(dirname(__DIR__).'/html/vendor/autoload.php'); ?>
  <?php 
    require_once('./Views/Login.php');
    // require_once('./Views/Calendar.php');
    // use \PDO;
    // $servername = "mysql";
    // $username = "user";
    // $password = "password";
    // $dbname = "db";
    // $port = "3306";
    
    // try{
    //    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname",$username,$password);
    //    $conn -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //    echo "Connected succesfully";
    // } catch(PDOException $e){
    //    echo "Connection failed: " . $e -> getMessage();
    // }
  ?>
  <?php 
    // require_once('./Views/employees/show.php'); 
    // require_once('./Views/labels/show.php');
    require_once('./Views/actions/add.php');
    require_once('./Views/actions/edit.php');
  ?> 

      <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.3.4/gsap.min.js"></script>
  <script src="./assets/app.js"></script>
</body>
</html>