/* ============================================================
   vault.js  —  Dashboard: Analyzer · Generator · Vault Table
   Wired to exact IDs in dashboard.php. No redesign.
   ============================================================ */
'use strict';

/* ══════════════════════════════════════════════════════════════
   SECTION 1 — SHARED UTILITIES
   ══════════════════════════════════════════════════════════════ */

/**
 * Analyse a password and return a rich result object.
 * Used by both the Analyzer panel and the Generator strength bar.
 */
function analysePassword(pw) {
  const len     = pw.length;
  const hasUp   = /[A-Z]/.test(pw);
  const hasLow  = /[a-z]/.test(pw);
  const hasDig  = /[0-9]/.test(pw);
  const hasSym  = /[^A-Za-z0-9]/.test(pw);
  const hasLen8  = len >= 8;
  const hasLen12 = len >= 12;

  /* Entropy: log2(charsetSize) * length */
  let charsetSize = 0;
  if (hasUp)  charsetSize += 26;
  if (hasLow) charsetSize += 26;
  if (hasDig) charsetSize += 10;
  if (hasSym) charsetSize += 32;
  if (charsetSize === 0 && len > 0) charsetSize = 26; // assume lowercase
  const entropy = len > 0 ? Math.round(Math.log2(charsetSize) * len) : 0;

  /* Score 0-100 */
  let score = 0;
  if (hasLen8)  score += 15;
  if (hasLen12) score += 15;
  if (hasUp)    score += 15;
  if (hasLow)   score += 15;
  if (hasDig)   score += 15;
  if (hasSym)   score += 15;
  // Bonus for extra length
  if (len >= 16) score += 5;
  if (len >= 20) score += 5;
  score = Math.min(100, score);

  /* Strength label */
  const strengthLabel = score < 30 ? 'Very Weak'
                      : score < 50 ? 'Weak'
                      : score < 65 ? 'Fair'
                      : score < 80 ? 'Good'
                      : score < 95 ? 'Strong'
                      : 'Excellent';

  /* Strength index 0-4 for CSS classes */
  const strengthIdx = score < 30 ? 0
                    : score < 50 ? 1
                    : score < 65 ? 2
                    : score < 80 ? 3
                    : 4;

  /* Suggestions */
  const suggestions = [];
  if (!hasLen8)  suggestions.push('Use at least 8 characters.');
  if (!hasLen12) suggestions.push('Aim for 12+ characters for better security.');
  if (!hasUp)    suggestions.push('Add uppercase letters (A–Z).');
  if (!hasLow)   suggestions.push('Add lowercase letters (a–z).');
  if (!hasDig)   suggestions.push('Include at least one number (0–9).');
  if (!hasSym)   suggestions.push('Add symbols like !@#$%^&* for extra strength.');
  if (len < 16)  suggestions.push('Consider a length of 16+ for high-security accounts.');

  return {
    len, hasLen8, hasLen12, hasUp, hasLow, hasDig, hasSym,
    entropy, score, strengthLabel, strengthIdx, suggestions
  };
}

/** Score → colour for ring and bars */
function scoreColour(score) {
  if (score < 30) return 'var(--red-alert)';
  if (score < 50) return 'var(--red-soft)';
  if (score < 65) return 'var(--yellow-sun)';
  if (score < 80) return 'var(--blue-mid)';
  return 'var(--green-light)';
}

/** Strength-index label strings */
const STR_LABELS = ['Very Weak','Weak','Fair','Good','Strong'];


/* ══════════════════════════════════════════════════════════════
   SECTION 2 — PASSWORD ANALYZER PANEL
   Elements: #analyzerInput, #toggleAnalyzerVis,
             #scoreRingFill, #scoreNumber,
             #checkList [data-key items],
             #metaStrength, #metaEntropy, #metaLength,
             #suggestionsBox, #suggestionsList
   ══════════════════════════════════════════════════════════════ */

