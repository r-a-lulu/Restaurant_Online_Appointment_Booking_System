<?php
/**
 * Register Page — Eudaimonia Restaurant
 * Registration form: name, email, phone, password, confirm, terms checkbox.
 */

$pageTitle   = 'Create Account';
$pageCSS     = ['auth.css'];
$currentPage = 'register';
$navStyle    = 'solid';
$basePath    = '../';

include '../includes/header.php';
?>

<div class="auth-page">

  <!-- ===== LEFT: Brand Panel ===== -->
  <aside class="auth-panel">
    <div class="auth-panel-bg"></div>

    <div class="auth-panel-content">
      <a href="<?= $basePath ?>index.php" class="auth-panel-logo">Eudaimonia</a>

      <div class="auth-panel-quote">
        <blockquote>"Every great dining experience begins with a single reservation."</blockquote>
        <cite>— Eudaimonia Philosophy</cite>
      </div>

      <div class="auth-features">
        <div class="auth-feature-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          Book tables in seconds from your personal dashboard
        </div>
        <div class="auth-feature-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          Track all past and upcoming reservations
        </div>
        <div class="auth-feature-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          Save dining preferences and special requests
        </div>
        <div class="auth-feature-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          Receive exclusive member offers and invitations
        </div>
      </div>
    </div>
  </aside>

  <!-- ===== RIGHT: Form Panel ===== -->
  <main class="auth-form-panel">
    <div class="auth-form-inner">

      <!-- Heading -->
      <div class="auth-heading">
        <p class="section-label">Join Us</p>
        <h1>Create Your Account</h1>
        <p>Register today for seamless reservations and a personalized dining experience.</p>
      </div>

      <!-- Register Form -->
      <form class="auth-form" id="register-form" novalidate>

        <!-- First Name + Last Name -->
        <div class="auth-form-row">
          <div class="form-group">
            <label for="reg-firstname" class="form-label">First Name</label>
            <input
              type="text"
              id="reg-firstname"
              name="first_name"
              class="form-input"
              placeholder="Jane"
              autocomplete="given-name"
              required
            >
          </div>
          <div class="form-group">
            <label for="reg-lastname" class="form-label">Last Name</label>
            <input
              type="text"
              id="reg-lastname"
              name="last_name"
              class="form-input"
              placeholder="Doe"
              autocomplete="family-name"
              required
            >
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="reg-email" class="form-label">Email Address</label>
          <input
            type="email"
            id="reg-email"
            name="email"
            class="form-input"
            placeholder="you@example.com"
            autocomplete="email"
            required
          >
        </div>

        <!-- Phone -->
        <div class="form-group">
          <label for="reg-phone" class="form-label">
            Phone Number
            <span style="font-weight: 400; color: var(--clr-muted-fg);">(optional)</span>
          </label>
          <input
            type="tel"
            id="reg-phone"
            name="phone"
            class="form-input"
            placeholder="+1 (555) 000-0000"
            autocomplete="tel"
          >
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="reg-password" class="form-label">Password</label>
          <div class="input-password-wrap">
            <input
              type="password"
              id="reg-password"
              name="password"
              class="form-input"
              placeholder="Create a strong password"
              autocomplete="new-password"
              required
            >
            <button
              type="button"
              class="input-password-toggle"
              id="toggle-reg-password"
              aria-label="Show/hide password"
            >
              <svg id="reg-eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <svg id="reg-eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </button>
          </div>
          <!-- Password strength meter -->
          <div class="password-strength" id="password-strength" style="display:none">
            <div class="strength-bars">
              <div class="strength-bar" id="bar-1"></div>
              <div class="strength-bar" id="bar-2"></div>
              <div class="strength-bar" id="bar-3"></div>
              <div class="strength-bar" id="bar-4"></div>
            </div>
            <span class="strength-label" id="strength-label">–</span>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <label for="reg-confirm" class="form-label">Confirm Password</label>
          <div class="input-password-wrap">
            <input
              type="password"
              id="reg-confirm"
              name="confirm_password"
              class="form-input"
              placeholder="Re-enter your password"
              autocomplete="new-password"
              required
            >
            <button
              type="button"
              class="input-password-toggle"
              id="toggle-reg-confirm"
              aria-label="Show/hide confirm password"
            >
              <svg id="conf-eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <svg id="conf-eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Terms -->
        <div class="auth-terms">
          <input
            type="checkbox"
            id="reg-terms"
            name="terms"
            class="form-checkbox"
            required
          >
          <label for="reg-terms">
            I agree to the
            <a href="#" class="auth-link">Terms of Service</a>
            and
            <a href="#" class="auth-link">Privacy Policy</a>.
            I understand that my information will be used to manage my reservations.
          </label>
        </div>

        <!-- Submit -->
        <div class="auth-submit">
          <button type="submit" class="btn btn-primary btn-block btn-lg" id="register-btn">
            Create Account
          </button>
        </div>

      </form>

      <!-- Switch to Login -->
      <p class="auth-switch">
        Already have an account?
        <a href="<?= $basePath ?>pages/login.php" class="auth-link" id="go-to-login">Sign in here</a>
      </p>

    </div>
  </main>

