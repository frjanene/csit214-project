(() => {
  'use strict';

  // Helper: open auth modal and show a specific tab ("signin" | "signup")
  function openAuthAndShow(tab) {
    const modalEl = document.getElementById('authModal');
    if (!modalEl) return;

    const tabBtnId = tab === 'signup' ? '#signup-tab' : '#signin-tab';
    const ensureTab = () => {
      const el = document.querySelector(tabBtnId);
      if (el) new bootstrap.Tab(el).show();
    };

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    if (modalEl.classList.contains('show')) {
      ensureTab();
    } else {
      modalEl.addEventListener('shown.bs.modal', function handler() {
        modalEl.removeEventListener('shown.bs.modal', handler);
        ensureTab();
      });
      modal.show();
    }
  }

  // 1) Open auth modal to the correct tab when page CTA buttons are clicked
  document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-bs-target="#authModal"][data-auth-tab]');
    if (!trigger) return;
    const tabToOpen = trigger.getAttribute('data-auth-tab'); // 'signin' | 'signup'
    // Modal will open via Bootstrap data attributes; we just ensure the right tab:
    const modalEl = document.getElementById('authModal');
    if (!modalEl) return;

    const showTab = () => {
      const id = tabToOpen === 'signup' ? '#signup-tab' : '#signin-tab';
      const el = document.querySelector(id);
      if (el) new bootstrap.Tab(el).show();
    };

    if (modalEl.classList.contains('show')) {
      showTab();
    } else {
      modalEl.addEventListener('shown.bs.modal', function handler() {
        modalEl.removeEventListener('shown.bs.modal', handler);
        showTab();
      });
    }
  });

  // 2) Password eye toggle (Font Awesome recommended for icons)
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.toggle-pass');
    if (!btn) return;

    const input = btn.closest('.fda-input-wrap')?.querySelector('input[data-password]');
    if (!input) return;

    const toText = input.type === 'password';
    input.type = toText ? 'text' : 'password';

    const i = btn.querySelector('i');
    if (i) {
      i.classList.toggle('fa-eye', !toText);
      i.classList.toggle('fa-eye-slash', toText);
    }
  });

  // 3) If redirected with #signin or #signup, auto-open modal and activate proper tab
  document.addEventListener('DOMContentLoaded', () => {
    if (location.hash === '#signin' || location.hash === '#signup') {
      openAuthAndShow(location.hash.substring(1)); // 'signin' | 'signup'
    }
  });
})();



// Booking Details modal hydrator
document.addEventListener('click', (e) => {
  const link = e.target.closest('[data-bs-target="#bookingDetailsModal"][data-bd-title]');
  if (!link) return;

  const modal = document.getElementById('bookingDetailsModal');
  if (!modal) return;

  const $ = (sel) => modal.querySelector(sel);

  const set = (sel, txt) => { if (txt) $(sel).textContent = txt; };

  set('#bd-title',   link.getAttribute('data-bd-title'));
  set('#bd-airport', link.getAttribute('data-bd-airport'));
  set('#bd-date',    link.getAttribute('data-bd-date'));
  set('#bd-time',    link.getAttribute('data-bd-time'));
  set('#bd-people',  link.getAttribute('data-bd-people'));
  set('#bd-flight',  link.getAttribute('data-bd-flight'));
  set('#bd-total',   link.getAttribute('data-bd-total'));

  // status pill style
  const status = link.getAttribute('data-bd-status') || 'confirmed';
  const pill = $('#bd-status');
  pill.textContent = status;
  pill.classList.remove('status-ok','status-cancel','status-done');
  if (status === 'cancelled') pill.classList.add('status-cancel');
  else if (status === 'completed') pill.classList.add('status-done');
  else pill.classList.add('status-ok'); // default

  // banner text for cancelled vs confirmed
  const banner = modal.querySelector('.bd-banner');
  banner.classList.toggle('success', status !== 'cancelled');
  banner.classList.toggle('danger',  status === 'cancelled');
});