function initAnalyzer() {
  const input      = document.getElementById('analyzerInput');
  const toggleBtn  = document.getElementById('toggleAnalyzerVis');
  const ringFill   = document.getElementById('scoreRingFill');
  const scoreNum   = document.getElementById('scoreNumber');
  const checkItems = document.querySelectorAll('#checkList .check-item');
  const metaStr    = document.getElementById('metaStrength');
  const metaEnt    = document.getElementById('metaEntropy');
  const metaLen    = document.getElementById('metaLength');
  const suggBox    = document.getElementById('suggestionsBox');
  const suggList   = document.getElementById('suggestionsList');

  if (!input) return; // not on dashboard

  /* SVG ring: circumference = 2π×50 ≈ 314 */
  const CIRC = 314;

  /* Map data-key → result property */
  const KEY_MAP = {
    len8:   r => r.hasLen8,
    len12:  r => r.hasLen12,
    upper:  r => r.hasUp,
    lower:  r => r.hasLow,
    digit:  r => r.hasDig,
    symbol: r => r.hasSym,
  };

  function updateUI(pw) {
    const r = analysePassword(pw);

    /* Score ring */
    const offset = CIRC - (CIRC * r.score / 100);
    if (ringFill) {
      ringFill.style.strokeDashoffset = offset;
      ringFill.style.stroke = scoreColour(r.score);
    }
    if (scoreNum) scoreNum.textContent = r.score;

    /* Checklist */
    checkItems.forEach(item => {
      const key    = item.dataset.key;
      const passed = pw.length > 0 && KEY_MAP[key] && KEY_MAP[key](r);
      const icon   = item.querySelector('.ci-icon');
      if (passed) {
        item.classList.add('passed');
        if (icon) icon.textContent = '✅';
      } else {
        item.classList.remove('passed');
        if (icon) icon.textContent = '❌';
      }
    });

    /* Meta row */
    if (metaStr) {
      metaStr.textContent = pw.length > 0 ? r.strengthLabel : '—';
      metaStr.style.color = pw.length > 0 ? scoreColour(r.score) : '';
    }
    if (metaEnt) metaEnt.textContent = pw.length > 0 ? r.entropy + ' bits' : '0 bits';
    if (metaLen) metaLen.textContent = pw.length > 0 ? r.len + ' chars' : '0 chars';

    /* Suggestions */
    if (suggBox && suggList) {
      if (pw.length > 0 && r.suggestions.length > 0) {
        suggList.innerHTML = r.suggestions
          .map(s => `<li>${s}</li>`)
          .join('');
        suggBox.style.display = 'block';
      } else {
        suggBox.style.display = 'none';
      }
    }
  }

  /* Live update on every keystroke */
  input.addEventListener('input', () => {
    updateUI(input.value);
    /* Show / hide the analyzer save form based on whether anything is typed */
    const analyzerSaveForm = document.getElementById('analyzerSaveForm');
    if (analyzerSaveForm) {
      analyzerSaveForm.style.display = input.value.length > 0 ? 'block' : 'none';
    }
  });

  /* Eye toggle */
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      const isText = input.type === 'text';
      input.type = isText ? 'password' : 'text';
      const eyeOpen  = toggleBtn.querySelector('.eye-open');
      const eyeClose = toggleBtn.querySelector('.eye-close');
      if (eyeOpen)  eyeOpen.style.display  = isText ? 'block' : 'none';
      if (eyeClose) eyeClose.style.display = isText ? 'none'  : 'block';
    });
  }

  /* Initial render (empty) */
  updateUI('');
}


/* ══════════════════════════════════════════════════════════════
   SECTION 3 — PASSWORD GENERATOR PANEL
   Elements: #genLength, #genLenVal,
             #genUpper, #genLower, #genDigits, #genSymbols,
             #genOutput, #genCopyBtn,
             #genStrengthFill, #genStrengthLabel,
             #btnGenerate,
             #saveForm, #saveLabel, #btnSaveVault, #saveStatus,
             #btnSuggestFromAnalyzer, #analyzerInput
   ══════════════════════════════════════════════════════════════ */

/* Store last-generated password & its analysis for save action */
let _lastGenerated = { pw: '', analysis: null };

