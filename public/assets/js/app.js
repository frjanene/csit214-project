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



// small helper
const postForm = async (url, data) => {
  const resp = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(data).toString()
  });
  try { return await resp.json(); } catch { return { ok:false, error:'Bad response' }; }
};

// ---------- Booking Details modal hydrator ----------
document.addEventListener('click', (e) => {
  const link = e.target.closest('[data-bs-target="#bookingDetailsModal"][data-bd-title]');
  if (!link) return;

  const modal = document.getElementById('bookingDetailsModal');
  if (!modal) return;

  const $ = (sel) => modal.querySelector(sel);
  const set = (sel, txt) => { if ($(sel) && txt != null) $(sel).textContent = txt; };

  // basic fields
  set('#bd-title',   link.getAttribute('data-bd-title'));
  set('#bd-airport', link.getAttribute('data-bd-airport'));
  set('#bd-date',    link.getAttribute('data-bd-date'));
  set('#bd-time',    link.getAttribute('data-bd-time'));
  set('#bd-people',  link.getAttribute('data-bd-people'));
  set('#bd-flight',  link.getAttribute('data-bd-flight'));
  set('#bd-total',   link.getAttribute('data-bd-total'));

  // payment label (prefer explicit, else infer from total)
  const payLbl = link.getAttribute('data-bd-payment');
  const total  = (link.getAttribute('data-bd-total') || '').toUpperCase();
  set('#bd-payment', payLbl || (total && total !== 'FREE' ? 'Pay Per Use' : 'Membership Access'));

  // reservation id (optional)
  const rid = link.getAttribute('data-bd-res-id');
  if (rid) set('#bd-res-id', `Reservation #${rid}`);

  // store booking id on the modal for actions (cancel)
  const linkBid = link.getAttribute('data-bd-id');
  const cardBid = link.closest('.booking-item')?.querySelector('[data-action="cancel-booking"]')?.getAttribute('data-booking-id');
  const bookingId = linkBid || cardBid || '';
  modal.dataset.bookingId = bookingId;

  // status pill style
  const status = (link.getAttribute('data-bd-status') || 'confirmed').toLowerCase();
  const pill = $('#bd-status');
  if (pill) {
    pill.textContent = status;
    pill.classList.remove('status-ok','status-cancel','status-done');
    if (status === 'cancelled') pill.classList.add('status-cancel');
    else if (status === 'completed' || status === 'completed ') pill.classList.add('status-done');
    else pill.classList.add('status-ok');
  }

  // banner state (success vs cancelled)
  const banner = modal.querySelector('.bd-banner');
  if (banner) {
    banner.classList.toggle('success', status !== 'cancelled');
    banner.classList.toggle('danger',  status === 'cancelled');
    // tweak banner copy
    const titleEl = banner.querySelector('.fw-semibold.fs-6');
    const subEl   = banner.querySelector('.small');
    if (status === 'cancelled') {
      if (titleEl) titleEl.textContent = 'Booking Cancelled';
      if (subEl)   subEl.textContent   = 'This reservation is no longer active.';
    } else {
      if (titleEl) titleEl.textContent = 'Booking Confirmed';
      if (subEl)   subEl.textContent   = 'Your lounge access is ready!';
    }
  }

  // QR — always show the local image asset; if a URL is provided, make it clickable
  const qrUrl  = link.getAttribute('data-bd-qr'); // optional deeplink or server URL
  const qrImgEl = modal.querySelector('.bd-section img[alt="QR"]');
  if (qrImgEl) {
    qrImgEl.src = 'assets/img/demo-qr.png';           // always display the image
    qrImgEl.classList.remove('d-none');
    qrImgEl.alt = 'Entry QR Code';
    if (qrUrl) {
      qrImgEl.style.cursor = 'pointer';
      qrImgEl.onclick = () => window.open(qrUrl, '_blank');
    } else {
      qrImgEl.onclick = null;
      qrImgEl.style.cursor = 'default';
    }
  }

  // Enable/disable Cancel button in modal based on status
  const cancelBtn = document.getElementById('bd-cancel');
  if (cancelBtn) cancelBtn.disabled = (status === 'cancelled' || status === 'completed');

}, { capture: true });

// ---------- Cancel booking (kebab + modal footer) ----------
async function cancelBooking(bookingId, originEl) {
  if (!bookingId) return;

  // simple confirm
  const sure = window.confirm('Cancel this booking? This action cannot be undone.');
  if (!sure) return;

  const res = await postForm('?r=booking_cancel', { id: String(bookingId) });
  if (!res.ok) {
    alert(res.error || 'Failed to cancel booking.');
    return;
  }

  // Update the card UI in-place
  // 1) find the nearest card from originEl if available
  let card = originEl?.closest('.booking-item');
  // 2) else try to locate by data-booking-id
  if (!card) {
    card = document.querySelector(`.booking-item [data-action="cancel-booking"][data-booking-id="${bookingId}"]`)?.closest('.booking-item');
  }

  if (card) {
    // Update status pill
    const pill = card.querySelector('.booking-status');
    if (pill) {
      pill.textContent = 'cancelled';
      pill.classList.remove('status-ok','status-done');
      pill.classList.add('status-cancel');
    }
    // Disable cancel link(s)
    card.querySelectorAll('[data-action="cancel-booking"]').forEach(a => { a.classList.add('disabled'); a.setAttribute('aria-disabled', 'true'); });

    // If this was in the "Upcoming" tab, you may want to move it to "Past" list.
    // (Optional: requires DOM reparenting; skipping for simplicity.)
  }

  // Also reflect in the modal if it's open
  const modal = document.getElementById('bookingDetailsModal');
  if (modal && modal.classList.contains('show') && modal.dataset.bookingId === String(bookingId)) {
    const pill = modal.querySelector('#bd-status');
    if (pill) {
      pill.textContent = 'cancelled';
      pill.classList.remove('status-ok','status-done');
      pill.classList.add('status-cancel');
    }
    const banner = modal.querySelector('.bd-banner');
    if (banner) {
      banner.classList.remove('success'); banner.classList.add('danger');
      const titleEl = banner.querySelector('.fw-semibold.fs-6');
      const subEl   = banner.querySelector('.small');
      if (titleEl) titleEl.textContent = 'Booking Cancelled';
      if (subEl)   subEl.textContent   = 'This reservation is no longer active.';
    }
    // Disable the modal cancel button
    const cancelBtn = document.getElementById('bd-cancel');
    if (cancelBtn) cancelBtn.disabled = true;
  }
}

// kebab-menu "Cancel Booking"
document.addEventListener('click', (e) => {
  const btn = e.target.closest('[data-action="cancel-booking"][data-booking-id]');
  if (!btn) return;
  e.preventDefault();
  const bid = btn.getAttribute('data-booking-id');
  cancelBooking(bid, btn);
});

// modal footer "Cancel Booking"
document.getElementById('bd-cancel')?.addEventListener('click', (e) => {
  e.preventDefault();
  const modal = document.getElementById('bookingDetailsModal');
  const bid = modal?.dataset.bookingId || '';
  cancelBooking(bid, modal);
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