// ===== Membership Upgrade modal (server-backed) =====
(() => {
  const modalEl = document.getElementById('upgradeModal');
  if (!modalEl) return;
  const form = modalEl.querySelector('#ug-form');

  const $ = (sel) => modalEl.querySelector(sel);

  function setHeader(stage){
    const title = $('#ug-title'), sub = $('#ug-sub');
    const back = modalEl.querySelector('.ug-back'), secure = $('#ug-secure-flag');
    if (stage === 1){
      title.textContent = 'Confirm Membership Upgrade';
      sub.textContent   = 'Are you sure you want to upgrade your membership?';
      title.classList.add('text-center'); sub.classList.add('text-center');
      back.classList.add('d-none'); secure.classList.add('d-none');
    } else {
      title.textContent = 'Complete Your Upgrade';
      sub.textContent   = 'Secure payment powered by FlyDreamAir';
      title.classList.remove('text-center'); sub.classList.remove('text-center');
      back.classList.remove('d-none'); secure.classList.remove('d-none');
    }
  }
  const showStage = (n) => {
    [...modalEl.querySelectorAll('.upgrade-stage')]
      .forEach(s => s.classList.toggle('d-none', +s.dataset.stage !== n));
    document.getElementById('ug-footer').classList.toggle('d-none', n !== 1);
    setHeader(n);
  };

  function openFor(plan, price, benefits) {
    modalEl.dataset.plan  = plan;
    modalEl.dataset.price = String(price);
    document.getElementById('ug-plan-input').value = plan;

    $('#ug-plan-chip').textContent = plan.toUpperCase();
    $('#ug-price').textContent     = `$${price}`;
    $('#ug-plan-tag').textContent  = `${plan.toUpperCase()} Membership`;
    $('#ug-price-num').textContent = `$${price}`;
    $('#ug-pay-label').textContent = `Pay $${price} - Complete Upgrade`;

    const ul = $('#ug-benefits'); ul.innerHTML = '';
    (benefits || []).slice(0,4).forEach((b,i) => {
      const li = document.createElement('li'); li.textContent = b; ul.appendChild(li);
      const slot = modalEl.querySelector('#ug-b'+(i+1)); if (slot) slot.textContent = b;
    });

    ['ug-name','ug-number','ug-exp','ug-cvv','ug-addr'].forEach(id => {
      const el = document.getElementById(id); if (el) el.value='';
    });
    document.getElementById('ug-pay').disabled = true;

    showStage(1);
    new bootstrap.Modal(modalEl).show();
  }

  // Open from tier cards
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-upgrade-tier');
    if (!btn) return;
    const plan = btn.getAttribute('data-plan') || '—';
    const price = +(btn.getAttribute('data-price') || 0);
    let benefits = [];
    try { benefits = JSON.parse(btn.getAttribute('data-benefits') || '[]'); } catch {}
    openFor(plan, price, benefits);
  });

  document.getElementById('ug-next')?.addEventListener('click', () => showStage(2));
  modalEl.querySelector('.ug-back')?.addEventListener('click', () => showStage(1));

  // Simple client validation enabling the Pay button
  function validatePay(){
    const name = ($('#ug-name').value || '').trim();
    const num  = ($('#ug-number').value || '').replace(/\s+/g,'');
    const exp  = ($('#ug-exp').value || '').trim();
    const cvv  = ($('#ug-cvv').value || '').trim();
    const addr = ($('#ug-addr').value || '').trim();
    const ok = name && /^\d{12,19}$/.test(num) && /^\d{2}\/\d{2}$/.test(exp) && /^\d{3,4}$/.test(cvv) && addr;
    document.getElementById('ug-pay').disabled = !ok;
  }
  ['ug-name','ug-number','ug-exp','ug-cvv','ug-addr'].forEach(id=>{
    document.getElementById(id)?.addEventListener('input', validatePay);
  });

  // After successful POST, server flashes; here we just allow submit
  form?.addEventListener('submit', (e)=>{
    // allow native submit; toast handled after redirect by server flash
  });
})();


// ===== Auto-submit for Find Lounges filters =====
(function(){
  const form = document.getElementById('loungeFilter');
  if (!form) return;

  // Debounce helper
  const debounce = (fn, wait = 500) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(null, args), wait);
    };
  };

  const submitForm = () => form.requestSubmit ? form.requestSubmit() : form.submit();

  // 1) Text search (debounced)
  form.querySelectorAll('input[name="q"][data-autosubmit="debounce"]').forEach(inp => {
    inp.addEventListener('input', debounce(submitForm, 500));
  });

  // 2) Country select (instant)
  form.querySelectorAll('select[name="country"][data-autosubmit="instant"]').forEach(sel => {
    sel.addEventListener('change', submitForm);
  });

  // 3) Amenity checkboxes (instant)
  form.querySelectorAll('input[name="amen[]"][data-autosubmit="instant"]').forEach(cb => {
    cb.addEventListener('change', submitForm);
  });
})();


// ===== Profile: edit/save toggle =====
(() => {
  const form = document.getElementById('profileForm');
  if (!form) return;

  const inputs = [...form.querySelectorAll('.profile-input')];
  const btnEdit   = document.querySelector('.profile-edit-btn');
  const btnSave   = document.querySelector('.profile-save-btn');
  const btnCancel = document.querySelector('.profile-cancel-btn');

  // remember original values for cancel
  let orig = null;

  function setEditing(on) {
    inputs.forEach(i => i.disabled = !on);
    btnEdit?.classList.toggle('d-none', on);
    btnSave?.classList.toggle('d-none', !on);
    btnCancel?.classList.toggle('d-none', !on);
  }

  btnEdit?.addEventListener('click', () => {
    // snapshot original values
    orig = inputs.map(i => ({ el: i, val: i.value }));
    setEditing(true);
  });

  btnCancel?.addEventListener('click', () => {
    if (orig) orig.forEach(o => { o.el.value = o.val; });
    setEditing(false);
  });

  // Just in case user submits without clicking Save button:
  form.addEventListener('submit', () => {
    inputs.forEach(i => i.disabled = false);
  });
})();