function generatePassword() {
  const lenSlider = document.getElementById('genLength');
  const chkUpper  = document.getElementById('genUpper');
  const chkLower  = document.getElementById('genLower');
  const chkDigits = document.getElementById('genDigits');
  const chkSymbols= document.getElementById('genSymbols');

  const len     = lenSlider  ? parseInt(lenSlider.value)   : 16;
  const upper   = chkUpper   ? chkUpper.checked   : true;
  const lower   = chkLower   ? chkLower.checked   : true;
  const digits  = chkDigits  ? chkDigits.checked  : true;
  const symbols = chkSymbols ? chkSymbols.checked : true;

  let charset = '';
  if (upper)   charset += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  if (lower)   charset += 'abcdefghijklmnopqrstuvwxyz';
  if (digits)  charset += '0123456789';
  if (symbols) charset += '!@#$%^&*()_+-=[]{}|;:,.<>?';
  if (!charset) charset = 'abcdefghijklmnopqrstuvwxyz'; // fallback

  /* Guarantee at least one char from each selected set */
  let mandatory = '';
  if (upper)   mandatory += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random()*26)];
  if (lower)   mandatory += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random()*26)];
  if (digits)  mandatory += '0123456789'[Math.floor(Math.random()*10)];
  if (symbols) mandatory += '!@#$%^&*'[Math.floor(Math.random()*8)];

  /* Fill remaining length with crypto random */
  const remaining = Math.max(0, len - mandatory.length);
  const arr = new Uint32Array(remaining);
  crypto.getRandomValues(arr);
  let pw = mandatory + Array.from(arr, n => charset[n % charset.length]).join('');

  /* Shuffle */
  pw = pw.split('').sort(() => Math.random() - 0.5).join('');

  return pw;
}

function renderGeneratedPassword(pw) {
  const output      = document.getElementById('genOutput');
  const strengthFill= document.getElementById('genStrengthFill');
  const strengthLbl = document.getElementById('genStrengthLabel');
  const saveForm    = document.getElementById('saveForm');

  if (!pw) return;

  const r = analysePassword(pw);
  _lastGenerated = { pw, analysis: r };

  if (output) {
    output.textContent = pw;
    output.style.color = scoreColour(r.score);
    /* Flash animation */
    output.style.opacity = '0';
    requestAnimationFrame(() => {
      output.style.transition = 'opacity 0.3s';
      output.style.opacity    = '1';
    });
  }

  /* Strength bar */
  const pct = r.score + '%';
  if (strengthFill) {
    strengthFill.style.width      = pct;
    strengthFill.style.background = scoreColour(r.score);
  }
  if (strengthLbl) {
    strengthLbl.textContent = r.strengthLabel;
    strengthLbl.style.color = scoreColour(r.score);
  }

  /* Show save form */
  if (saveForm) saveForm.style.display = 'block';
}

