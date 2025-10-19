// public/assets/js/app.js

// 1) Open auth modal to the correct tab based on the trigger button
document.addEventListener('click', function(e){
  const trigger = e.target.closest('[data-bs-target="#authModal"][data-auth-tab]');
  if(!trigger) return;

  const tabToOpen = trigger.getAttribute('data-auth-tab'); // 'signin' | 'signup'
  const modalEl = document.getElementById('authModal');

  const showTab = () => {
    const id = tabToOpen === 'signup' ? '#signup-tab' : '#signin-tab';
    const el = document.querySelector(id);
    if (el) new bootstrap.Tab(el).show();
  };

  if (modalEl.classList.contains('show')) {
    showTab();
  } else {
    modalEl.addEventListener('shown.bs.modal', function handler(){
      modalEl.removeEventListener('shown.bs.modal', handler);
      showTab();
    });
  }
});

// 2) Password eye toggle (Font Awesome recommended for icons)
document.addEventListener('click', function(e){
  const btn = e.target.closest('.toggle-pass');
  if(!btn) return;

  const input = btn.closest('.fda-input-wrap')?.querySelector('input[data-password]');
  if(!input) return;

  const toText = input.type === 'password';
  input.type = toText ? 'text' : 'password';

  // If using Font Awesome, swap the icon class
  const i = btn.querySelector('i');
  if(i){
    i.classList.toggle('fa-eye', !toText);
    i.classList.toggle('fa-eye-slash', toText);
  }
});

