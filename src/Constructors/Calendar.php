<?php 

namespace App\Constructors;
use App\Constructors\Forms;
use App\Internal\Event;
use App\Internal\Timesheet;
use DateTime;

class Calendar {
  private $days = [
    'Dim', 
    'Lun', 
    'Mar', 
    'Mer', 
    'Jeu', 
    'Ven', 
    'Sam'
  ];

  private $months = [
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
  ];

  public $projects;
  public $week;
  public $year;
  public $forms;
  public $timesheet;
  public $event;

  /**
   * Calendar contructor
   * @param int $week Le numéro de la semaine
   * @param int $year L'année
   * @param array $proejcts Liste des projets de la semaine en cours
   */
  public function __construct(Event $event, Timesheet $timesheet, Forms $forms, ?int $week = null, ?int $year = null, ?array $projects = null) {
    $this->projects = $projects === null ? [[],[],[],[],[],[],[]] : $projects;

    $this->timesheet = $timesheet;
    $this->event = $event;
    $this->forms = $forms;
    $this->week = $week === null ? intval(date('W')) : $week;
    $this->year = $year === null ? intval(date('o')) : $year; 

  }

  public function dump(): string {
    $start = $this->getStartingWeeklyDay()->format('U');
    $end = $this->getStartingWeeklyDay()->modify('+6 day -1 minute')->format('U');
    $dump = print_r($this->timesheet->get($_SESSION['id'], $start, $end));
    return <<<HTML
      <div style="overflow:hidden; max-height: 400px">
        <pre>{$dump}</pre>
      </div>
    HTML;
  }

  /**
   * Renvoie le nombre de semaine dans le mois
   * @return int
   */
  public function getWeeks(): int {
    $start = $this->getStartingMonthlyDay();
    $end = (clone $start)->modify('+1 month -1 day');
    $startWeek = intval($start->format('W'));
    $endWeek = intval($end->format('W'));
    if ($endWeek === 1) {
        $endWeek = intval($end->modify('- 7 days')->format('W')) + 1;
    }
    $weeks = $endWeek - $startWeek + 1;
    if ($weeks < 0) {
        $weeks = intval($end->format('W'));
    }

    return $weeks;
  }

  /**
   * Renvoie le premier jour de la semaine
   * @return \DateTime
   */
  public function getStartingWeeklyDay(): \DateTime {
    $date = (new \DateTime())->setISODate($this->year, $this->week);
    return (clone $date)->modify(
      ('Saturday' == $date->format('l')) ? 'Sunday this week' : 'Sunday last week'
    );
  }

  /**
   * Renvoie le premier jour du mois
   * @return \DateTime
   */
  public function getStartingMonthlyDay(): \DateTime {
    $month = (new \DateTime())->setISODate($this->year, $this->week)->format('m');
    return new \DateTime("{$this->year}-{$month}-01");
  }

  /**
   * Retourne l'intervalle couverte par la semaine courante
   * @return string 
   */
  public function getWeeklyDate(): string {
    $start = $this->getStartingWeeklyDay();
    $end = (clone $start)->modify('+6 day');

    return "du " . ($start->format('n') === $end->format('n')
      ? "{$start->format('d')} au {$end->format('d')} {$this->months[$start->format('n') - 1]}, "
      : "{$start->format('d')} {$this->months[$start->format('n') - 1]} au 
         {$end->format('d')} {$this->months[$end->format('n') - 1]}, "
    ) . "{$end->format('Y')}";
  }

  public function getMonthlyDate(): string {
    return $this->months[$this->getStartingWeeklyDay()->format('n') - 1] . " " . $this->year;
  }

  /**
   * Renvoie si le jour est dans le mois courrant
   * @param \DateTime $date
   * @return bool
   */
  public function withinMonth(\DateTime $date): bool {
    return $this->getStartingMonthlyDay()->format('Y-m') === $date->format('Y-m');
  }

  /**
   * Renvoie le nombre de semaine qu'il y a dans l'année
   * @param int $year Année que l'on cherche le nombre de semaine
   * @return int
   */
  public function getISOWeeksInYear(int $year): int {
    $date = new \DateTime;
    $date->setISODate($year, 53);
    return ($date->format("W") === "53" ? 53 : 52);
  }