function initGenerator() {
  const lenSlider = document.getElementById('genLength');
  const lenVal    = document.getElementById('genLenVal');
  const btnGen    = document.getElementById('btnGenerate');
  const copyBtn   = document.getElementById('genCopyBtn');
  const saveLabel = document.getElementById('saveLabel');
  const btnSave   = document.getElementById('btnSaveVault');
  const saveStatus= document.getElementById('saveStatus');
  const btnSuggest= document.getElementById('btnSuggestFromAnalyzer');

  if (!btnGen) return; // not on dashboard

  /* Slider label */
  if (lenSlider && lenVal) {
    lenSlider.addEventListener('input', () => {
      lenVal.textContent = lenSlider.value;
    });
  }

  /* Generate button */
  btnGen.addEventListener('click', () => {
    const pw = generatePassword();
    renderGeneratedPassword(pw);
  });

  /* Copy button */
  if (copyBtn) {
    copyBtn.addEventListener('click', () => {
      const output = document.getElementById('genOutput');
      const text   = output ? output.textContent.trim() : '';
      if (!text || text === 'Click generate…') return;

      navigator.clipboard.writeText(text).then(() => {
        const orig = copyBtn.innerHTML;
        copyBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>';
        copyBtn.style.color      = 'var(--green-light)';
        copyBtn.style.borderColor= 'rgba(82,183,136,0.6)';
        setTimeout(() => {
          copyBtn.innerHTML         = orig;
          copyBtn.style.color       = '';
          copyBtn.style.borderColor = '';
        }, 1800);
      }).catch(() => {
        /* Fallback for http */
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity  = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
      });
    });
  }

  /* Save to vault */
  if (btnSave) {
    btnSave.addEventListener('click', async () => {
      const label = saveLabel ? saveLabel.value.trim() : '';
      if (!label) {
        setStatus(saveStatus, 'Please enter a label first.', 'err');
        saveLabel && saveLabel.focus();
        return;
      }
      if (!_lastGenerated.pw) {
        setStatus(saveStatus, 'Generate a password first.', 'err');
        return;
      }

      const r = _lastGenerated.analysis || analysePassword(_lastGenerated.pw);
      btnSave.disabled = true;
      btnSave.textContent = 'Saving…';

      try {
        const fd = new FormData();
        fd.append('action',   'save');
        fd.append('label',    label);
        fd.append('password', _lastGenerated.pw);
        fd.append('strength', r.strengthIdx);
        fd.append('entropy',  r.entropy);

        const res  = await fetch('vault-api.php', { method:'POST', body: fd });
        const data = await res.json();

        if (data.ok) {
          setStatus(saveStatus, '✓ Saved to vault!', 'ok');
          if (saveLabel) saveLabel.value = '';
          loadVault(); // refresh table + sidebar
        } else {
          setStatus(saveStatus, data.error || 'Save failed.', 'err');
        }
      } catch (e) {
        setStatus(saveStatus, 'Network error — is the server running?', 'err');
      }

      btnSave.disabled = false;
      btnSave.textContent = '💾 Save';
    });
  }

  /* "Suggest stronger from analyzer" — takes analyzer input, enhances it,
     populates generator output */
  if (btnSuggest) {
    btnSuggest.addEventListener('click', () => {
      const analyzerInput = document.getElementById('analyzerInput');
      const base = analyzerInput ? analyzerInput.value.trim() : '';

      if (!base) {
        /* Nothing typed — just generate a fresh strong one */
        const pw = generatePassword();
        renderGeneratedPassword(pw);
        document.getElementById('genOutput')
          && document.getElementById('genOutput').scrollIntoView({ behavior:'smooth', block:'center' });
        return;
      }

      /* Build a stronger version: take base chars + inject missing char types
         then pad to max(base.length+4, 16) with random fill */
      const r        = analysePassword(base);
      let stronger   = base;
      const UPPER    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      const LOWER    = 'abcdefghijklmnopqrstuvwxyz';
      const DIGITS   = '0123456789';
      const SYMBOLS  = '!@#$%^&*()_+-=';

      if (!r.hasUp)  stronger += UPPER[Math.floor(Math.random()*26)];
      if (!r.hasLow) stronger += LOWER[Math.floor(Math.random()*26)];
      if (!r.hasDig) stronger += DIGITS[Math.floor(Math.random()*10)];
      if (!r.hasSym) stronger += SYMBOLS[Math.floor(Math.random()*SYMBOLS.length)];

      const targetLen = Math.max(stronger.length + 4, 16);
      const allChars  = UPPER + LOWER + DIGITS + SYMBOLS;
      const arr = new Uint32Array(targetLen - stronger.length);
      crypto.getRandomValues(arr);
      stronger += Array.from(arr, n => allChars[n % allChars.length]).join('');

      /* Shuffle */
      stronger = stronger.split('').sort(() => Math.random() - 0.5).join('');

      renderGeneratedPassword(stronger);
      document.getElementById('genOutput')
        && document.getElementById('genOutput').scrollIntoView({ behavior:'smooth', block:'center' });
    });
  }
}

function setStatus(el, msg, type) {
  if (!el) return;
  el.textContent  = msg;
  el.className    = 'save-status ' + type;
  setTimeout(() => { if (el) el.textContent = ''; }, 4000);
}


/* ══════════════════════════════════════════════════════════════
   SECTION 3b — ANALYZER SAVE TO VAULT
   Elements: #analyzerSaveForm, #analyzerSaveLabel,
             #btnAnalyzerSave, #analyzerSaveStatus,
             #analyzerInput
   ══════════════════════════════════════════════════════════════ */

