<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>


<body>
<?php
    date_default_timezone_set('EST');
  ?>
<style>
    td {
      text-align: center;
    }

    .flex {
      display: flex;
      justify-content: space-between;
    }

    .bg-grey-weekend {
      background: #B3B3B3;
    }

    .bg-grey-week {
      background: #D9D9D9; 
    }
  
    .color-grey-700 {
      color: #363636;
    }

    .fw-300 { 
      font-weight: 300;
    }

    .d-inline {
      display: inline-block;
    }

    .rotate {
      transform: rotate(90deg);
    }

    .bottom {
      vertical-align: bottom;
    }

    .border-top { 
      border-top: 1px solid #737373;
    }

    .border-left {
      border-left: 1px solid #737373;
    }


    tr:nth-child(3) > td {
      border-top: 1px solid #737373;
    }

    .total > * {
      border-top: 1px solid black;
    }

    .fw-normal {
      font-weight: normal;
    }

    .mw-300 {
      min-width: 260px;
    }

    .mw-400 {
      min-width: 300px;
    }

    .mw-120 {
      min-width: 70px;
    }

    .mw-35 {
      min-width: 35px;
    }

    .max-w-300{
      max-width: 260;
    }

    .max-w-400{
      max-width: 300px;
    }

    .line {
      width: 50%;
      border-bottom: 1px solid black;
      align-content: right;
    }

    .text-left {
      text-align: left;
    }

    .fs-small {
      font-size: small;
    }

    .fs-smaller {
      font-size: smaller;
    }

    table:first-child{
      margin-bottom: 70px; 
    }
  </style>
  <table style="border-spacing: 0px;">
    <col>
    <colgroup span="8"></colgroup>
    <tr>
      <td rowspan="2">
        <img src="https://www.csisher.com/wp-content/themes/csi_2016/img/logo.jpg" alt="" srcset="" width="300px">
      </td>
      <th colspan="8" scope="colgroup">
        <?php
          $payed_hours = 0;
          $total = 0;
          $day_array = array();
          $calendar = array();
          $query = $this->getEmployeeInfo($data['id_employee']);
          $stmt = $query->fetch(PDO::FETCH_ASSOC);
        ?>
        <div>
          <h2 class="fs-small" style="text-align: center; padding-bottom: 0px; margin-bottom: 0px;">
          <?php echo $stmt['first_name'] . " " . $stmt['last_name'];?>
          </h2>
          <span class="fs-smaller fw-normal" style="display: flex; justify-content: center;">
          <?php echo "Du: " . $data['from'];?>
          </span>
          <span class="fs-smaller fw-normal" style="display: flex; justify-content: center;">
          <?php echo "Au: " . $data['to'];?>
        </span>
        </div>
      </th>
    </tr>
    <tr class="fs-small">

    <?php 
          setlocale(LC_TIME, 'fra', 'french-canadian');
          for($i = 0; $i < 7; $i++){
            $calendar[$i] =  " ";
            $day = date('Y-m-d',date(strtotime("+{$i} day", strtotime($data['from']))));
            array_push($day_array, $day);
    ?>
      <th scope="col">
        <p class="rotate"><?php echo substr(ucfirst(strftime("%A", strtotime($day))), 0, 3);?></p>
        <strong><?php echo date("d", strtotime($day));?></strong>
      </th>
    <?php 
          }
    ?> 
      <th scope="col" class="rotate">Total</th>
      <th scope="col" class="bottom text-left">Description</th>
    </tr>

    <?php
      $last_label = 0;
      $cpt = 0;
      $aside = array();
      $query = $this->getEventsInfo($data); 
      while($stmt = $query->fetch(PDO::FETCH_ASSOC)) {
        
    ?>
    <tr>
      <th class="fw-300 color-grey-700 d-inline mw-120 fs-small" scope="row" style="text-align: left;">
        <?php echo $stmt['ref'];?>
      </th>
        
      <th class="d-inline border-top fw-normal mw-300 max-w-300 fs-small" scope="row" style="text-align: left;">
        <?php echo $stmt['title']; ?>
      </th>
      <?php
        $i = 0;
        $total = 0;
        $hours = " ";
        $description = "";
        $query2 = $this->getHoursData($data, $stmt['id_event']);
        while($stmt2 = $query2->fetch(PDO::FETCH_ASSOC)) {
          $total += floatval($stmt2['hours']);
          $description .= $stmt2['description'] . ". <br/>";
        
          for($i = 0; $i < 7; $i++){
            if(intval($stmt2['day']) == intval(date("d", strtotime($day_array[$i])))) {
              $calendar[$i] = number_format(floatval($stmt2['hours']), 2);
            } 
          }
        }
        for($i = 0; $i < 7; $i++) {
      ?>
      <td class="border-left mw-35 border-top fs-smaller"><?php echo $calendar[$i];?></td>
      <?php
          $calendar[$i] = " ";
        }
        
        $payed_hours += $total; 
    ?>

      <td class="border-left mw-35 border-top fs-smaller"><strong><?php echo number_format($total, 2);?></strong></td>
      <td class="border-left text-left max-w-400 mw-400 border-top"><?php echo $description;?></td>
    </tr>
    <?php  
        $verif = false;
        if($stmt['amc'] == 1) {
          $cpt += 1;
          $last_label = $stmt['id_label'];
          $last_title = $stmt['title_label'];
          if($last_label != $stmt['id_label']) {
            if(!$last_title) {
              $last_title = $stmt['title_label'];
            }
          }
          $verif = true;
        } else if($cpt > 0){
          $cpt = 0;
          $verif = true;
        }
        
        if($verif == true) {
          $sous_total = 0;
          $cpt = 0;
        
    ?>
    <tr>
      <th class="fw-normal fs-smaller" style="text-align: right;"><?php echo "Sous-total " .  $last_title;?></th>
    <?php
            for($j = 0; $j < 7; $j++) {
              $at = date('Y-m-d',date(strtotime("+{$j} day", strtotime($data['from']))));
              $query3 = $this->getAMCHours($at, $data['id_employee'], $last_label);
              $stmt3 = $query3->fetch(PDO::FETCH_ASSOC);
              
              if($stmt3['hours']) {
                $hours = number_format($stmt3['hours'], 2);
                $sous_total += number_format($hours, 2);
              } else {
                $hours = " ";
              }
    ?>
      <td class="border-left mw-35 fs-smaller"><?php echo $hours?></td>
    <?php
            }
    ?>
      <td class="border-left mw-35 fs-smaller"><strong><?php echo number_format($sous_total, 2);?></strong></td>
      <td class="border-left mw-35 fs-smaller"> </td>
    </tr>
    <?php
       }
      }
    ?>
    <tr class="total" style="font-weight: bold;">
     <th scope="row" style="text-align: right;" class="fs-small">TOTAUX</th>
      <?php
        $overtime = 0;
        for($i = 0; $i < 7; $i++) {
          $at = date('Y-m-d',date(strtotime("+{$i} day", strtotime($data['from']))));
          $query = $this->getTotalHours($at, $data['id_employee']);
          $stmt = $query->fetch(PDO::FETCH_ASSOC);

          if($stmt['total_hours']) {
            $hours = $stmt['total_hours'];
            // if($hours > 40) {
            //   $overtime += ($hours - 40)*0.5;
            // }
          } else {
            $hours = 0.00;
          }
      ?>
      <td class="border-top fs-smaller"><?php echo number_format($hours, 2);?></td>
      <?php  
        }
      ?>
      <td class="border-top fs-smaller"><?php echo number_format($payed_hours, 2);?></td>
      <td></td>
    </tr>
      <?php
        $overtime = $payed_hours - 40;
        
        if($overtime > 0) {

      ?>
    <tr>
      <th class="fs-small" scope="row" colspan="8" style="text-align: right; font-weight: normal;">Heures supplémentaires</th>
      <td class="fs-smaller"><strong><?php echo number_format($overtime, 2);?></strong></td>
    </tr>
      <?php
          $payed_hours -= $overtime;
          $payed_hours += $overtime *1.5;
        }
      ?>
    
    <tr>
      <th class="fs-small" scope="row" colspan="8" style="text-align: right; font-weight: normal;">Heures payées</th>
      <td class="fs-smaller"><strong><?php echo number_format($payed_hours, 2);?></strong></td>
    </tr>
    </col>
  </table>

  <table>
    <tr>
      <td style="min-width: 400px;">
          <span class="fs-small" style="border-top: 1px solid black; padding-right: 30px; padding-left: 100px;">Signature vérification</span>
      </td>

      <td style="min-width: 400px;">
        <span class="fs-small" style="border-top: 1px solid black; padding-right: 30px; padding-left: 100px;">Signature employé.e</span>
      </td>
    </tr>
  </table>
</body>

</html>
