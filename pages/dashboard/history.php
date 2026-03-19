<?php
/**
 * Dashboard Dining History — pages/dashboard/history.php
 */

$pageTitle       = 'Dining History';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'history';
$basePath        = '../../';

include '../../includes/header.php';

$history = [
  ['day'=>'18','month'=>'Feb','zone'=>'Patio','detail'=>'Outdoor Seating','time'=>'7:00 PM','guests'=>2,'rating'=>5,'note'=>'Anniversary dinner — perfect evening.'],
  ['day'=>'04','month'=>'Jan','zone'=>'Dining Room','detail'=>'Chef\'s Table','time'=>'8:00 PM','guests'=>4,'rating'=>4,'note'=>'Tasting menu was exceptional.'],
  ['day'=>'20','month'=>'Dec','zone'=>'Bar','detail'=>'Cocktail Lounge','time'=>'9:00 PM','guests'=>2,'rating'=>5,'note'=>'Best old fashioned I\'ve ever had.'],
  ['day'=>'12','month'=>'Nov','zone'=>'Patio','detail'=>'Terrace Table','time'=>'6:30 PM','guests'=>2,'rating'=>4,'note'=>''],
  ['day'=>'28','month'=>'Oct','zone'=>'Dining Room','detail'=>'Window Table','time'=>'7:30 PM','guests'=>3,'rating'=>5,'note'=>'Impeccable service.'],
];
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <main class="dashboard-main">
    <div class="dashboard-content">

      <header class="dashboard-header">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">Dining History</h1>
            <p class="dashboard-page-subtitle">A record of all your past visits at Eudaimonia.</p>
          </div>
        </div>
      </header>

      <div class="dash-section">
        <div class="dash-section-header">
          <h2 class="dash-section-title">Past Visits</h2>
          <span class="badge badge-secondary"><?= count($history) ?> visits</span>
        </div>

        <?php if (empty($history)): ?>
        <div class="empty-state">
          <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <h3 class="empty-state-title">No dining history yet</h3>
          <p class="empty-state-text">Your completed reservations will appear here after your first visit.</p>
        </div>
        <?php else: ?>

        <div class="reservation-list">
          <?php foreach ($history as $visit): ?>
          <div class="history-row">
            <div class="reservation-date-block">
              <span class="reservation-date-day"><?= $visit['day'] ?></span>
              <span class="reservation-date-month"><?= $visit['month'] ?></span>
            </div>
            <div class="history-body">
              <p class="history-zone"><?= htmlspecialchars($visit['zone']) ?> — <?= htmlspecialchars($visit['detail']) ?></p>
              <p class="history-meta"><?= $visit['time'] ?> &nbsp;·&nbsp; <?= $visit['guests'] ?> Guests</p>
              <!-- Star rating (display) -->
              <div class="rating-stars rating-interactive">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <svg class="rating-star <?= $i <= $visit['rating'] ? 'filled' : '' ?>"
                     viewBox="0 0 24 24" fill="<?= $i <= $visit['rating'] ? 'currentColor' : 'none' ?>"
                     stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                <?php endfor; ?>
              </div>
              <?php if ($visit['note']): ?>
              <p style="font-size: var(--text-xs); color: var(--clr-muted-fg); margin-top: var(--space-2); font-style: italic;">
                "<?= htmlspecialchars($visit['note']) ?>"
              </p>
              <?php endif; ?>
            </div>
            <span class="badge badge-confirmed">Completed</span>
          </div>
          <?php endforeach; ?>
        </div>

        <?php endif; ?>
      </div>

    </div><!-- /.dashboard-content -->
  </main>

</div><!-- /.dashboard-layout -->

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