function initAnalyzerSave() {
  const btnSave    = document.getElementById('btnAnalyzerSave');
  const labelInput = document.getElementById('analyzerSaveLabel');
  const statusEl   = document.getElementById('analyzerSaveStatus');
  const pwInput    = document.getElementById('analyzerInput');

  if (!btnSave) return;

  btnSave.addEventListener('click', async () => {
    const pw    = pwInput    ? pwInput.value.trim()    : '';
    const label = labelInput ? labelInput.value.trim() : '';

    /* Validate */
    if (!pw) {
      setStatus(statusEl, 'Type a password in the analyzer first.', 'err');
      pwInput && pwInput.focus();
      return;
    }
    if (!label) {
      setStatus(statusEl, 'Please enter a label first.', 'err');
      labelInput && labelInput.focus();
      return;
    }

    /* Analyse the manually typed password */
    const r = analysePassword(pw);

    /* Visual loading state */
    btnSave.disabled    = true;
    btnSave.textContent = 'Saving…';

    try {
      const fd = new FormData();
      fd.append('action',   'save');
      fd.append('label',    label);
      fd.append('password', pw);
      fd.append('strength', r.strengthIdx);
      fd.append('entropy',  r.entropy);

      const res  = await fetch('vault-api.php', { method: 'POST', body: fd });
      const data = await res.json();

      if (data.ok) {
        setStatus(statusEl, '\u2713 Saved to vault!', 'ok');
        labelInput.value = '';
        loadVault(); /* refresh table + sidebar + counters */
      } else {
        setStatus(statusEl, data.error || 'Save failed.', 'err');
      }
    } catch (e) {
      setStatus(statusEl, 'Network error \u2014 is the server running?', 'err');
      console.error('[Vault] analyzerSave error:', e);
    }

    btnSave.disabled    = false;
    btnSave.textContent = '\u{1F4BE} Save';
  });

  /* Allow Enter key on label field to trigger save */
  if (labelInput) {
    labelInput.addEventListener('keydown', e => {
      if (e.key === 'Enter') btnSave.click();
    });
  }
}

/* ══════════════════════════════════════════════════════════════
   SECTION 4 — VAULT TABLE + SIDEBAR
   API: vault-api.php?action=list|delete|count
   ══════════════════════════════════════════════════════════════ */

const STRENGTH_NAMES = ['Very Weak','Weak','Fair','Good','Strong'];

