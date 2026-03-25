<?php
/**
 * Booking Wizard — Eudaimonia Restaurant
 * 4-step multi-step reservation form:
 *   Step 1: Guest Info → Step 2: Zone Selection → Step 3: Date & Time → Step 4: Review
 */

$pageTitle   = 'Reserve a Table';
$pageCSS     = ['book.css'];
$currentPage = 'book';
$navStyle    = 'solid';
$basePath    = '../';

require_once '../includes/security.php';
start_secure_session();
require_login();

$bookingError = get_flash('booking_error');
$services = [];
$packages = [];
$addOns = [];
$zones = [];
$user = [];

$zoneMeta = [
  'The Patio' => [
    'image' => 'assets/images/zones/zone-patio.jpg',
    'desc' => 'An enchanting garden courtyard with al-fresco dining beneath open skies and fragrant flora.',
    'capacity' => 'Up to 40 guests',
    'tag' => 'Al fresco',
    'slug' => 'patio',
  ],
  'Main Dining Room' => [
    'image' => 'assets/images/zones/zone-dining.jpg',
    'desc' => 'Our signature indoor space — warm oak panels, candlelit tables, and impeccable service.',
    'capacity' => 'Up to 80 guests',
    'tag' => 'Indoor',
    'slug' => 'dining-room',
  ],
  'The Bar' => [
    'image' => 'assets/images/zones/zone-bar.jpg',
    'desc' => 'A sophisticated cocktail lounge crafted for conversation — curated spirits and creative small plates.',
    'capacity' => 'Up to 30 guests',
    'tag' => 'Lounge',
    'slug' => 'bar',
  ],
];

try {
  $pdo = db();

  $stmt = $pdo->prepare('SELECT first_name, last_name, email, phone FROM users WHERE user_id = :id LIMIT 1');
  $stmt->execute([':id' => (int) $_SESSION['user_id']]);
  $user = $stmt->fetch() ?: [];
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT service_id, service_name, price FROM vw_active_services');
  $stmt->execute();
  $services = $stmt->fetchAll();
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT package_id, package_name, base_price, description FROM vw_active_event_packages');
  $stmt->execute();
  $packages = $stmt->fetchAll();
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT add_on_id, category, name, description, price FROM vw_active_add_ons');
  $stmt->execute();
  $addOns = $stmt->fetchAll();
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT zone_id, zone_name FROM dining_zones ORDER BY zone_name');
  $stmt->execute();
  $zones = $stmt->fetchAll();
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT table_id, table_number, capacity, zone_id, zone_name, seating_preference FROM vw_available_tables');
  $stmt->execute();
  $tables = $stmt->fetchAll();
  $stmt->closeCursor();
} catch (PDOException $e) {
  $bookingError = safe_error_message($e);
}

include '../includes/header.php';
include '../includes/nav.php';
?>

<script>
  window.ALL_TABLES = <?= json_encode($tables) ?>;
</script>

