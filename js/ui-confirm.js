/**
 * Global UI Confirm Modal
 * Replaces native window.confirm with a custom styled modal.
 * Triggered by `data-confirm="Message"` on links, buttons, or forms.
 */
(function() {
  'use strict';

  // Inject beautiful aesthetics for the modal directly
  const modalHTML = `
    <style>
      .ui-confirm-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(22, 20, 18, 0.45); /* Soft dark overlay */
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease-out, visibility 0.25s ease-out;
      }
      .ui-confirm-overlay.ui-confirm-active {
        opacity: 1;
        visibility: visible;
      }
      .ui-confirm-modal {
        background-color: var(--clr-bg, #fffefc);
        width: 100%;
        max-width: 420px;
        margin: var(--space-4);
        border-radius: 1.25rem;
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.03);
        padding: var(--space-8, 2rem);
        text-align: center;
        transform: scale(0.95) translateY(10px);
        transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
      }
      .ui-confirm-overlay.ui-confirm-active .ui-confirm-modal {
        transform: scale(1) translateY(0);
      }
      .ui-confirm-title {
        font-family: var(--font-display, serif);
        font-size: 1.4rem;
        font-weight: 500;
        color: var(--clr-fg, #1f1b18);
        margin: 0 0 var(--space-2, 0.5rem);
        line-height: 1.3;
      }
      .ui-confirm-message {
        font-family: var(--font-sans, sans-serif);
        font-size: 0.95rem;
        color: var(--clr-muted-fg, #6b635c);
        margin: 0 0 var(--space-6, 1.5rem);
        line-height: 1.5;
        max-width: 90%;
        margin-left: auto;
        margin-right: auto;
      }
      .ui-confirm-actions {
        display: flex;
        gap: var(--space-3, 0.75rem);
        justify-content: center;
      }
      .ui-confirm-actions .btn {
        flex: 1;
        padding-top: 0.7rem;
        padding-bottom: 0.7rem;
        font-size: 0.95rem;
      }
      .ui-confirm-btn-cancel {
        background-color: transparent;
        border: 1px solid var(--clr-border, #e0d8d0);
        color: var(--clr-fg, #1f1b18);
        cursor: pointer;
        transition: all 0.2s;
      }
      .ui-confirm-btn-cancel:hover {
        background-color: var(--clr-muted, #f3f0ea);
        border-color: var(--clr-border, #e0d8d0);
      }
      .ui-confirm-btn-ok {
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s;
        color: #fff;
      }
      .ui-confirm-btn-ok--warning {
        background-color: var(--clr-destructive, #dc2626);
      }
      .ui-confirm-btn-ok--warning:hover {
        background-color: #b91c1c;
      }
      .ui-confirm-btn-ok--info {
        background-color: var(--clr-primary, #3b2f2f);
      }
      .ui-confirm-btn-ok--info:hover {
        opacity: 0.9;
      }
    </style>
    <div class="ui-confirm-overlay" id="globalConfirmOverlay">
      <div class="ui-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="globalConfirmTitle">
        <h3 class="ui-confirm-title" id="globalConfirmTitle">Please confirm</h3>
        <p class="ui-confirm-message" id="globalConfirmMessage">Are you sure you want to proceed?</p>
        <div class="ui-confirm-actions">
          <button type="button" class="btn ui-confirm-btn-cancel" id="globalConfirmCancel">Cancel</button>
          <button type="button" class="btn ui-confirm-btn-ok" id="globalConfirmOk">Confirm</button>
        </div>
      </div>
    </div>
  `;

  document.addEventListener('DOMContentLoaded', () => {
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    const overlay = document.getElementById('globalConfirmOverlay');
    const titleEl = document.getElementById('globalConfirmTitle');
    const msgEl = document.getElementById('globalConfirmMessage');
    const btnCancel = document.getElementById('globalConfirmCancel');
    const btnOk = document.getElementById('globalConfirmOk');

    let pendingAction = null;

    function openConfirm(msg, onConfirm) {
      // Determine if action is destructive based on keywords
      const isDestructive = /(delete|remove|clear)/i.test(msg);

      if (isDestructive) {
        titleEl.textContent = "Are you sure?";
        btnOk.className = 'btn ui-confirm-btn-ok ui-confirm-btn-ok--warning';
        btnOk.textContent = 'Delete';
      } else {
        titleEl.textContent = "Please Confirm";
        btnOk.className = 'btn ui-confirm-btn-ok ui-confirm-btn-ok--info';
        btnOk.textContent = 'Confirm';
      }

      msgEl.textContent = msg;
      pendingAction = onConfirm;
      overlay.classList.add('ui-confirm-active');
    }

    function closeConfirm() {
      overlay.classList.remove('ui-confirm-active');
      pendingAction = null;
    }

    btnCancel.addEventListener('click', closeConfirm);
    btnOk.addEventListener('click', () => {
      const action = pendingAction;
      closeConfirm(); // Must close before action executes in case action involves DOM manipulation that breaks focus
      if (action) action();
    });
    
    // Close on click outside (overlay click)
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeConfirm();
    });
    // Close on escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && overlay.classList.contains('ui-confirm-active')) {
        closeConfirm();
      }
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
