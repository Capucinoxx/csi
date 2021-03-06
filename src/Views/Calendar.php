<div class="flex wrapper">
  <div class="flex-x p-2">
    <section class="controls-panel">
      <div class="panel-option">
        <button id="btn-trigger-timesheet" data-modal="ajout-timesheet">
          <i class="fas fa-plus"></i>
          Ajout
        </button>
      </div>
      <?php if (isset($_SESSION['role']) && filter_var($_SESSION['role'], FILTER_VALIDATE_BOOLEAN)): ?>
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
      <?php endif; ?>
    </section>
    <div class="toto-tata">
    <?= $calendar->draw_monthly_calendar() ?>
    </div>
      <span class="employee-name">
        <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] ?>
        <div class="remaining-hours">
          <span style="margin-left: -10px; font-weight: 600;">Heures restantes</span>
          <div style="margin-left: -10px; height: 1px; width: 90%; background: currentColor;"></div>
          <?php 
            foreach($ILeave->getRemainingHours($_SESSION['id'], $calendar->getStartingWeeklyDay()->format('U')) as $key => $value) {
              echo <<<HTML
               <div>
                  <div>{$key}</div>
                  <div>{$value['Heures restantes']} h</div>
               </div>
              HTML;
            }
          ?>
        </div>
      </span>
    <div class="flex-y-end" style="flex: 1 1 auto">
      <div class="panel-option">
        <button onClick="logout()">
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

<?php if (isset($_SESSION['role']) && filter_var($_SESSION['role'], FILTER_VALIDATE_BOOLEAN)):?>
  <?= $forms->draw("libellé", "gestion-labels") ?>
  <?= $forms->draw("employé", "gestion-users") ?>
  <?= $forms->draw("projet", "gestion-projects") ?>
<?php endif; ?>