async function loadVault(search) {
  const tbody      = document.getElementById('vaultTbody');
  const vaultTotal = document.getElementById('vaultTotal');
  const sidebarList= document.getElementById('sidebarList');
  const sidebarCnt = document.getElementById('sidebarCount');
  const statCount  = document.getElementById('vaultCountStat');

  if (!tbody) return;

  try {
    const qs  = search ? `?action=list&search=${encodeURIComponent(search)}` : '?action=list';
    const res  = await fetch('vault-api.php' + qs);
    const data = await res.json();

    if (!data.ok) { tbody.innerHTML = `<tr><td colspan="6" class="vault-empty">Error: ${data.error}</td></tr>`; return; }

    const rows = data.data;

    /* Count */
    if (vaultTotal) vaultTotal.textContent = rows.length;
    if (sidebarCnt) sidebarCnt.textContent = rows.length;
    if (statCount)  statCount.textContent  = rows.length;

    /* Table */
    if (rows.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6" class="vault-empty">No passwords saved yet. Generate one above!</td></tr>`;
    } else {
      tbody.innerHTML = rows.map(row => {
        const si    = Math.max(0, Math.min(4, Number(row.strength)));
        const label = STRENGTH_NAMES[si] || '—';
        const date  = new Date(row.created_at).toLocaleDateString(undefined, { month:'short', day:'numeric', year:'numeric' });
        const masked = '•'.repeat(Math.min(12, (row.password_plain || '').length || 8));
        return `
          <tr>
            <td style="font-weight:500">${escHtml(row.label)}</td>
            <td><span class="pw-masked">${masked}</span></td>
            <td><span class="str-badge str-${si}">${label}</span></td>
            <td style="font-family:'JetBrains Mono',monospace;font-size:12px">${Number(row.entropy).toFixed(0)} bits</td>
            <td style="font-size:12px;color:var(--text-muted)">${date}</td>
            <td>
              <div class="vault-actions">
                <button class="btn-view-pw" data-pw="${escAttr(row.password_plain)}" data-label="${escAttr(row.label)}">👁 View</button>
                <button class="btn-del-pw"  data-id="${row.id}">🗑 Del</button>
              </div>
            </td>
          </tr>`;
      }).join('');

      /* Wire view + delete buttons */
      tbody.querySelectorAll('.btn-view-pw').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.dataset.label, btn.dataset.pw));
      });
      tbody.querySelectorAll('.btn-del-pw').forEach(btn => {
        btn.addEventListener('click', () => deleteEntry(Number(btn.dataset.id)));
      });
    }

    /* Sidebar */
    if (sidebarList) {
      if (rows.length === 0) {
        sidebarList.innerHTML = '<div class="sidebar-empty">No passwords saved yet.</div>';
      } else {
        sidebarList.innerHTML = rows.slice(0, 20).map((row, idx) => {
          const si    = Math.max(0, Math.min(4, Number(row.strength)));
          const date  = new Date(row.created_at).toLocaleDateString(undefined, { month:'short', day:'numeric' });
          const masked = '•'.repeat(Math.min(12, (row.password_plain || '').length || 8));
          return `
            <div class="sidebar-item" style="animation-delay:${idx*30}ms"
                 data-pw="${escAttr(row.password_plain)}" data-label="${escAttr(row.label)}">
              <div class="sidebar-item-label">${escHtml(row.label)}</div>
              <div class="sidebar-item-pw">${masked}</div>
              <div class="sidebar-item-date">${date} · <span style="color:${scoreColour(si*20+10)}">${STRENGTH_NAMES[si]}</span></div>
            </div>`;
        }).join('');

        sidebarList.querySelectorAll('.sidebar-item').forEach(item => {
          item.addEventListener('click', () => openModal(item.dataset.label, item.dataset.pw));
        });
      }
    }

  } catch (e) {
    if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="vault-empty">Could not load vault. Check your server connection.</td></tr>`;
    console.error('[Vault] loadVault error:', e);
  }
}

async function deleteEntry(id) {
  if (!confirm('Delete this password from your vault?')) return;
  try {
    const fd = new FormData();
    fd.append('action', 'delete');
    fd.append('id', id);
    const res  = await fetch('vault-api.php', { method:'POST', body: fd });
    const data = await res.json();
    if (data.ok) {
      loadVault(document.getElementById('vaultSearch')?.value?.trim() || '');
    } else {
      alert('Delete failed: ' + (data.error || 'Unknown error'));
    }
  } catch (e) {
    alert('Network error during delete.');
    console.error(e);
  }
}

function initVaultSearch() {
  const searchInput = document.getElementById('vaultSearch');
  if (!searchInput) return;
  let timer;
  searchInput.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => loadVault(searchInput.value.trim()), 300);
  });
}


/* ══════════════════════════════════════════════════════════════
   SECTION 5 — PASSWORD VIEW MODAL
   Elements: #modalOverlay, #modalTitle, #modalPw,
             #modalCopy, #modalClose
   ══════════════════════════════════════════════════════════════ */

function openModal(label, pw) {
  const overlay = document.getElementById('modalOverlay');
  const titleEl = document.getElementById('modalTitle');
  const pwEl    = document.getElementById('modalPw');
  if (!overlay) return;

  if (titleEl) titleEl.textContent = label || 'Password';
  if (pwEl)    pwEl.textContent    = pw    || '(empty)';
  overlay.classList.add('open');
}

function initModal() {
  const overlay   = document.getElementById('modalOverlay');
  const copyBtn   = document.getElementById('modalCopy');
  const closeBtn  = document.getElementById('modalClose');
  const pwEl      = document.getElementById('modalPw');
  if (!overlay) return;

  if (closeBtn) {
    closeBtn.addEventListener('click', () => overlay.classList.remove('open'));
  }

  overlay.addEventListener('click', e => {
    if (e.target === overlay) overlay.classList.remove('open');
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') overlay.classList.remove('open');
  });

  if (copyBtn) {
    copyBtn.addEventListener('click', () => {
      const text = pwEl ? pwEl.textContent : '';
      navigator.clipboard.writeText(text).then(() => {
        const orig = copyBtn.textContent;
        copyBtn.textContent = '✓ Copied!';
        setTimeout(() => { copyBtn.textContent = orig; }, 1600);
      }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.cssText = 'position:fixed;opacity:0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
      });
    });
  }
}


/* ══════════════════════════════════════════════════════════════
   SECTION 6 — HELPERS
   ══════════════════════════════════════════════════════════════ */

function escHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;');
}

function escAttr(str) {
  return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}


/* ══════════════════════════════════════════════════════════════
   BOOT — only runs on dashboard (checks for #analyzerInput)
   ══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  /* Bail out if not on dashboard */
  if (!document.getElementById('analyzerInput')) return;

  initAnalyzer();
  initAnalyzerSave();
  initGenerator();
  initModal();
  initVaultSearch();
  loadVault();

  console.info('[SecureVault] Dashboard fully initialised ✓');
});