</div>

<script>
  // ──── Password Show/Hide Toggles ────
  function makeToggle(btnId, inputId, eyeOpenId, eyeClosedId) {
    var btn = document.getElementById(btnId);
    var input = document.getElementById(inputId);
    var eyeOpen = document.getElementById(eyeOpenId);
    var eyeClosed = document.getElementById(eyeClosedId);
    if (!btn || !input) return;
    btn.addEventListener('click', function () {
      var isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      eyeOpen.style.display  = isPass ? 'none'  : 'block';
      eyeClosed.style.display = isPass ? 'block' : 'none';
    });
  }
  makeToggle('toggle-reg-password', 'reg-password',  'reg-eye-open',  'reg-eye-closed');
  makeToggle('toggle-reg-confirm',  'reg-confirm',   'conf-eye-open', 'conf-eye-closed');

  // ──── Password Strength Meter ────
  (function () {
    var input      = document.getElementById('reg-password');
    var meter      = document.getElementById('password-strength');
    var bars       = [
      document.getElementById('bar-1'),
      document.getElementById('bar-2'),
      document.getElementById('bar-3'),
      document.getElementById('bar-4'),
    ];
    var label      = document.getElementById('strength-label');
    var levels     = ['Weak', 'Fair', 'Good', 'Strong'];
    var levelClass = ['active-weak', 'active-fair', 'active-good', 'active-strong'];

    function getScore(pw) {
      var score = 0;
      if (pw.length >= 8)  score++;
      if (/[A-Z]/.test(pw)) score++;
      if (/[0-9]/.test(pw)) score++;
      if (/[^A-Za-z0-9]/.test(pw)) score++;
      return score; // 0–4
    }

    if (!input) return;

    input.addEventListener('input', function () {
      var val = input.value;
      if (!val) {
        meter.style.display = 'none';
        return;
      }
      meter.style.display = 'flex';
      var score = getScore(val);
      if (score === 0) score = 1; // at least 1 bar if typing

      bars.forEach(function (bar, i) {
        bar.className = 'strength-bar';
        if (i < score) bar.classList.add(levelClass[score - 1]);
      });

      label.textContent = 'Strength: ' + levels[score - 1];
    });
  })();

  // ──── Form Validation & Submit ────
  (function () {
    var form = document.getElementById('register-form');
    var btn  = document.getElementById('register-btn');
    if (!form) return;

    function showAlert(msg) {
      var existing = form.querySelector('.auth-alert');
      if (existing) existing.remove();
      var alert = document.createElement('div');
      alert.className = 'auth-alert';
      alert.innerHTML =
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
        '<span>' + msg + '</span>';
      form.insertBefore(alert, form.firstChild);
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var firstName = document.getElementById('reg-firstname').value.trim();
      var lastName  = document.getElementById('reg-lastname').value.trim();
      var email     = document.getElementById('reg-email').value.trim();
      var password  = document.getElementById('reg-password').value;
      var confirm   = document.getElementById('reg-confirm').value;
      var terms     = document.getElementById('reg-terms').checked;

      // Remove existing alert
      var existing = form.querySelector('.auth-alert');
      if (existing) existing.remove();

      if (!firstName || !lastName || !email || !password || !confirm) {
        showAlert('Please fill in all required fields.'); return;
      }

      var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRe.test(email)) {
        showAlert('Please enter a valid email address.'); return;
      }

      if (password.length < 8) {
        showAlert('Password must be at least 8 characters long.'); return;
      }

      if (password !== confirm) {
        showAlert('Passwords do not match.'); return;
      }

      if (!terms) {
        showAlert('You must agree to the Terms of Service to continue.'); return;
      }

      // Loading state
      btn.disabled = true;
      btn.textContent = 'Creating account…';

      // Simulate API delay (replace with real PHP form action later)
      setTimeout(function () {
        btn.disabled = false;
        btn.textContent = 'Create Account';
        alert('Registration functionality will connect to the PHP backend in a future phase. Welcome, ' + firstName + '!');
      }, 1400);
    });
  })();
</script>

<?php include '../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
<script src="<?= $basePath ?>js/dev-tools.js"></script>
</body>
</html>
