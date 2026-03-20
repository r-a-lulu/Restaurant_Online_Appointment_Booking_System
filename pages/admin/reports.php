<?php
/**
 * Admin Reports & Analytics — pages/admin/reports.php
 */

$pageTitle        = 'Reports — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'reports';
$basePath         = '../../';

include '../../includes/header.php';

// Mock datasets customized to match the new UI references exactly.
$daily_stats = [
  ['day'=>'Mon', 'val'=>18, 'guests'=>52],
  ['day'=>'Tue', 'val'=>22, 'guests'=>68],
  ['day'=>'Wed', 'val'=>25, 'guests'=>78],
  ['day'=>'Thu', 'val'=>28, 'guests'=>85],
  ['day'=>'Fri', 'val'=>42, 'guests'=>128],
  ['day'=>'Sat', 'val'=>48, 'guests'=>152],
  ['day'=>'Sun', 'val'=>32, 'guests'=>98],
];

$peak_hours = [
  ['time'=>'5:00', 'val'=>8],
  ['time'=>'5:30', 'val'=>12],
  ['time'=>'6:00', 'val'=>18],
  ['time'=>'6:30', 'val'=>22],
  ['time'=>'7:00', 'val'=>32],
  ['time'=>'7:30', 'val'=>38],
  ['time'=>'8:00', 'val'=>28],
  ['time'=>'8:30', 'val'=>18],
  ['time'=>'9:00', 'val'=>12],
  ['time'=>'9:30', 'val'=>6],
];
$max_peak = 38;

$trends = [
  ['month'=>'Sep', 'val'=>480],
  ['month'=>'Oct', 'val'=>520],
  ['month'=>'Nov', 'val'=>610],
  ['month'=>'Dec', 'val'=>750],
  ['month'=>'Jan', 'val'=>580],
  ['month'=>'Feb', 'val'=>620],
  ['month'=>'Mar', 'val'=>680],
];
$max_trend = 750;

