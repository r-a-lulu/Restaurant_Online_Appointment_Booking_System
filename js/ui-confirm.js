/**
 * Global UI Confirm Modal
 * Replaces native window.confirm with a custom styled modal.
 * Triggered by `data-confirm="Message"` on links, buttons, or forms.
 */
(function() {
  'use strict';

  // Inject modal HTML into body
  const modalHTML = `
    <div class="modal-overlay" id="globalConfirmModal" style="z-index: 99999;">
      <div class="modal" style="max-width: 400px; text-align: center;">
        <div class="modal-body" style="padding-top: var(--space-6);">
          <h3 id="globalConfirmMessage" style="margin-bottom: var(--space-6); font-family: var(--font-display); font-size: 1.25rem; color: var(--clr-fg);">Are you sure?</h3>
          <div style="display: flex; gap: var(--space-4); justify-content: center;">
            <button type="button" class="btn btn-outline" id="globalConfirmCancel">Cancel</button>
            <button type="button" class="btn btn-primary" id="globalConfirmOk">Confirm</button>
          </div>
        </div>
      </div>
    </div>
  `;

  document.addEventListener('DOMContentLoaded', () => {
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    const modal = document.getElementById('globalConfirmModal');
    const msgEl = document.getElementById('globalConfirmMessage');
    const btnCancel = document.getElementById('globalConfirmCancel');
    const btnOk = document.getElementById('globalConfirmOk');

    let pendingAction = null;

    function openConfirm(msg, onConfirm) {
      msgEl.textContent = msg;
      pendingAction = onConfirm;
      modal.classList.add('active');
    }

    function closeConfirm() {
      modal.classList.remove('active');
      pendingAction = null;
    }

    btnCancel.addEventListener('click', closeConfirm);
    btnOk.addEventListener('click', () => {
      const action = pendingAction;
      closeConfirm(); // Must close before action executes in case action involves DOM manipulation that breaks focus
      if (action) action();
    });
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeConfirm();
    });

    document.body.addEventListener('click', function(e) {
      const btn = e.target.closest('[data-confirm]');
      if (!btn) return;
      
      const form = btn.closest('form');
      const msg = btn.getAttribute('data-confirm');

      // Intercept submit button inside a form, or an action button
      if (form && (btn.type === 'submit' || btn.tagName === 'BUTTON')) {
        e.preventDefault();
        openConfirm(msg, function() {
          // Temporarily remove to avoid double triggers if there's custom logic
          btn.removeAttribute('data-confirm');
          // Add hidden input if the button had a name/value (e.g. action="delete")
          if (btn.name) {
             const input = document.createElement('input');
             input.type = 'hidden';
             input.name = btn.name;
             input.value = btn.value;
             form.appendChild(input);
          }
          form.submit();
        });
      } else if (btn.tagName === 'A' || btn.tagName === 'BUTTON') {
        e.preventDefault();
        openConfirm(msg, function() {
          if (btn.href) {
            window.location.href = btn.href;
          } else {
             btn.removeAttribute('data-confirm');
             btn.click();
          }
        });
      }
    });

    // Handle form submissions if data-confirm is on the form itself instead of the submit button
    document.body.addEventListener('submit', function(e) {
       const form = e.target;
       const msg = form.getAttribute('data-confirm');
       if (msg) {
         e.preventDefault();
         openConfirm(msg, function() {
           form.removeAttribute('data-confirm');
           form.submit();
         });
       }
    });

    // Expose programmatic API just in case it is needed by JS
    window.uiConfirm = function(msg, callback) {
       openConfirm(msg, callback);
    };
  });

})();
