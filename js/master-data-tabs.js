/**
 * Master Data – Tab switching + Tables zone filter
 * Purely progressive enhancement; the active tab is already set server-side.
 */
(function () {
  /* ── Tab switching ─────────────────────────────────────── */
  var tabList   = document.getElementById('mdTabs');
  var allBtns   = tabList  ? tabList.querySelectorAll('[data-tab]') : [];
  var allPanels = document.querySelectorAll('.md-tab-panel');

  function activate(tabKey) {
    allBtns.forEach(function (btn) {
      var isActive = btn.dataset.tab === tabKey;
      btn.classList.toggle('md-tab-btn--active', isActive);
      btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
    allPanels.forEach(function (panel) {
      panel.classList.toggle('md-tab-panel--active', panel.id === 'mdpanel-' + tabKey);
    });
    sessionStorage.setItem('mdActiveTab', tabKey);
  }

  var serverActive = tabList && tabList.querySelector('.md-tab-btn--active');
  var stored = sessionStorage.getItem('mdActiveTab');
  if (stored && serverActive && !serverActive.classList.contains('md-tab-btn--active')) {
    activate(stored);
  }

  allBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      activate(btn.dataset.tab);
    });
  });

  /* ── Tables: zone filter ───────────────────────────────── */
  var zoneFilter = document.getElementById('tableZoneFilter');
  var tableBody  = document.getElementById('tablesTbody');

  function filterTablesByZone() {
    if (!zoneFilter || !tableBody) return;
    var selectedZoneId = zoneFilter.value; // '' = All
    var rows = tableBody.querySelectorAll('tr');
    rows.forEach(function (row) {
      if (!selectedZoneId) {
        row.classList.remove('md-row-hidden');
      } else {
        // Each row's first <select> holds the zone_id value
        var sel = row.querySelector('select[name="zone_id"]');
        var match = sel && sel.value === selectedZoneId;
        row.classList.toggle('md-row-hidden', !match);
      }
    });
    // Update the empty-state message visibility
    var visibleRows = tableBody.querySelectorAll('tr:not(.md-row-hidden)');
    var emptyMsg = document.getElementById('tablesEmptyMsg');
    if (emptyMsg) {
      emptyMsg.classList.toggle('md-row-hidden', visibleRows.length > 0);
    }
    // Persist filter choice
    if (selectedZoneId) {
      sessionStorage.setItem('mdTableZone', selectedZoneId);
    } else {
      sessionStorage.removeItem('mdTableZone');
    }
  }

  if (zoneFilter) {
    zoneFilter.addEventListener('change', filterTablesByZone);

    // Restore zone filter from sessionStorage
    var savedZone = sessionStorage.getItem('mdTableZone');
    if (savedZone) {
      zoneFilter.value = savedZone;
      filterTablesByZone();
    }
  }
})();
