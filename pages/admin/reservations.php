<?php
/**
 * Admin Reservations Manager — pages/admin/reservations.php
 */

$pageTitle        = 'Reservations — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'reservations';
$basePath         = '../../';

include '../../includes/header.php';

$reservations = [
  ['id'=>'EU-089','guest'=>'Jane Doe',      'email'=>'jane@example.com', 'zone'=>'Patio',       'seating'=>'Garden View',  'date'=>'24 Mar 2026','time'=>'7:00 PM','guests'=>2,'status'=>'confirmed'],
  ['id'=>'EU-090','guest'=>'Marco Reyes',   'email'=>'marco@example.com','zone'=>'Dining Room', 'seating'=>'Window Table', 'date'=>'24 Mar 2026','time'=>'8:30 PM','guests'=>4,'status'=>'pending'],
  ['id'=>'EU-091','guest'=>'Priya Sharma',  'email'=>'priya@example.com','zone'=>'Bar',         'seating'=>'Bar Counter',  'date'=>'25 Mar 2026','time'=>'6:00 PM','guests'=>3,'status'=>'pending'],
  ['id'=>'EU-092','guest'=>'Thomas Kline',  'email'=>'thomas@example.com','zone'=>'Patio',      'seating'=>'Olive Grove',  'date'=>'25 Mar 2026','time'=>'7:30 PM','guests'=>6,'status'=>'confirmed'],
  ['id'=>'EU-093','guest'=>'Chloe Martin',  'email'=>'chloe@example.com','zone'=>'Dining Room', 'seating'=>'Banquette',    'date'=>'26 Mar 2026','time'=>'8:00 PM','guests'=>2,'status'=>'cancelled'],
  ['id'=>'EU-094','guest'=>'Akira Tanaka',  'email'=>'akira@example.com','zone'=>'Bar',         'seating'=>'Lounge Booths','date'=>'26 Mar 2026','time'=>'9:00 PM','guests'=>2,'status'=>'pending'],
  ['id'=>'EU-095','guest'=>'Sofia Rossi',   'email'=>'sofia@example.com','zone'=>'Patio',       'seating'=>'Pergola',      'date'=>'27 Mar 2026','time'=>'7:00 PM','guests'=>4,'status'=>'confirmed'],
  ['id'=>'EU-096','guest'=>'James Okafor',  'email'=>'james@example.com','zone'=>'Dining Room', 'seating'=>'Fireplace',    'date'=>'27 Mar 2026','time'=>'6:30 PM','guests'=>4,'status'=>'pending'],
  ['id'=>'EU-097','guest'=>'Luna Park',     'email'=>'luna@example.com', 'zone'=>'Bar',         'seating'=>'High Tops',    'date'=>'28 Mar 2026','time'=>'8:00 PM','guests'=>2,'status'=>'confirmed'],
  ['id'=>'EU-098','guest'=>'Carlos Vega',   'email'=>'carlos@example.com','zone'=>'Patio',      'seating'=>'Fountain Side','date'=>'28 Mar 2026','time'=>'7:30 PM','guests'=>2,'status'=>'cancelled'],
];

