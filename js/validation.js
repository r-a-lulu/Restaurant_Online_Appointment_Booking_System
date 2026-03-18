/**
 * =============================================================
 * Form Validation Utilities
 * Restaurant Online Appointment Booking System
 * =============================================================
 *
 * Reusable validation functions for all forms in the project.
 * Include this script BEFORE page-specific JS files.
 *
 * Usage:
 *   <script src="/js/validation.js"></script>
 *
 * Then in your form:
 *   const result = FormValidator.validateEmail('user@example.com');
 *   if (!result.valid) { showError(result.message); }
 * =============================================================
 */

const FormValidator = (() => {
  'use strict';

  // ---------------------------------------------------------
  // EMAIL VALIDATION
  // ---------------------------------------------------------

  /**
   * Validates an email address against RFC 5322 simplified pattern.
   * Checks format, length, and common typos.
   *
   * @param {string} email - The email to validate
   * @returns {{ valid: boolean, message: string }}
   */
  function validateEmail(email) {
    if (!email || typeof email !== 'string') {
      return { valid: false, message: 'Email address is required.' };
    }

    const trimmed = email.trim();

    // Check empty after trim
    if (trimmed.length === 0) {
      return { valid: false, message: 'Email address is required.' };
    }

    // Max length (per RFC 5321)
    if (trimmed.length > 254) {
      return { valid: false, message: 'Email address is too long (max 254 characters).' };
    }

    // RFC 5322 simplified regex
    // Validates: local@domain.tld format
    const emailPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;

    if (!emailPattern.test(trimmed)) {
      return { valid: false, message: 'Please enter a valid email address (e.g., name@example.com).' };
    }

    // Must have at least one dot in the domain part
    const domainPart = trimmed.split('@')[1];
    if (!domainPart || !domainPart.includes('.')) {
      return { valid: false, message: 'Email domain must include a valid extension (e.g., .com, .ph).' };
    }

    // TLD must be at least 2 characters
    const tld = domainPart.split('.').pop();
    if (tld.length < 2) {
      return { valid: false, message: 'Email domain extension is too short.' };
    }

    // Common typo detection
    const commonDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
    const typoMap = {
      'gmial.com': 'gmail.com',
      'gmal.com': 'gmail.com',
      'gamil.com': 'gmail.com',
      'gmail.co': 'gmail.com',
      'gmaill.com': 'gmail.com',
      'yaho.com': 'yahoo.com',
      'yahooo.com': 'yahoo.com',
      'yahoo.co': 'yahoo.com',
      'outlok.com': 'outlook.com',
      'outllook.com': 'outlook.com',
      'hotmal.com': 'hotmail.com',
      'hotmial.com': 'hotmail.com',
    };

    if (typoMap[domainPart]) {
      return {
        valid: false,
        message: `Did you mean ${trimmed.split('@')[0]}@${typoMap[domainPart]}?`
      };
    }

    return { valid: true, message: '' };
  }

  // ---------------------------------------------------------
  // FORM FIELD HELPERS
  // ---------------------------------------------------------

  /**
   * Shows an error message below a form field.
   * Expects the field to have a sibling or nearby element with class `.field-error`.
   * If none exists, it creates one.
   *
   * @param {HTMLElement} field - The input/select element
   * @param {string} message - Error message to display
   */
  function showFieldError(field, message) {
    clearFieldError(field);

    field.classList.add('field-invalid');

    let errorEl = field.parentElement.querySelector('.field-error');
    if (!errorEl) {
      errorEl = document.createElement('span');
      errorEl.className = 'field-error';
      field.parentElement.appendChild(errorEl);
    }

    errorEl.textContent = message;
    errorEl.style.display = 'block';

    // Accessibility
    field.setAttribute('aria-invalid', 'true');
    field.setAttribute('aria-describedby', errorEl.id || '');
  }

  /**
   * Clears the error state from a form field.
   *
   * @param {HTMLElement} field - The input/select element
   */
  function clearFieldError(field) {
    field.classList.remove('field-invalid');
    field.setAttribute('aria-invalid', 'false');

    const errorEl = field.parentElement.querySelector('.field-error');
    if (errorEl) {
      errorEl.textContent = '';
      errorEl.style.display = 'none';
    }
  }

  /**
   * Attaches real-time email validation to an input field.
   * Validates on blur and on form submit.
   *
   * @param {string} inputSelector - CSS selector for the email input
   * @param {object} options - Optional config
   * @param {string} options.formSelector - CSS selector for the parent form
   */
  function attachEmailValidation(inputSelector, options = {}) {
    const input = document.querySelector(inputSelector);
    if (!input) return;

    // Validate on blur (when user tabs/clicks away)
    input.addEventListener('blur', () => {
      const result = validateEmail(input.value);
      if (!result.valid) {
        showFieldError(input, result.message);
      } else {
        clearFieldError(input);
      }
    });

    // Clear error on focus (let them type)
    input.addEventListener('focus', () => {
      clearFieldError(input);
    });

    // If form selector is provided, also validate on submit
    if (options.formSelector) {
      const form = document.querySelector(options.formSelector);
      if (form) {
        form.addEventListener('submit', (e) => {
          const result = validateEmail(input.value);
          if (!result.valid) {
            e.preventDefault();
            showFieldError(input, result.message);
            input.focus();
          }
        });
      }
    }
  }

  // ---------------------------------------------------------
  // PUBLIC API
  // ---------------------------------------------------------

  return {
    validateEmail,
    showFieldError,
    clearFieldError,
    attachEmailValidation
  };

})();
