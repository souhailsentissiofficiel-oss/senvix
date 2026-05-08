/* ============================================
   SECURE AUTH SYSTEM — script.js
   ============================================ */

/* ============================================
   SECURE AUTH SYSTEM — script.js
   Handles: code rain, auth forms, clock,
            password strength on register page
   Dashboard-specific logic → vault.js
   ============================================ */
'use strict';

/* ─── Code Rain ─────────────────────────────── */
(function initCodeRain() {
  const container = document.querySelector('.code-rain');
  if (!container) return;

  const snippets = [
    '#36F4','22%GS','AES-256','SHA-512','0xF3A1',
    'SSL/TLS','■■■■■','JWT●','€$¥₿','ROOT✗',
    '01101','HASH#','██░░','KEY:●●','∑Ω∆',
    'AUTH✓','BCRYPT','SALT::8F','TOKEN','∞NULL',
    '0xDEAD','CRYPT◆','###═══','NODE:3','VLAN∣',
    '%SAFE','PRIV_K','127.0','PORT:443','MASK/',
    'MD5✗','PBKDF2','ECB■','CTR●●','GCM✓',
    '⌘⇧↑','∅⊕⊗','≡≢≤≥','△◇○','⟨⟩{}',
  ];

  const count = window.innerWidth < 600 ? 22 : 40;
  for (let i = 0; i < count; i++) {
    const el = document.createElement('span');
    el.className = 'code-char';
    el.textContent = snippets[Math.floor(Math.random() * snippets.length)];
    const x     = Math.random() * 100;
    const dur   = 7 + Math.random() * 9;
    const delay = Math.random() * 12;
    const op    = 0.12 + Math.random() * 0.28;
    const drift = (Math.random() - 0.5) * 80;
    const hue   = Math.random() > 0.7 ? 'var(--blue-sky)' : 'var(--green-light)';
    el.style.cssText = `left:${x}%;--dur:${dur}s;--delay:${delay}s;--op:${op};--drift:${drift}px;color:${hue};font-size:${9+Math.random()*5}px;`;
    container.appendChild(el);
  }

  setInterval(() => {
    container.querySelectorAll('.code-char').forEach(el => {
      if (Math.random() > 0.7) el.textContent = snippets[Math.floor(Math.random() * snippets.length)];
    });
  }, 4000);
})();


/* ─── Password Strength Bars (register page) ── */
function initPasswordStrength() {
  document.querySelectorAll('input[type="password"][data-strength]').forEach(input => {
    const wrap = input.closest('.form-group');
    if (!wrap) return;
    const strengthBox = wrap.querySelector('.pw-strength');
    const bars  = wrap.querySelectorAll('.pw-bar');
    const label = wrap.querySelector('.pw-label');
    if (!strengthBox || !bars.length) return;

    input.addEventListener('input', () => {
      const val = input.value;
      if (!val) { strengthBox.classList.remove('visible'); return; }
      strengthBox.classList.add('visible');

      let score = 0;
      if (val.length >= 8)           score++;
      if (/[A-Z]/.test(val))         score++;
      if (/[0-9]/.test(val))         score++;
      if (/[^A-Za-z0-9]/.test(val))  score++;

      const levels = ['','weak','fair','good','strong'];
      const names  = ['','Weak','Fair','Good','Strong'];
      bars.forEach((bar, i) => {
        bar.className = 'pw-bar';
        if (i < score) bar.classList.add(levels[score]);
      });
      if (label) {
        label.textContent = `Strength: ${names[score] || '—'}`;
        label.style.color = score <= 1 ? 'var(--red-soft)'
                          : score === 2 ? 'var(--yellow-sun)'
                          : score === 3 ? 'var(--blue-mid)'
                          : 'var(--green-light)';
      }
    });
  });
}


/* ─── Toggle Password Eye ────────────────────── */
function initTogglePassword() {
  document.querySelectorAll('.toggle-pw').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = document.getElementById(btn.dataset.target);
      if (!target) return;
      const isText = target.type === 'text';
      target.type = isText ? 'password' : 'text';
      btn.querySelector('.eye-open').style.display  = isText ? 'block' : 'none';
      btn.querySelector('.eye-close').style.display = isText ? 'none'  : 'block';
    });
  });
}