  /**
   * Renvoie soit le mois ou la semaine précédente
   * @param bool $isMonth si on retourne 1 mois ou 1 semaine en arrière
   * @return Calendar
   */
  public function next(bool $isMonth): Calendar {
    $date = $this->getStartingWeeklyDay();
    $isMonth ? $date->modify('next month') : $date->modify('+8 days');

    return new Calendar($this->event, $this->timesheet, $this->forms, $date->format('W'), $date->format('o'));
  }

  /**
   * Renvoie soit le mois ou la semaine suivant
   * @param bool $isMonth si on avance de 1 mois ou 1 semaine
   * @return Calendar
   */
  public function prev(bool $isMonth): Calendar {
    $date = $this->getStartingWeeklyDay();
    $isMonth ? $date->modify('last month') : $date;

    return new Calendar($this->event, $this->timesheet, $this->forms, $date->format('W'), $date->format('o'));
  }

  private function setupEvents() {
    $start = $this->getStartingWeeklyDay()->format('U');
    $end = $this->getStartingWeeklyDay()->modify('+6 day -1 minute')->format('U');
    $events = $this->timesheet->get($_SESSION['id'], $start, $end);
    foreach($events as $timesheet) {
      $pos = ((new DateTime)->setTimeStamp(intval($timesheet['at'])/1000)->format('N')) % 7;

      if (isset($this->projects[$pos])) {
        array_push($this->projects[$pos], $timesheet);
      }
    }
  }


  public function draw_monthly_calendar(): string {
    $this->setupEvents();
    
    $prev_href = "/index.php?week={$this->prev(true)->week}&year={$this->prev(true)->year}";
    $next_href = "/index.php?week={$this->next(true)->week}&year={$this->next(true)->year}";

    return "
      <div class='flex-between'>
        <h2 class='m-0'>{$this->getMonthlyDate()}</h2>
        <div class='flex'>
          <a href='{$prev_href}' class='arrow left'><i></i></a>
          <a href='{$next_href}' class='arrow'><i></i></a>
        </div>
      </div>
      <table>
        {$this->draw_monthly_days()}
      </table>
    ";
  }

  public function draw_monthly_days(): string {
    $html = "";

    $start =  clone $this->getStartingMonthlyDay();
    $start = $start->format('N') === '7' 
      ? $start 
      : (clone $this->getStartingMonthlyDay())->modify('saturday last week + 1 day');

    for($i = 0; $i <= $this->getWeeks(); $i++) {
      $current_week = (clone $start)->modify("+" . ($i * 7) . " days")->format('W');
      

      $class = intval($current_week) === $this->week ? "current-week" : "";
      $html .= "<tr class='{$class}'>";
      foreach($this->days as $k => $day) {
        $date = (clone $start)->modify("+" . ($k + ($i - 1) * 7) . " days");
        $isToday = date('Y-m-d') === $date->format('Y-m-d');

        $class = $this->withinMonth($date) ? "" : "calendar__othermonth";
        $class .= $isToday ? " is-today" : "";
        $html .= "<td>";
        $html .= $i === 0 
          ? "<div class='calendar__weekday'>{$day}</div>" 
          : "<div data-week='{$date->format('W')}' data-year='{$date->format('o')}' class='calendar__day {$class}'>{$date->format('d')}</div>";
        $html .= "</td>";
      }
      $html .= "</tr>";
    }

    return $html;
  }


  public function draw_weekly_calendar(): string {
    $prev_href = "/index.php?week={$this->prev(false)->week}&year={$this->prev(false)->year}";
    $next_href = "/index.php?week={$this->next(false)->week}&year={$this->next(false)->year}";

    return "
      <div style='position: relative'>
        <div class='wrapper-hidden'>
          <div class='flex-align-center'>
            <h2>{$this->getWeeklyDate()}</h2>
            <div class='flex ml-2'>
              <a href='{$prev_href}' class='arrow left'><i></i></a>
              <a href='{$next_href}' class='arrow'><i></i></a>
            </div>
          </div>

          {$this->draw_weekly_days()}
        </div>
        <div id='print-btn'>
          <i class='fas fa-print'></i>
        </div>
        {$this->forms->draw_timesheet_form('ajout-timesheet', $this->event->get($_SESSION['id'], $this->getStartingWeeklyDay()->format('U')))}
      </div>
    ";
  }