<div class="book-page">

  <!-- ===== HERO STRIP ===== -->
  <section class="book-hero">
    <div class="container book-hero-inner">
      <p class="section-label">Online Reservations</p>
      <h1>Reserve Your Table</h1>
      <p>Complete in just a few steps. We'll confirm your booking by email.</p>
    </div>
  </section>

  <?php if ($bookingError): ?>
    <div class="container" style="margin-top: var(--space-4);">
      <div class="auth-alert">
        <span><?= e($bookingError) ?></span>
      </div>
    </div>
  <?php endif; ?>

  <!-- ===== WIZARD PROGRESS BAR (sticky) ===== -->
  <div class="wizard-progress">
    <div class="wizard-progress-inner">

      <div class="wizard-step active" data-step="1">
        <div class="wizard-step-circle">
          <span class="wizard-step-num">1</span>
          <!-- Check icon shown when completed -->
          <svg class="wizard-step-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <span class="wizard-step-label">Guest Info</span>
      </div>

      <div class="wizard-step" data-step="2">
        <div class="wizard-step-circle">
          <span class="wizard-step-num">2</span>
          <svg class="wizard-step-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <span class="wizard-step-label">Dining Zone</span>
      </div>

      <div class="wizard-step" data-step="3">
        <div class="wizard-step-circle">
          <span class="wizard-step-num">3</span>
          <svg class="wizard-step-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <span class="wizard-step-label">Date & Time</span>
      </div>

      <div class="wizard-step" data-step="4">
        <div class="wizard-step-circle">
          <span class="wizard-step-num">4</span>
          <svg class="wizard-step-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <span class="wizard-step-label">Review</span>
      </div>

    </div>
  </div>

  <!-- ===== WIZARD BODY ===== -->
  <form id="booking-form" method="post" action="<?= $basePath ?>actions.php?action=process_booking" class="wizard-body" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action_token" value="<?= e(action_token('process_booking')) ?>">
    <input type="hidden" id="check-availability-token" value="<?= e(action_token('check_availability')) ?>">
    <input type="hidden" name="service_id" id="service-id">
    <input type="hidden" name="event_package_id" id="event-package-id">
    <input type="hidden" name="zone_id" id="zone-id">
    <input type="hidden" name="table_id" id="table-id">
    <input type="hidden" name="appointment_date" id="appointment-date">
    <input type="hidden" name="start_time" id="start-time">
    <input type="hidden" name="party_size" id="party-size">
    <input type="hidden" name="special_requests" id="special-requests">
    <input type="hidden" name="zone_label" id="zone-label">
    <input type="hidden" name="date_label" id="date-label">
    <input type="hidden" name="time_label" id="time-label">

    <!-- ── LEFT: Step Panels ── -->
    <div class="wizard-main">

      <!-- ════════════════════════════════════════════
           STEP 1 — Guest Information
      ═══════════════════════════════════════════════ -->
      <div class="wizard-step-panel active" id="step-1">
        <div class="wizard-panel-heading">
          <h2>Guest Information</h2>
          <p>Tell us about yourself so we can personalise your dining experience.</p>
        </div>

        <div class="guest-form">

          <!-- Name row -->
          <div class="guest-form-row">
          <div class="form-group">
            <label for="guest-firstname" class="form-label">First Name <span style="color:var(--clr-destructive)">*</span></label>
              <input type="text" id="guest-firstname" name="first_name" class="form-input" placeholder="Jane" autocomplete="given-name" required value="<?= e($user['first_name'] ?? '') ?>" readonly>
            </div>
            <div class="form-group">
              <label for="guest-lastname" class="form-label">Last Name <span style="color:var(--clr-destructive)">*</span></label>
              <input type="text" id="guest-lastname" name="last_name" class="form-input" placeholder="Doe" autocomplete="family-name" required value="<?= e($user['last_name'] ?? '') ?>" readonly>
            </div>
          </div>

          <!-- Email -->
          <div class="form-group">
            <label for="guest-email" class="form-label">Email Address <span style="color:var(--clr-destructive)">*</span></label>
            <input type="email" id="guest-email" name="email" class="form-input" placeholder="you@example.com" autocomplete="email" required value="<?= e($user['email'] ?? '') ?>" readonly>
          </div>

          <!-- Phone -->
          <div class="form-group">
            <label for="guest-phone" class="form-label">Phone <span style="font-weight:400; color:var(--clr-muted-fg)">(optional)</span></label>
            <input type="tel" id="guest-phone" name="phone" class="form-input" placeholder="+1 (555) 000-0000" autocomplete="tel" inputmode="tel" maxlength="30" pattern="[0-9+()\-\s]{6,30}" value="<?= e($user['phone'] ?? '') ?>">
          </div>

          <!-- Party size -->
          <div class="form-group">
            <label for="guest-count" class="form-label">Number of Guests <span style="color:var(--clr-destructive)">*</span></label>
            <select id="guest-count" name="party_size" class="form-select" required>
              <option value="">Select party size</option>
              <option value="1">1 guest</option>
              <option value="2">2 guests</option>
              <option value="3">3 guests</option>
              <option value="4">4 guests</option>
              <option value="5">5 guests</option>
              <option value="6">6 guests</option>
              <option value="7">7 guests</option>
              <option value="8">8 guests</option>
              <option value="8">8 guests</option>
            </select>
          </div>

          <!-- Occasion (optional) -->
          <div class="form-group">
            <label for="guest-occasion" class="form-label">Occasion <span style="font-weight:400; color:var(--clr-muted-fg)">(optional)</span></label>
            <select id="guest-occasion" class="form-select">
              <option value="">None / General Dining</option>
              <option value="Birthday">Birthday Celebration</option>
              <option value="Anniversary">Anniversary</option>
              <option value="Business Dinner">Business Dinner</option>
              <option value="Romantic Dinner">Romantic Dinner</option>
              <option value="Family Gathering">Family Gathering</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="service-select" class="form-label">Service <span style="color:var(--clr-destructive)">*</span></label>
            <select id="service-select" class="form-select">
              <option value="">Select a service</option>
              <?php foreach ($services as $service): ?>
                <option value="<?= e($service['service_id']) ?>" data-price="<?= e(number_format((float) $service['price'], 2)) ?>">
                  <?= e($service['service_name']) ?> (<?= e(number_format((float) $service['price'], 2)) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="package-select" class="form-label">Event Package (optional)</label>
            <select id="package-select" class="form-select">
              <option value="">No package</option>
              <?php foreach ($packages as $package): ?>
                <option value="<?= e($package['package_id']) ?>" data-price="<?= e(number_format((float) $package['base_price'], 2)) ?>">
                  <?= e($package['package_name']) ?> (<?= e(number_format((float) $package['base_price'], 2)) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <p class="text-muted text-sm" style="margin-top: var(--space-2);">
              Choose either a service or a package, not both.
            </p>
          </div>

          <!-- Note -->
          <div class="guest-note">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            A confirmation email with your reservation details will be sent to your email address.
          </div>

        </div><!-- /guest-form -->

        <!-- Navigation -->
        <div class="wizard-nav">
          <div class="wizard-nav-left">
            <button type="button" class="btn btn-outline" id="btn-prev" disabled>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
              Back
            </button>
            <span class="wizard-step-count" id="step-count">Step 1 of 4</span>
          </div>
          <button type="button" class="btn btn-primary btn-lg" id="btn-next">
            Continue
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div><!-- /step-1 -->


      <!-- ════════════════════════════════════════════
           STEP 2 — Dining Zone
      ═══════════════════════════════════════════════ -->
      <div class="wizard-step-panel" id="step-2">
        <div class="wizard-panel-heading">
          <h2>Choose Your Dining Zone</h2>
          <p>Each space offers a distinct atmosphere — select the one that suits your evening.</p>
        </div>

        <div class="zone-cards-grid">
          <?php foreach ($zones as $zone): ?>
            <?php
              $zoneName = $zone['zone_name'];
              $meta = $zoneMeta[$zoneName] ?? $zoneMeta['Main Dining Room'];
              $slug = $meta['slug'] ?? strtolower(preg_replace('/[^a-z0-9]+/i', '-', $zoneName));
            ?>
            <div class="zone-select-card" data-zone="<?= e($slug) ?>" data-zone-id="<?= e($zone['zone_id']) ?>" data-label="<?= e($zoneName) ?>" tabindex="0" role="button" aria-label="Select <?= e($zoneName) ?> dining zone">
              <img src="<?= $basePath . e($meta['image']) ?>" alt="<?= e($zoneName) ?>" class="zone-select-card-img">
              <div class="zone-select-card-body">
                <h3 class="zone-select-card-title"><?= e($zoneName) ?></h3>
                <p class="zone-select-card-desc"><?= e($meta['desc']) ?></p>
                <div class="zone-select-card-meta">
                  <span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <?= e($meta['capacity']) ?>
                  </span>
                  <span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2"/></svg>
                    <?= e($meta['tag']) ?>
                  </span>
                </div>
              </div>
              <div class="zone-select-card-check">
                <div class="zone-check-circle">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

        </div><!-- /zone-cards-grid -->

        <!-- Seating Spot Sub-selection (revealed once zone is chosen) -->
        <div class="seating-reveal" id="seating-reveal">
          <div class="seating-reveal-inner">
            <p class="seating-reveal-label">Seating Preference</p>
            <p class="seating-reveal-title" id="seating-reveal-title">Choose your preferred spot</p>
            <div class="seating-spot-pills" id="seating-spot-pills">
              <!-- Pills injected by book.js based on selected zone and capacity -->
            </div>
            <p class="text-muted text-sm" style="margin-top: var(--space-4);" id="seating-capacity-notice">
              Options are strictly filtered by your party size in this zone.
            </p>
          </div>
        </div>


        <div class="wizard-nav">
          <div class="wizard-nav-left">
            <button type="button" class="btn btn-outline" id="btn-prev">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
              Back
            </button>
            <span class="wizard-step-count" id="step-count">Step 2 of 4</span>
          </div>
          <button type="button" class="btn btn-primary btn-lg" id="btn-next">
            Continue
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </button>
        </div>

      </div><!-- /step-2 -->


      <!-- ════════════════════════════════════════════
           STEP 3 — Date & Time
      ═══════════════════════════════════════════════ -->
      <div class="wizard-step-panel" id="step-3">
        <div class="wizard-panel-heading">
          <h2>Select Date &amp; Time</h2>
          <p>Choose your preferred evening. Available time slots are shown below.</p>
        </div>

        <div class="datetime-grid">

          <!-- Date -->
          <div class="datetime-section">
            <h3>Reservation Date</h3>
            <div class="form-group">
              <label for="book-date" class="form-label">Date <span style="color:var(--clr-destructive)">*</span></label>
              <input type="date" id="book-date" class="form-input" required>
              <p class="date-picker-note">We are open Tuesday – Sunday. Advance booking of at least 24 hours required.</p>
            </div>
          </div>

          <!-- Time slots -->
          <div class="datetime-section">
            <h3>Available Times</h3>
            <div class="time-grid">
              <button type="button" class="time-slot" data-time="17:00">5:00 PM</button>
              <button type="button" class="time-slot" data-time="17:30">5:30 PM</button>
              <button type="button" class="time-slot" data-time="18:00">6:00 PM</button>
              <button type="button" class="time-slot" data-time="18:30">6:30 PM</button>
              <button type="button" class="time-slot" data-time="19:00">7:00 PM</button>
              <button type="button" class="time-slot" data-time="19:30">7:30 PM</button>
              <button type="button" class="time-slot" data-time="20:00">8:00 PM</button>
              <button type="button" class="time-slot" data-time="20:30">8:30 PM</button>
              <button type="button" class="time-slot" data-time="21:00">9:00 PM</button>
              <button type="button" class="time-slot" data-time="21:30">9:30 PM</button>
              <button type="button" class="time-slot" data-time="22:00">10:00 PM</button>
            </div>
            <p class="date-picker-note" style="margin-top:var(--space-2);">
              <span style="opacity:0.5; text-decoration:line-through; margin-right:4px">Strikethrough</span> = unavailable
            </p>
          </div>

        </div><!-- /datetime-grid -->

        <!-- Extras: occasion + requests -->
        <div class="extras-section">
          <div class="form-group">
            <label for="guest-requests" class="form-label">Special Requests <span style="font-weight:400; color:var(--clr-muted-fg)">(optional)</span></label>
            <textarea id="guest-requests" class="form-textarea" placeholder="Dietary restrictions, accessibility needs, seating preferences, special setups…" rows="3" maxlength="500"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Add-ons (optional)</label>
            <div class="checkbox-group">
              <?php foreach ($addOns as $addOn): ?>
                <label class="checkbox-item">
                  <input type="checkbox" name="add_on_ids[]" value="<?= e($addOn['add_on_id']) ?>" data-price="<?= e(number_format((float) $addOn['price'], 2)) ?>" data-name="<?= e($addOn['name']) ?>">
                  <span><?= e($addOn['category']) ?> — <?= e($addOn['name']) ?> (<?= e(number_format((float) $addOn['price'], 2)) ?>)</span>
                  <input
                    type="number"
                    name="add_on_qty[<?= e($addOn['add_on_id']) ?>]"
                    class="form-input"
                    value="1"
                    min="1"
                    max="20"
                    style="max-width: 90px; margin-left: var(--space-3);"
                    aria-label="Quantity for <?= e($addOn['name']) ?>"
                  >
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="wizard-nav">
          <div class="wizard-nav-left">
            <button type="button" class="btn btn-outline" id="btn-prev">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
              Back
            </button>
            <span class="wizard-step-count" id="step-count">Step 3 of 4</span>
          </div>
          <button type="button" class="btn btn-primary btn-lg" id="btn-next">
            Review Reservation
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div><!-- /step-3 -->


      <!-- ════════════════════════════════════════════
           STEP 4 — Review & Confirm
      ═══════════════════════════════════════════════ -->
      <div class="wizard-step-panel" id="step-4">
        <div class="wizard-panel-heading">
          <h2>Review Your Reservation</h2>
          <p>Please confirm the details below before submitting.</p>
        </div>

        <div class="review-card">

          <div class="review-card-section">
            <p class="review-card-label">Guest Details</p>
            <div class="review-rows">
              <div class="review-row">
                <span class="review-row-key">Name</span>
                <span class="review-row-val" id="rev-name">—</span>
              </div>
              <div class="review-row">
                <span class="review-row-key">Email</span>
                <span class="review-row-val" id="rev-email">—</span>
              </div>
              <div class="review-row">
                <span class="review-row-key">Phone</span>
                <span class="review-row-val" id="rev-phone">—</span>
              </div>
              <div class="review-row">
                <span class="review-row-key">Party Size</span>
                <span class="review-row-val" id="rev-guests">—</span>
              </div>
            </div>
          </div>

          <div class="review-card-section">
            <p class="review-card-label">Reservation Details</p>
            <div class="review-rows">
              <div class="review-row">
                <span class="review-row-key">Dining Zone</span>
                <span class="review-row-val" id="rev-zone">—</span>
              </div>
              <div class="review-row">
                <span class="review-row-key">Seating</span>
                <span class="review-row-val" id="rev-spot">—</span>
              </div>
              <div class="review-row">
                <span class="review-row-key">Date</span>
                <span class="review-row-val" id="rev-date">—</span>
              </div>
              <div class="review-row">
                <span class="review-row-key">Time</span>
                <span class="review-row-val" id="rev-time">—</span>
              </div>
              <div class="review-row">
                <span class="review-row-key">Occasion</span>
                <span class="review-row-val" id="rev-occasion">—</span>
              </div>
              <div class="review-row">
                <span class="review-row-key">Add-ons</span>
                <span class="review-row-val" id="rev-addon">—</span>
              </div>
            </div>
          </div>

          <div class="review-card-section">
            <p class="review-card-label">Special Requests</p>
            <p class="review-row-val" id="rev-requests" style="font-size:var(--text-sm); color:var(--clr-muted-fg)">—</p>
          </div>

        </div><!-- /review-card -->

        <div class="guest-note" style="margin-top:var(--space-4)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
          By confirming, you agree that this is a request — a final confirmation email will be sent once our team reviews your booking (usually within 1 hour).
        </div>

        <div class="wizard-nav">
          <div class="wizard-nav-left">
            <button type="button" class="btn btn-outline" id="btn-prev">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
              Back
            </button>
            <span class="wizard-step-count" id="step-count">Step 4 of 4</span>
          </div>
          <button type="button" class="btn btn-primary btn-lg" id="btn-next">
            Confirm Reservation
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </button>
        </div>

      </div><!-- /step-4 -->

    </div><!-- /wizard-main -->


    <!-- ── RIGHT: Summary Sidebar ── -->
    <aside class="wizard-sidebar">
      <div class="summary-card">

        <div class="summary-card-header">
          <h3>Booking Summary</h3>
          <p>Updates as you fill in each step</p>
        </div>

        <div class="summary-body">

          <div class="summary-item">
            <div class="summary-item-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="summary-item-text">
              <span class="summary-item-label">Guest</span>
              <span class="summary-item-value empty" id="sum-name">—</span>
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-item-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="summary-item-text">
              <span class="summary-item-label">Party Size</span>
              <span class="summary-item-value empty" id="sum-guests">—</span>
            </div>
          </div>

          <hr class="summary-divider">

          <div class="summary-item">
            <div class="summary-item-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div class="summary-item-text">
              <span class="summary-item-label">Dining Zone</span>
              <span class="summary-item-value empty" id="sum-zone">—</span>
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-item-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
            </div>
            <div class="summary-item-text">
              <span class="summary-item-label">Seating</span>
              <span class="summary-item-value empty" id="sum-spot">—</span>
            </div>
          </div>

          <hr class="summary-divider">

          <div class="summary-item">
            <div class="summary-item-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <div class="summary-item-text">
              <span class="summary-item-label">Date</span>
              <span class="summary-item-value empty" id="sum-date">—</span>
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-item-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="summary-item-text">
              <span class="summary-item-label">Time</span>
              <span class="summary-item-value empty" id="sum-time">—</span>
            </div>
          </div>

          <hr class="summary-divider">

          <p class="summary-note">
            No payment required to book. You will receive an email confirmation within 1 hour of submitting.
          </p>

        </div><!-- /summary-body -->
      </div><!-- /summary-card -->
    </aside>

  </form><!-- /wizard-body -->

</div><!-- /book-page -->

<?php include '../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
<script src="<?= $basePath ?>js/book.js"></script>
<script src="<?= $basePath ?>js/dev-tools.js"></script>
</body>
</html>