$zones = [
  ['name'=>'Main Dining Room', 'pct'=>55, 'res'=>145],
  ['name'=>'The Patio', 'pct'=>27, 'res'=>72],
  ['name'=>'The Bar', 'pct'=>18, 'res'=>48],
];
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">

      <!-- Header -->
      <header class="admin-header">
        <div class="admin-header-row" style="display:flex; justify-content:space-between; align-items:flex-start;">
          <div>
            <h1 class="admin-page-title">Reports</h1>
            <p class="admin-page-subtitle" style="font-size:var(--text-base); color:var(--clr-muted-fg); margin-top:var(--space-1);">Analytics and insights for your restaurant</p>
          </div>
          <div style="display:flex; gap:var(--space-4);">
             <div style="position:relative;">
                <select class="form-select" style="min-width:140px; background-color:var(--clr-card); font-family:var(--font-sans); appearance:none; padding-right:2.5rem;">
                  <option>This Week</option>
                  <option>This Month</option>
                  <option>This Year</option>
                </select>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); pointer-events:none; color:#78716c;"><polyline points="6 9 12 15 18 9"/></svg>
             </div>
             <button class="btn btn-outline" style="display:flex; align-items:center; gap:var(--space-2); background-color:var(--clr-card);">
               <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
               Export
             </button>
          </div>
        </div>
      </header>

      <!-- 4 Top Cards -->
      <div class="reports-stats-grid">
        <div class="report-stat-card">
          <div class="r-stat-top">
             <span class="r-stat-title">Total Reservations</span>
             <div class="r-stat-icon-wrap"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg></div>
          </div>
          <div class="r-stat-val">265</div>
          <div class="r-stat-trend up">+12% vs last week</div>
        </div>
        <div class="report-stat-card">
          <div class="r-stat-top">
             <span class="r-stat-title">Total Guests</span>
             <div class="r-stat-icon-wrap"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
          </div>
          <div class="r-stat-val">661</div>
          <div class="r-stat-trend up">+8% vs last week</div>
        </div>
        <div class="report-stat-card">
          <div class="r-stat-top">
             <span class="r-stat-title">Avg. Party Size</span>
             <div class="r-stat-icon-wrap"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg></div>
          </div>
          <div class="r-stat-val">2.5</div>
          <div class="r-stat-trend neutral">No change</div>
        </div>
        <div class="report-stat-card">
          <div class="r-stat-top">
             <span class="r-stat-title">Peak Hour</span>
             <div class="r-stat-icon-wrap"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
          </div>
          <div class="r-stat-val">7:30</div>
          <div class="r-stat-sub">PM, consistently</div>
        </div>
      </div>

      <!-- Segmented Tabs Navigation -->
      <div class="segmented-tabs" data-admin-tabs style="margin-top:var(--space-8); width:100%; max-width:500px; display:flex;">
        <button class="segment-btn active" data-tab="daily" aria-selected="true" style="flex:1; white-space:nowrap;">Daily</button>
        <button class="segment-btn" data-tab="peak" aria-selected="false" style="flex:1; white-space:nowrap;">Peak Hours</button>
        <button class="segment-btn" data-tab="trends" aria-selected="false" style="flex:1; white-space:nowrap;">Trends</button>
      </div>

      <!-- Tab Panels Wrapper -->
      <div class="report-panels-wrap">

        <!-- PANEL: Daily -->
        <div class="admin-panel active" data-panel="daily">
           <div class="daily-split">
             <!-- Left Box -->
             <div class="report-card">
               <div class="report-card-title">Daily Reservations</div>
               <div class="daily-chart-list">
                  <?php foreach($daily_stats as $d): ?>
                  <div class="daily-chart-row">
                     <div class="dc-label"><?= $d['day'] ?></div>
                     <div class="dc-track-wrap">
                        <div class="dc-track">
                           <div class="dc-fill" style="width: <?= ($d['val'] / 48) * 100 ?>%;">
                              <span class="dc-fill-val"><?= $d['val'] ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="dc-guests">
                        <span class="dc-g-num"><?= $d['guests'] ?></span><br>
                        <span class="dc-g-lbl">guests</span>
                     </div>
                  </div>
                  <?php endforeach; ?>
               </div>
             </div>
             
             <!-- Right Box -->
             <div class="report-card">
               <div class="report-card-title">Zone Breakdown</div>
               <div class="zone-breakdown-list">
                  <?php foreach($zones as $z): ?>
                  <div class="zb-item">
                     <div class="zb-top">
                        <span class="zb-name"><?= $z['name'] ?></span>
                        <span class="zb-pct"><?= $z['pct'] ?>%</span>
                     </div>
                     <div class="zb-track">
                        <div class="zb-fill" style="width:<?= $z['pct'] ?>%"></div>
                     </div>
                     <div class="zb-btm"><?= $z['res'] ?> reservations</div>
                  </div>
                  <?php endforeach; ?>
               </div>
             </div>
           </div>
        </div>

        <!-- PANEL: Peak Hours -->
        <div class="admin-panel" data-panel="peak">
           <div class="report-card">
             <div class="report-card-title">Reservations by Hour</div>
             <div class="peak-bars-wrap">
                <?php foreach($peak_hours as $p): ?>
                <div class="peak-bar-col">
                   <div class="peak-b-val"><?= $p['val'] ?></div>
                   <div class="peak-b-track">
                      <div class="peak-b-fill" style="height:<?= ($p['val']/$max_peak)*100 ?>%"></div>
                   </div>
                   <div class="peak-b-label"><?= $p['time'] ?></div>
                </div>
                <?php endforeach; ?>
             </div>
             <div class="peak-footer-msg">Peak dining hours are between 7:00 PM and 8:00 PM</div>
           </div>
        </div>

        <!-- PANEL: Trends -->
        <div class="admin-panel" data-panel="trends">
           <!-- Top Block -->
           <div class="report-card" style="margin-bottom:var(--space-6);">
             <div class="report-card-title">Monthly Reservation Trends</div>
             <div class="trend-bars-wrap">
                <?php foreach($trends as $t): ?>
                <div class="trend-bar-col">
                   <span class="trend-b-val"><?= $t['val'] ?></span>
                   <div class="trend-b-track">
                      <div class="trend-b-fill" style="height:<?= ($t['val']/$max_trend)*100 ?>%"></div>
                   </div>
                   <span class="trend-b-label"><?= $t['month'] ?></span>
                </div>
                <?php endforeach; ?>
             </div>
             <div class="trend-msg-box">
                <div class="tmb-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg> Overall Growth</div>
                <div class="tmb-desc">Reservations have increased by 42% over the past 6 months, with December showing the highest volume due to holiday season.</div>
             </div>
           </div>
           
           <!-- Bottom Two Blocks -->
           <div class="reports-2col-grid">
             <!-- Left -->
             <div class="report-card">
               <div class="report-card-title">Top Performing Days</div>
               <div class="top-days-list">
                 <div class="top-day-row">
                    <span class="td-name">Saturday</span>
                    <span class="td-val">~48 reservations/day</span>
                 </div>
                 <div class="top-day-row">
                    <span class="td-name">Friday</span>
                    <span class="td-val">~42 reservations/day</span>
                 </div>
                 <div class="top-day-row">
                    <span class="td-name">Sunday</span>
                    <span class="td-val">~32 reservations/day</span>
                 </div>
               </div>
             </div>
             <!-- Right -->
             <div class="report-card">
               <div class="report-card-title">Cancellation Rate</div>
               <div class="cancel-rate-content">
                 <div class="cr-big-val">4.2%</div>
                 <div class="cr-lbl">Average cancellation rate</div>
                 <div class="cr-sub">Below Industry average of 8%</div>
                 <div class="cr-warning-box">
                    Most cancellations occur within 24 hours of the reservation time. Consider implementing a confirmation reminder system.
                 </div>
               </div>
             </div>
           </div>
        </div>

      </div><!-- End Panels -->

    </div>
  </main>
</div>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>
