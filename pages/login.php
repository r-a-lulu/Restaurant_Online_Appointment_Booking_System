<?php
/**
 * Login Page — Eudaimonia Restaurant
 * Centered two-panel auth layout with email/password form.
 */

$pageTitle   = 'Sign In';
$pageCSS     = ['auth.css'];
$currentPage = 'login';
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
        <blockquote>"Dining is not merely eating. It is an art—a ritual of pleasure shared among souls."</blockquote>
        <cite>— Eudaimonia Philosophy</cite>
      </div>

      <div class="auth-features">
        <div class="auth-feature-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          Manage your reservations with ease
        </div>
        <div class="auth-feature-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          View your dining history and preferences
        </div>
        <div class="auth-feature-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          Exclusive member-only offers and updates
        </div>
      </div>
    </div>
  </aside>

  <!-- ===== RIGHT: Form Panel ===== -->
  <main class="auth-form-panel">
    <div class="auth-form-inner">

      <!-- Heading -->
      <div class="auth-heading">
        <p class="section-label">Welcome Back</p>
        <h1>Sign In to Your Account</h1>
        <p>Enter your credentials below to access your Eudaimonia guest portal.</p>
      </div>

      <!-- Login Form -->
      <form class="auth-form" id="login-form" novalidate>

        <!-- Email -->
        <div class="form-group">
          <label for="login-email" class="form-label">Email Address</label>
          <input
            type="email"
            id="login-email"
            name="email"
            class="form-input"
            placeholder="you@example.com"
            autocomplete="email"
            required
          >
        </div>

        <!-- Password -->
        <div class="form-group">
          <div class="auth-form-meta">
            <label for="login-password" class="form-label">Password</label>
            <a href="#" class="auth-link" id="forgot-password-link">Forgot password?</a>
          </div>
          <div class="input-password-wrap">
            <input
              type="password"
              id="login-password"
              name="password"
              class="form-input"
              placeholder="Enter your password"
              autocomplete="current-password"
              required
            >
            <button
              type="button"
              class="input-password-toggle"
              id="toggle-login-password"
              aria-label="Show/hide password"
            >
              <!-- Eye icon (visible when password is hidden) -->
              <svg id="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <!-- Eye-off icon (visible when password is shown) -->
              <svg id="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Submit -->
        <div class="auth-submit">
          <button type="submit" class="btn btn-primary btn-block btn-lg" id="login-btn">
            Sign In
          </button>
        </div>

      </form>

      <!-- Divider -->
      <div class="auth-separator">or</div>

      <!-- Admin Portal Link -->
      <a href="#" onclick="alert('Admin portal — Phase 6'); return false;" class="auth-admin-link" id="admin-portal-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="18" height="18" x="3" y="3" rx="2"/>
          <path d="M9 9h6M9 12h6M9 15h4"/>
        </svg>
        Access Admin Portal
      </a>

      <!-- Switch to Register -->
      <p class="auth-switch">
        Don't have an account?
        <a href="<?= $basePath ?>pages/register.php" class="auth-link" id="go-to-register">Create one here</a>
      </p>

    </div>
  </main>

</div>

<script>
  // ──── Password Show/Hide Toggle ────
  (function () {
    var btn      = document.getElementById('toggle-login-password');
    var input    = document.getElementById('login-password');
    var eyeOpen  = document.getElementById('eye-open');
    var eyeClosed = document.getElementById('eye-closed');

    if (!btn || !input) return;

    btn.addEventListener('click', function () {
      var isPassword = input.type === 'password';
      input.type     = isPassword ? 'text' : 'password';
      eyeOpen.style.display  = isPassword ? 'none'  : 'block';
      eyeClosed.style.display = isPassword ? 'block' : 'none';
    });
  })();

  // ──── Basic client-side validation feedback ────
  (function () {
    var form = document.getElementById('login-form');
    var btn  = document.getElementById('login-btn');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var email    = document.getElementById('login-email').value.trim();
      var password = document.getElementById('login-password').value;

      // Remove any existing alert
      var existing = form.querySelector('.auth-alert');
      if (existing) existing.remove();

      if (!email || !password) {
        var alert = document.createElement('div');
        alert.className = 'auth-alert';
        alert.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span>Please fill in all fields.</span>';
        form.insertBefore(alert, form.firstChild);
        return;
      }

      // Simulate loading state
      btn.disabled = true;
      btn.textContent = 'Signing in…';

      // Simulate API delay (replace with real PHP form action later)
      setTimeout(function () {
        btn.disabled = false;
        btn.textContent = 'Sign In';
        // In a real implementation, redirect to dashboard or show server error.
        alert('Login functionality will connect to the PHP backend in a future phase. Email: ' + email);
      }, 1200);
    });
  })();
</script>

<?php include '../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
<script src="<?= $basePath ?>js/dev-tools.js"></script>
</body>
</html>