(() => {
  const modalEl = document.getElementById('bookingModal');
  if (!modalEl) return;

  const $ = sel => modalEl.querySelector(sel);

  const backBtn      = $('.booking-back');
  const primaryBtn   = $('#bk-primary');
  const primaryLabel = $('#bk-primary-label');

  let activeStage = 1;

  // ---------- small helpers ----------
  const hhmmToMinutes = (t) => {
    const [h,m] = (t||'').split(':').map(n=>+n||0);
    return h*60 + m;
  };
  const minutesToHours = (min) => {
    const h = Math.floor(min/60);
    const m = min % 60;
    if (!h) return `${m} min`;
    if (!m) return `${h} hour${h>1?'s':''}`;
    return `${h}.${String(Math.round(m/6)).slice(0,1)} hours`; // 30 => .5 style
  };

  function setHeaderForStage(n){
    const title = document.getElementById('bookingModalLabel');
    const sub   = document.getElementById('bookingModalSub');
    const flag  = document.getElementById('bk-secure-flag');

    if (n===2){
      title.textContent = 'Complete Your Booking';
      sub.textContent   = 'Secure payment powered by FlyDreamAir';
      flag.classList.remove('d-none');
    } else if (n===3){
      title.textContent = 'Booking Confirmed';
      sub.textContent   = '';
      flag.classList.add('d-none');
    } else {
      title.textContent = 'Book Lounge Access';
      sub.textContent   = 'Select your preferred time and complete your lounge booking';
      flag.classList.add('d-none');
    }
  }

  // Build time options with occupancy text like "11:30 — 90/120"
  function populateTimes(map){
    const startSel = $('#bk-start');
    const endSel   = $('#bk-end');
    startSel.innerHTML = '';
    endSel.innerHTML   = '';

    Object.entries(map).forEach(([t, occ]) => {
      const add = (sel) => {
        const o = document.createElement('option');
        o.value = t;
        o.textContent = `${t} — ${occ}`;
        sel.appendChild(o);
      };
      add(startSel); add(endSel);
    });

    if (startSel.options.length) startSel.selectedIndex = Math.min(1, startSel.options.length-1);
    if (endSel.options.length)   endSel.selectedIndex   = Math.min(3, endSel.options.length-1);
    updateSelectedSlot();
  }

  const selectedOccText = (sel) => {
    const txt = sel.selectedOptions[0]?.textContent || '';
    const parts = txt.split('—');
    return parts[1]?.trim() || '';
  };

  function updateSelectedSlot(){
    const date = $('#bk-date').value;
    const st   = $('#bk-start').value;
    const et   = $('#bk-end').value;

    $('#bk-slot-text').textContent = (date && st && et) ? `${st} – ${et} on ${date}` : '—';
    $('#bk-slot-occ').textContent  = selectedOccText($('#bk-end')) || selectedOccText($('#bk-start')) || '—';

    // mini summary box (Stage 1)
    const guests = +$('#bk-guests').value || 0;
    const people = 1 + guests;
    const price  = +(modalEl.dataset.price || 0);
    const flight = ($('#bk-flight').value || '').toUpperCase();
    const depT   = $('#bk-dep-time').value ? ` (${ $('#bk-dep-time').value })` : '';

    const ready = !!(date && st && et && flight);
    $('#bk-summary').classList.toggle('d-none', !ready);

    $('#sum-date').textContent   = date || '—';
    $('#sum-time').textContent   = (st && et) ? `${st} – ${et}` : '—';
    $('#sum-flight').textContent = flight ? `${flight}${depT}` : '—';
    $('#sum-people').textContent = `${people} ${people>1?'people':'person'}`;
    $('#sum-occ').textContent    = $('#bk-slot-occ').textContent;
    $('#sum-total').textContent  = `$${(price*people).toFixed(0)}`;

    validateStage1();
  }

  function validateStage1(){
    if (activeStage !== 1) return; // only controls the Stage-1 CTA
    const hasFlight = ($('#bk-flight').value || '').trim().length > 0 && !$('#bk-flight-card').classList.contains('d-none');
    const hasDate   = !!$('#bk-date').value;
    const hasStart  = !!$('#bk-start').value;
    const hasEnd    = !!$('#bk-end').value;
    primaryBtn.disabled = !(hasFlight && hasDate && hasStart && hasEnd);
    primaryLabel.textContent = 'Confirm Booking';
  }

  // Stage 2 button enablement (simple checks)
  function validatePayment(){
    const ok = $('#pay-name')?.value.trim()
           && $('#pay-number')?.value.replace(/\s+/g,'').length >= 12
           && /^\d{2}\/\d{2}$/.test($('#pay-exp')?.value.trim() || '')
           && /^\d{3,4}$/.test($('#pay-cvv')?.value.trim() || '')
           && $('#pay-addr')?.value.trim();
    const bigPay = document.getElementById('btn-pay-stage2');
    if (bigPay) bigPay.disabled = !ok;
    // footer is hidden on Stage 2, so we don't mirror state there
  }

  // Hydrate Stage 2 summary + labels
  function hydrateStage2(){
    const price   = +(modalEl.dataset.price || 0);
    const title   = $('#bk-title').textContent;
    const airport = $('#bk-airport').textContent;
    const date    = $('#sum-date').textContent;
    const time    = $('#sum-time').textContent;
    const guests  = $('#sum-people').textContent;
    const flight  = ($('#bk-flight').value || '').toUpperCase();
    const depT    = $('#bk-dep-time').value;

    // duration from selected start/end
    const [st, et] = [$('#bk-start').value, $('#bk-end').value];
    const mins = Math.max(0, hhmmToMinutes(et) - hhmmToMinutes(st));
    const durLabel = mins ? `(${minutesToHours(mins)})` : '';

    // Fill the Stage-2 panel
    $('#sum2-title').textContent   = title;
    $('#sum2-airport').textContent = airport;
    $('#sum2-amount').textContent  = price.toString();
    $('#sum2-date').textContent    = date;
    $('#sum2-time').textContent    = time;
    $('#sum2-duration').textContent= durLabel || '';
    $('#sum2-people').textContent  = guests.replace(' people','').replace(' person','');
    $('#sum2-flight').textContent  = flight;
    $('#sum2-flight-sub').textContent = depT ? `Departs ${depT}` : '';

    // label for the big button only
    const payLabel = `Pay $${price} - Complete Booking`;
    const payLblEl = document.getElementById('btn-pay-label');
    if (payLblEl) payLblEl.textContent = payLabel;
  }

  function showStage(n){
    activeStage = n;

    // toggle stages
    [...modalEl.querySelectorAll('.booking-stage')]
      .forEach(s => s.classList.toggle('d-none', +s.dataset.stage !== n));

    // header + secure flag
    backBtn.classList.toggle('d-none', n === 1);
    setHeaderForStage(n);

    // hide footer entirely on Stage 2
    const footer = modalEl.querySelector('.modal-footer');
    if (footer) footer.classList.toggle('d-none', n === 2);

    if (n===1){
      validateStage1();
    } else if (n===2){
      // nothing in footer to manage
    } else {
      // Stage 3: footer is visible; primary becomes "Close"
      if (footer) footer.classList.remove('d-none');
      primaryBtn.disabled = false;
      primaryLabel.textContent = 'Close';
    }
  }

  // ---------- bootstrap from card click ----------
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-bs-target="#bookingModal"][data-lounge-title]');
    if (!btn) return;

    showStage(1);

    $('#bk-title').textContent   = btn.getAttribute('data-lounge-title')  || '';
    $('#bk-airport').textContent = btn.getAttribute('data-lounge-airport')|| '';
    $('#bk-hours').textContent   = btn.getAttribute('data-lounge-hours')  || '';
    $('#bk-occ').textContent     = btn.getAttribute('data-lounge-occ')    || '—';
    modalEl.dataset.price        = btn.getAttribute('data-lounge-price')  || '0';
    $('#bk-price-chip').textContent = `$${modalEl.dataset.price}`;
    const img = btn.getAttribute('data-lounge-img') || '';
    const imgEl = $('#bk-thumb'); if (imgEl) imgEl.src = img;

    // reset inputs
    $('#bk-flight').value = '';
    $('#bk-date').value   = '';
    $('#bk-dep-time').value = '';
    $('#bk-start').innerHTML = '';
    $('#bk-end').innerHTML   = '';
    $('#bk-guests').value = '0';
    $('#bk-slot-text').textContent = '—';
    $('#bk-slot-occ').textContent  = '—';
    $('#bk-summary').classList.add('d-none');
    $('#bk-flight-card').classList.add('d-none');
    $('#bk-after-flight').classList.add('d-none');

    validateStage1();
  });

  // ---------- flight lookup (simulated) ----------
  $('#bk-flight').addEventListener('change', () => {
    const v = $('#bk-flight').value.trim().toUpperCase();
    if (!v){ validateStage1(); return; }

    // demo date one week ahead
    const d = new Date(); d.setDate(d.getDate()+7);
    const yyyy = d.getFullYear(), mm = String(d.getMonth()+1).padStart(2,'0'), dd = String(d.getDate()).padStart(2,'0');
    if (!$('#bk-date').value) $('#bk-date').value = `${yyyy}-${mm}-${dd}`;

    $('#bk-dep-time').value = '14:30';
    $('#bk-flight-title').textContent = `FlyDreamAir ${v}`;
    $('#bk-dep-dt').textContent = `${mm}/${dd}/${yyyy} at 14:30`;
    $('#bk-arr-dt').textContent = `${mm}/${dd}/${yyyy} at 23:45`;
    $('#bk-flight-card').classList.remove('d-none');
    $('#bk-after-flight').classList.remove('d-none');

    // available times with occupancy (demo data)
    populateTimes({
      '11:00':'88/120','11:30':'90/120','12:00':'94/120',
      '12:30':'98/120','13:00':'105/120','13:30':'110/120','14:00':'89/120'
    });

    updateSelectedSlot();
  });

  // ---------- reactive updates ----------
  ['bk-date','bk-start','bk-end','bk-guests'].forEach(id=>{
    $('#'+id).addEventListener('change', updateSelectedSlot);
  });

  // ---------- footer primary CTA (Stage 1 only) ----------
  primaryBtn.addEventListener('click', () => {
    if (activeStage === 1){
      hydrateStage2();
      showStage(2);
      validatePayment();
    } else if (activeStage === 3){
      bootstrap.Modal.getInstance(modalEl)?.hide();
    }
  });

  backBtn.addEventListener('click', () => showStage(Math.max(1, activeStage-1)));
  modalEl.addEventListener('hidden.bs.modal', () => showStage(1));

  // BIG Pay button (Stage 2)
  document.getElementById('btn-pay-stage2')?.addEventListener('click', () => {
    const btn = document.getElementById('btn-pay-stage2');
    if (!btn || btn.disabled) return;
    $('#done-title').textContent    = $('#sum2-title').textContent;
    $('#done-airport').textContent  = $('#sum2-airport').textContent;
    $('#done-datetime').textContent = `${$('#sum2-date').textContent} · ${$('#sum2-time').textContent}`;
    showStage(3);
  });

  // hook up payment validation inputs
  ['pay-name','pay-number','pay-exp','pay-cvv','pay-addr'].forEach(id=>{
    document.getElementById(id)?.addEventListener('input', validatePayment);
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


// ===== Membership Upgrade modal =====
(() => {
  const modalEl = document.getElementById('upgradeModal');
  if (!modalEl) return;

  const $ = (sel) => modalEl.querySelector(sel);

  function setHeader(stage){
    const title = $('#ug-title');
    const sub   = $('#ug-sub');
    const back  = modalEl.querySelector('.ug-back');
    const secure= $('#ug-secure-flag');

    if (stage === 1){
      title.textContent = 'Confirm Membership Upgrade';
      sub.textContent   = 'Are you sure you want to upgrade your membership?';
      title.classList.add('text-center');  sub.classList.add('text-center');
      back.classList.add('d-none');
      secure.classList.add('d-none');
    } else {
      title.textContent = 'Complete Your Upgrade';
      sub.textContent   = 'Secure payment powered by FlyDreamAir';
      title.classList.remove('text-center'); sub.classList.remove('text-center');
      back.classList.remove('d-none');
      secure.classList.remove('d-none');
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

    document.getElementById('ug-plan-chip').textContent = plan;
    $('#ug-price').textContent = `$${price}`;

    const ul = $('#ug-benefits');
    ul.innerHTML = '';
    (benefits || []).slice(0,4).forEach((b,i) => {
      const li = document.createElement('li'); li.textContent = b; ul.appendChild(li);
      const slot = modalEl.querySelector('#ug-b'+(i+1)); if (slot) slot.textContent = b;
    });

    $('#ug-plan-tag').textContent = `${plan} Membership`;
    $('#ug-price-num').textContent = price;
    $('#ug-pay-label').textContent = `Pay $${price} - Complete Upgrade`;

    ['ug-name','ug-number','ug-exp','ug-cvv','ug-addr'].forEach(id => { const el = document.getElementById(id); if (el) el.value=''; });
    document.getElementById('ug-pay').disabled = true;

    showStage(1);
    new bootstrap.Modal(modalEl).show();
  }

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

  function validatePay(){
    const ok =
      ($('#ug-name').value || '').trim() &&
      ($('#ug-number').value || '').replace(/\s+/g,'').length >= 12 &&
      /^\d{2}\/\d{2}$/.test(($('#ug-exp').value || '').trim()) &&
      /^\d{3,4}$/.test(($('#ug-cvv').value || '').trim()) &&
      ($('#ug-addr').value || '').trim();
    document.getElementById('ug-pay').disabled = !ok;
  }
  ['ug-name','ug-number','ug-exp','ug-cvv','ug-addr'].forEach(id=>{
    document.getElementById(id)?.addEventListener('input', validatePay);
  });

  document.getElementById('ug-pay')?.addEventListener('click', () => {
    if (document.getElementById('ug-pay').disabled) return;
    const plan = modalEl.dataset.plan || '—';
    bootstrap.Modal.getInstance(modalEl)?.hide();

    const toastEl = document.getElementById('membershipToast');
    if (toastEl){
      toastEl.querySelector('#toast-plan').textContent = plan;
      new bootstrap.Toast(toastEl, { delay: 4500 }).show();
    }
  });
})();