  public function draw_weekly_days(): string {
    $html_hours = "";
    foreach (range(6, 23) as $hour) {
      $html_hours .= <<<HTML
        <li class='schedule__row'>
          <span>{$this->format_date($hour)}</span>
        </li>
      HTML;
    }

    $html_days = "";
    $start = $this->getStartingWeeklyDay();

    foreach (range(0, 6) as $day) {
      $html_daily_events = "";
      foreach($this->projects[$day] as $project) {
        $html_daily_events .= "
          <li
            class='event-card'
            style='{$this->generate_style_event(floatval($project['start']), floatval($project['end']), $project['color'])}'
            data-id='{$project['id']}'
          >
            <div class='event-card-wrapper'>
              {$project['event_title']}<br/>
              {$this->format_date($project['start'], true)}-
              {$this->format_date($project['end'], true)}
              <i class='delete-btn fas fa-ban'></i>
            </div>
          </li>
        ";
      }

      $html_days.= <<<HTML
        <li class="schedule__group">
          <div class="flex-center daily-title" style="height: 54px">
            <span>{$this->days[$day]} {$start->format('d')}</span>
          </div>
          <ul class="h-100 event-list" data-date="{$start->format('Y-m-d')}">
            {$html_daily_events}
          </ul>
        </li>
      HTML;

      $start->modify('+1 day');
    }

    // ob_start();
    // require_once(dirname(__FILE__) . '/actions/edit.php');

    // $form = ob_get_contents(); 

    return <<<HTML
      <div class='schedule__events'>
        <div class='scroll'>
        
          <div style='position: relative'>
            <ul class='py-30'>
              {$html_hours}
            </ul>
            <ul id="week-calendar" class='ml-60 z-10' style='align-items: stretch'>
              <!-- <div class='cursor'></div> -->
              {$html_days}
            </ul>
          </div>
        </div>
      </div>
    HTML;
  }


  // ===========================================================
  // ========================== UTILS ==========================
  // ===========================================================

  /**
   * generate_style_event
   * -----------------------------------
   * génère les informations de la balise style 
   * de l'évennement ciblé
   */
  function generate_style_event(float $start_time, float $end_time, string $color = ""): string {
    $color = str_replace("O", "0", $color);
    $top_position = (string)(($start_time - 6.0) * 100.0 / (24.0 - 6.0));
    $height = (string)(($end_time - $start_time) * 100.0 / (24.0 - 6.0));

    $elapsed_time = $end_time - $start_time;
    
    $style = "
      background: radial-gradient(1019.19% 203.34% at 50% -6.24%, #FEFEFE 29.91%, {$color} 100%), #F0F0F0;
      top: {$top_position}%;
      height: {$height}%;
      box-shadow: 0px 7px 24px rgba(0, 0, 0, 0.24);
      border-radius: 5px 5px 14px 14px;

      --color-triangle: {$color};
    ";
    
    return $style;
  }

  //  
  /**
   * prend une valeur hexa et retourne sous le forma rgba()
   * @param string $hex Couleur en hexa
   * @param float $alpha 
   * @return string
   */
  private function rgba(string $hex, float $alpha): string {
    list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
    return "rgba(" . $r . "," . $g . "," . $b . "," . $alpha . ")";
  }

  // formatte l'heure pour retourner sour format hh:mm
  function format_date($date, ?bool $is24Hours = false) {
    $hour = floor($date) < 10 ? '0'.floor($date) : floor($date);
    $minute = ($date - $hour) * 60 < 10 ? '0'.round(($date - $hour) * 60) : round(($date - $hour) * 60);

    if ($is24Hours) {
      $fix = $hour >= 12 ? 'PM' : 'AM';

      $hour = $hour > 12 ? $hour - 12 : $hour;

      return $hour.":".$minute." ".$fix;
    }
    return $hour.":".$minute;
  }
}
?>
