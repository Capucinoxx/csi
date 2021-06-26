<div class="flex wrapper">
  <div class="flex-x p-2">
    <section class="controls-panel">
      <div class="panel-option">
        <button data-modal="ajout-timesheet">
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

<?= $forms->draw("libellé", "gestion-labels") ?>
<?= $forms->draw("employé", "gestion-users") ?>
<?= $forms->draw("projet", "gestion-projects") ?>
<?= $forms->draw_timesheet_form("ajout-timesheet") ?>