$byStatus = ['pending'=>[],'confirmed'=>[],'cancelled'=>[]];
foreach ($reservations as $r) { $byStatus[$r['status']][] = $r; }
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">

      <header class="admin-header">
        <div class="admin-header-row">
          <div>
            <h1 class="admin-page-title">Reservations</h1>
            <p class="admin-page-subtitle">Review, approve, and manage all guest reservations.</p>
          </div>
        </div>
      </header>

      <div class="admin-section">

        <!-- Filter Bar -->
        <div class="admin-filter-bar">
          <input type="text" class="admin-search-input" placeholder="Search guest, zone, or ID…" data-search-input="resTable" id="resSearch">
        </div>

        <!-- Tabs -->
        <div class="admin-tabs" data-admin-tabs>
          <?php foreach (['pending'=>'Pending','confirmed'=>'Confirmed','cancelled'=>'Cancelled'] as $key=>$label): ?>
          <button class="admin-tab <?= $key === 'pending' ? 'active' : '' ?>" data-tab="<?= $key ?>" aria-selected="<?= $key === 'pending' ? 'true' : 'false' ?>">
            <?= $label ?> <span class="admin-tab-count">(<?= count($byStatus[$key]) ?>)</span>
          </button>
          <?php endforeach; ?>
        </div>

        <!-- Panels -->
        <?php foreach ($byStatus as $status => $rows): ?>
        <div class="admin-panel <?= $status === 'pending' ? 'active' : '' ?>" data-panel="<?= $status ?>">
          <div class="admin-table-wrap">
            <table class="admin-table resTable">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Guest</th>
                  <th>Zone</th>
                  <th>Seating</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Guests</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($rows as $r):
                  $badgeClass = $r['status'] === 'confirmed' ? 'badge-confirmed' : ($r['status'] === 'pending' ? 'badge-pending' : 'badge-cancelled');
                ?>
                <tr>
                  <td style="font-size:var(--text-xs);color:var(--clr-muted-fg)"><?= $r['id'] ?></td>
                  <td>
                    <span class="admin-guest-name"><?= $r['guest'] ?></span>
                    <br><span class="admin-guest-email"><?= $r['email'] ?></span>
                  </td>
                  <td><?= $r['zone'] ?></td>
                  <td><?= $r['seating'] ?></td>
                  <td><?= $r['date'] ?></td>
                  <td><?= $r['time'] ?></td>
                  <td><?= $r['guests'] ?></td>
                  <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($r['status']) ?></span></td>
                  <td class="admin-row-actions">
                    <button class="btn btn-outline btn-sm" data-open-detail data-modal-open="reservationDetailModal">View</button>
                    <?php if ($r['status'] === 'pending'): ?>
                    <button class="btn btn-primary btn-sm" data-confirm-action="approve">Approve</button>
                    <button class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);" data-confirm-action="reject">Reject</button>
                    <?php elseif ($r['status'] === 'confirmed'): ?>
                    <button class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);" data-confirm-action="reject">Cancel</button>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                <tr><td colspan="9" style="text-align:center;color:var(--clr-muted-fg);padding:var(--space-8);">No reservations in this category.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endforeach; ?>

      </div>

    </div>
  </main>

</div>

<!-- Reservation Detail Modal -->
<div class="admin-modal" id="reservationDetailModal">
  <div class="admin-modal-card">
    <div class="admin-modal-header">
      <h2 class="admin-modal-title">Reservation Detail</h2>
      <button class="admin-modal-close" data-modal-close aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <dl class="admin-modal-rows">
      <div class="admin-modal-row"><dt>Reservation ID</dt><dd id="detailId">—</dd></div>
      <div class="admin-modal-row"><dt>Guest</dt><dd id="detailGuest">—</dd></div>
      <div class="admin-modal-row"><dt>Dining Zone</dt><dd id="detailZone">—</dd></div>
      <div class="admin-modal-row"><dt>Seating</dt><dd id="detailSeating">—</dd></div>
      <div class="admin-modal-row"><dt>Date</dt><dd id="detailDate">—</dd></div>
      <div class="admin-modal-row"><dt>Time</dt><dd id="detailTime">—</dd></div>
      <div class="admin-modal-row"><dt>Party Size</dt><dd id="detailGuests">—</dd></div>
      <div class="admin-modal-row"><dt>Status</dt><dd><span class="badge badge-pending" id="detailStatus">—</span></dd></div>
    </dl>
    <div style="margin-top:var(--space-5);">
      <label style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--clr-fg);margin-bottom:var(--space-2);">Internal Notes</label>
      <textarea rows="3" style="width:100%;padding:var(--space-3) var(--space-4);border:1px solid var(--clr-border);border-radius:var(--radius-lg);font-size:var(--text-sm);color:var(--clr-fg);background:var(--clr-bg);resize:vertical;" placeholder="Add internal notes here…"></textarea>
    </div>
    <div class="admin-modal-footer">
      <button class="btn btn-outline" data-modal-close>Close</button>
    </div>
  </div>
</div>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>