/* ─── Form Validation (login + register) ─────── */
function showFieldError(input, msg) {
  input.style.borderColor = 'rgba(230,57,70,0.6)';
  input.style.boxShadow   = '0 0 0 3px rgba(230,57,70,0.15)';
  let err = input.parentElement.nextElementSibling;
  if (!err || !err.classList.contains('field-err')) {
    err = document.createElement('p');
    err.className = 'field-err';
    err.style.cssText = 'font-size:12px;color:var(--red-soft);margin-top:5px;';
    input.parentElement.after(err);
  }
  err.textContent = msg;
}

function clearFieldError(input) {
  input.style.borderColor = '';
  input.style.boxShadow   = '';
  const err = input.parentElement.nextElementSibling;
  if (err && err.classList.contains('field-err')) err.remove();
}

function initFormValidation() {
  document.querySelectorAll('input').forEach(inp => {
    inp.addEventListener('input', () => clearFieldError(inp));
  });

  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      let valid = true;
      const email = this.querySelector('[name="email"]');
      const pass  = this.querySelector('[name="password"]');
      if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        showFieldError(email, 'Enter a valid email address.'); valid = false;
      }
      if (!pass.value || pass.value.length < 6) {
        showFieldError(pass, 'Password must be at least 6 characters.'); valid = false;
      }
      if (!valid) { e.preventDefault(); } else {
        const btn = this.querySelector('.btn-primary');
        if (btn) btn.classList.add('loading');
      }
    });
  }

  const regForm = document.getElementById('registerForm');
  if (regForm) {
    regForm.addEventListener('submit', function(e) {
      let valid = true;
      const email = this.querySelector('[name="email"]');
      const pass  = this.querySelector('[name="password"]');
      const pass2 = this.querySelector('[name="confirm_password"]');
      const terms = this.querySelector('[name="terms"]');
      if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        showFieldError(email, 'Enter a valid email address.'); valid = false;
      }
      if (!pass.value || pass.value.length < 8) {
        showFieldError(pass, 'Password must be at least 8 characters.'); valid = false;
      }
      if (pass2 && pass.value !== pass2.value) {
        showFieldError(pass2, 'Passwords do not match.'); valid = false;
      }
      if (terms && !terms.checked) {
        const row = terms.closest('.check-row');
        if (row) {
          let err = row.nextElementSibling;
          if (!err || !err.classList.contains('field-err')) {
            err = document.createElement('p');
            err.className = 'field-err';
            err.style.cssText = 'font-size:12px;color:var(--red-soft);margin-top:5px;';
            row.after(err);
          }
          err.textContent = 'You must accept the terms.';
        }
        valid = false;
      }
      if (!valid) { e.preventDefault(); } else {
        const btn = this.querySelector('.btn-primary');
        if (btn) btn.classList.add('loading');
      }
    });
  }
}


/* ─── Page Transitions ───────────────────────── */
function initTransitions() {
  document.querySelectorAll('a[data-transition]').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const href = this.href;
      const card = document.querySelector('.glass-card') || document.querySelector('.dash-content');
      if (card) {
        card.classList.add('page-out');
        setTimeout(() => { window.location.href = href; }, 380);
      } else {
        window.location.href = href;
      }
    });
  });
}


/* ─── Auto-dismiss alerts ────────────────────── */
function initAlerts() {
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.5s, transform 0.5s';
      alert.style.opacity = '0';
      alert.style.transform = 'scale(0.95)';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });
}


/* ─── Live Clock ─────────────────────────────── */
function initClock() {
  const el = document.getElementById('liveClock');
  if (!el) return;
  const tick = () => {
    el.textContent = new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit', second:'2-digit' });
  };
  tick();
  setInterval(tick, 1000);
}


/* ─── Stat Card Entry Animation ──────────────── */
function initStatCards() {
  document.querySelectorAll('.stat-card').forEach((card, i) => {
    card.style.opacity   = '0';
    card.style.transform = 'translateY(20px)';
    setTimeout(() => {
      card.style.transition = 'opacity 0.5s, transform 0.5s cubic-bezier(0.16,1,0.3,1)';
      card.style.opacity    = '1';
      card.style.transform  = 'translateY(0)';
    }, 200 + i * 80);
  });
}


/* ─── Boot ───────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initPasswordStrength();
  initTogglePassword();
  initFormValidation();
  initTransitions();
  initAlerts();
  initClock();
  initStatCards();
});