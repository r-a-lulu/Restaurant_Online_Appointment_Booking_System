<?php
/**
 * Admin Reports & Analytics — pages/admin/reports.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_admin();

$pageTitle        = 'Reports — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'reports';
$basePath         = '../../';

$adminError = get_flash('admin_error');

$daily_stats = [];
$peak_hours = [];
$trends = [];
$zones = [];
$summary = [
  'total_res' => 0,
  'total_guests' => 0,
  'avg_party' => 0,
  'peak_hour' => '—',
  'cancel_rate' => '0.0',
];

try {
  $pdo = db();

  $stmt = $pdo->prepare('SELECT appointment_date, COUNT(*) AS val, COALESCE(SUM(party_size),0) AS guests FROM appointments WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY appointment_date ORDER BY appointment_date');
  $stmt->execute();
  $rows = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $map = [];
  foreach ($rows as $r) {
    $map[$r['appointment_date']] = $r;
  }
  for ($i = 6; $i >= 0; $i--) {
    $date = (new DateTime())->modify('-' . $i . ' days')->format('Y-m-d');
    $label = date('D', strtotime($date));
    $val = isset($map[$date]) ? (int) $map[$date]['val'] : 0;
    $guests = isset($map[$date]) ? (int) $map[$date]['guests'] : 0;
    $daily_stats[] = ['day' => $label, 'val' => $val, 'guests' => $guests];
  }

  $timeSlots = ['17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30'];
  $stmt = $pdo->prepare('SELECT DATE_FORMAT(start_time, "%H:%i") AS slot, COUNT(*) AS val FROM appointments WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY slot');
  $stmt->execute();
  $rows = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $slotMap = [];
  foreach ($rows as $r) {
    $slotMap[$r['slot']] = (int) $r['val'];
  }
  foreach ($timeSlots as $slot) {
    $val = $slotMap[$slot] ?? 0;
    if ($val > 0 && ($summary['peak_hour'] === '—' || $val > ($slotMap[$summary['peak_hour']] ?? 0))) {
      $summary['peak_hour'] = $slot;
    }
    $peak_hours[] = ['time' => $slot, 'val' => $val];
  }

  $stmt = $pdo->prepare('SELECT DATE_FORMAT(appointment_date, "%b") AS month, COUNT(*) AS val, YEAR(appointment_date) AS yr, MONTH(appointment_date) AS mo FROM appointments WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY yr, mo ORDER BY yr, mo');
  $stmt->execute();
  $rows = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  foreach ($rows as $r) {
    $val = (int) $r['val'];
    $trends[] = ['month' => $r['month'], 'val' => $val];
  }

  $stmt = $pdo->prepare('SELECT dz.zone_name, COUNT(*) AS res FROM appointments a LEFT JOIN `tables` t ON t.table_id = a.table_id JOIN dining_zones dz ON dz.zone_id = COALESCE(a.zone_id, t.zone_id) WHERE a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY dz.zone_id');
  $stmt->execute();
  $rows = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $totalZone = 0;
  foreach ($rows as $r) { $totalZone += (int) $r['res']; }
  foreach ($rows as $r) {
    $pct = $totalZone > 0 ? (int) round(((int) $r['res'] / $totalZone) * 100) : 0;
    $zones[] = ['name' => $r['zone_name'], 'pct' => $pct, 'res' => (int) $r['res']];
  }

  $stmt = $pdo->prepare('SELECT COUNT(*) AS total_res, COALESCE(SUM(party_size),0) AS total_guests, COALESCE(AVG(party_size),0) AS avg_party FROM appointments WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)');
  $stmt->execute();
  $row = $stmt->fetch() ?: [];
  $stmt->closeCursor();

  $summary['total_res'] = (int) ($row['total_res'] ?? 0);
  $summary['total_guests'] = (int) ($row['total_guests'] ?? 0);
  $summary['avg_party'] = number_format((float) ($row['avg_party'] ?? 0), 1);

  $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments a JOIN appointment_status s ON s.status_id = a.status_id WHERE a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND s.status_name = 'cancelled'");
  $stmt->execute();
  $cancelled = (int) $stmt->fetchColumn();
  $stmt->closeCursor();
  $summary['cancel_rate'] = $summary['total_res'] > 0 ? number_format(($cancelled / $summary['total_res']) * 100, 1) : '0.0';

  if ($summary['peak_hour'] !== '—') {
    $summary['peak_hour'] = date('g:i', strtotime($summary['peak_hour']));
  }
} catch (PDOException $e) {
  $adminError = safe_error_message($e);
}

include '../../includes/header.php';
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">

      <?php if ($adminError): ?>
        <div class="auth-alert"><span><?= e($adminError) ?></span></div>
      <?php endif; ?>

      <header class="admin-header">
        <div class="admin-header-row" style="display:flex; justify-content:space-between; align-items:flex-start;">
          <div>
            <h1 class="admin-page-title">Reports</h1>
            <p class="admin-page-subtitle" style="font-size:var(--text-base); color:var(--clr-muted-fg); margin-top:var(--space-1);">Analytics and insights for your restaurant</p>
          </div>
        </div>
      </header>

      <div class="reports-stats-grid">
        <div class="report-stat-card">
          <div class="r-stat-top">
             <span class="r-stat-title">Total Reservations</span>
             <div class="r-stat-icon-wrap"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg></div>
          </div>
          <div class="r-stat-val"><?= e((string) $summary['total_res']) ?></div>
          <div class="r-stat-trend up">Last 7 days</div>
        </div>
        <div class="report-stat-card">
          <div class="r-stat-top">
             <span class="r-stat-title">Total Guests</span>
             <div class="r-stat-icon-wrap"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
          </div>
          <div class="r-stat-val"><?= e((string) $summary['total_guests']) ?></div>
          <div class="r-stat-trend up">Last 7 days</div>
        </div>
        <div class="report-stat-card">
          <div class="r-stat-top">
             <span class="r-stat-title">Avg. Party Size</span>
             <div class="r-stat-icon-wrap"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg></div>
          </div>
          <div class="r-stat-val"><?= e((string) $summary['avg_party']) ?></div>
          <div class="r-stat-trend neutral">Last 7 days</div>
        </div>
        <div class="report-stat-card">
          <div class="r-stat-top">
             <span class="r-stat-title">Peak Hour</span>
             <div class="r-stat-icon-wrap"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
          </div>
          <div class="r-stat-val"><?= e((string) $summary['peak_hour']) ?></div>
          <div class="r-stat-sub">Based on last 30 days</div>
        </div>
      </div>

      <div class="segmented-tabs" data-admin-tabs style="margin-top:var(--space-8); width:100%; max-width:500px; display:flex;">
        <button class="segment-btn active" data-tab="daily" aria-selected="true" style="flex:1; white-space:nowrap;">Daily</button>
        <button class="segment-btn" data-tab="peak" aria-selected="false" style="flex:1; white-space:nowrap;">Peak Hours</button>
        <button class="segment-btn" data-tab="trends" aria-selected="false" style="flex:1; white-space:nowrap;">Trends</button>
      </div>

      <div class="report-panels-wrap">

        <div class="admin-panel active" data-panel="daily">
           <div class="daily-split">
             <div class="report-card">
               <div class="report-card-title">Daily Reservations</div>
               <div class="daily-chart-list">
                  <?php $max_daily = 0; foreach ($daily_stats as $d) { if ($d['val'] > $max_daily) $max_daily = $d['val']; } ?>
                  <?php foreach($daily_stats as $d): ?>
                  <div class="daily-chart-row">
                     <div class="dc-label"><?= e($d['day']) ?></div>
                     <div class="dc-track-wrap">
                        <div class="dc-track">
                           <div class="dc-fill" style="width: <?= $max_daily > 0 ? e((string) (($d['val'] / $max_daily) * 100)) : '0' ?>%;">
                              <span class="dc-fill-val"><?= e((string) $d['val']) ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="dc-guests">
                        <span class="dc-g-num"><?= e((string) $d['guests']) ?></span><br>
                        <span class="dc-g-lbl">guests</span>
                     </div>
                  </div>
                  <?php endforeach; ?>
               </div>
             </div>

             <div class="report-card">
               <div class="report-card-title">Zone Breakdown</div>
               <div class="zone-breakdown-list">
                  <?php foreach($zones as $z): ?>
                  <div class="zb-item">
                     <div class="zb-top">
                        <span class="zb-name"><?= e($z['name']) ?></span>
                        <span class="zb-pct"><?= e((string) $z['pct']) ?>%</span>
                     </div>
                     <div class="zb-track">
                        <div class="zb-fill" style="width:<?= e((string) $z['pct']) ?>%"></div>
                     </div>
                     <div class="zb-btm"><?= e((string) $z['res']) ?> reservations</div>
                  </div>
                  <?php endforeach; ?>
               </div>
             </div>
           </div>
        </div>

        <div class="admin-panel" data-panel="peak">
           <div class="report-card">
             <div class="report-card-title">Reservations by Hour</div>
             <div class="peak-bars-wrap">
                <?php $max_peak = 0; foreach ($peak_hours as $p) { if ($p['val'] > $max_peak) $max_peak = $p['val']; } ?>
                <?php foreach($peak_hours as $p): ?>
                <div class="peak-bar-col">
                   <div class="peak-b-val"><?= e((string) $p['val']) ?></div>
                   <div class="peak-b-track">
                      <div class="peak-b-fill" style="height:<?= $max_peak > 0 ? e((string) (($p['val']/$max_peak)*100)) : '0' ?>%"></div>
                   </div>
                   <div class="peak-b-label"><?= e($p['time']) ?></div>
                </div>
                <?php endforeach; ?>
             </div>
           </div>
        </div>

        <div class="admin-panel" data-panel="trends">
           <div class="report-card" style="margin-bottom:var(--space-6);">
             <div class="report-card-title">Monthly Reservation Trends</div>
             <div class="trend-bars-wrap">
                <?php $max_trend = 0; foreach ($trends as $t) { if ($t['val'] > $max_trend) $max_trend = $t['val']; } ?>
                <?php foreach($trends as $t): ?>
                <div class="trend-bar-col">
                   <span class="trend-b-val"><?= e((string) $t['val']) ?></span>
                   <div class="trend-b-track">
                      <div class="trend-b-fill" style="height:<?= $max_trend > 0 ? e((string) (($t['val']/$max_trend)*100)) : '0' ?>%"></div>
                   </div>
                   <span class="trend-b-label"><?= e($t['month']) ?></span>
                </div>
                <?php endforeach; ?>
             </div>
           </div>

           <div class="reports-2col-grid">
             <div class="report-card">
               <div class="report-card-title">Top Performing Days</div>
               <div class="top-days-list">
                 <?php foreach ($daily_stats as $d): ?>
                 <div class="top-day-row">
                    <span class="td-name"><?= e($d['day']) ?></span>
                    <span class="td-val"><?= e((string) $d['val']) ?> reservations</span>
                 </div>
                 <?php endforeach; ?>
               </div>
             </div>
             <div class="report-card">
               <div class="report-card-title">Cancellation Rate</div>
               <div class="cancel-rate-content">
                 <div class="cr-big-val"><?= e((string) $summary['cancel_rate']) ?>%</div>
                 <div class="cr-lbl">Average cancellation rate</div>
               </div>
             </div>
           </div>
        </div>

      </div>

    </div>
  </main>
</div>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>

