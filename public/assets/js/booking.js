(() => {
  const modalEl = document.getElementById('bookingModal');
  if (!modalEl) return;

  console.log('[booking] modal dataset on load:', JSON.parse(JSON.stringify(modalEl.dataset)));

  const $ = sel => modalEl.querySelector(sel);

  const backBtn      = $('.booking-back');
  const primaryBtn   = $('#bk-primary');
  const primaryLabel = $('#bk-primary-label');

  let activeStage = 1;
  let needsPaymentFlag = true; // kept in sync as we learn more
  const selected = { loungeId: null, isPremium: 0 };

  // ---------- helpers ----------
  const hhmmToMinutes = (t) => {
    const [h,m] = (t||'').split(':').map(n=>+n||0);
    return h*60 + m;
  };
  const minutesToHours = (min) => {
    const h = Math.floor(min/60);
    const m = min % 60;
    if (!h) return `${m} min`;
    if (!m) return `${h} hour${h>1?'s':''}`;
    return `${h}.${String(Math.round(m/6)).slice(0,1)} hours`;
  };
  const debounce = (fn, wait=400) => {
    let t; return (...args) => { clearTimeout(t); t = setTimeout(()=>fn(...args), wait); };
  };

  const postForm = async (url, data) => {
    const resp = await fetch(url, {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body: new URLSearchParams(data).toString()
    });
    try { return await resp.json(); } catch { return { ok:false, error:'Bad response' }; }
  };

  // ---------- membership-aware UI helpers ----------
  const planFromDataset = () => ({
    slug:           (modalEl.dataset.planSlug    || 'basic').toLowerCase(),
    normal_access:  (modalEl.dataset.planNormal  || 'pay_per_use').toLowerCase(),
    premium_access: (modalEl.dataset.planPremium || 'pay_per_use').toLowerCase(),
  });

  const loungeCoveredByPlan = (plan, isPremium) => {
    if (isPremium) return plan.premium_access === 'free';
    return plan.normal_access === 'free';
  };

  function setPlanNote(plan, covered){
    const noteEl = document.getElementById('bk-plan-note');
    if (!noteEl) return;
    if (covered) {
      noteEl.textContent = `Included with ${String(plan.slug||'basic').toUpperCase()} membership`;
    } else {
      noteEl.textContent = 'Basic member · pay-per-use for all lounges';
    }
  }

  function toggleStage1PaymentCard(show){
    const card = modalEl.querySelector('.method-card');
    if (!card) return;
    card.classList.toggle('d-none', !show);
  }

  function toggleStage2PaymentForm(show){
    modalEl.querySelector('.pay-card')?.classList.toggle('d-none', !show);
    modalEl.querySelector('.secure-note')?.classList.toggle('d-none', !show);
    modalEl.querySelector('.tos')?.classList.toggle('d-none', !show);
  }

  function setupStage2Button(covered){
    const payBtn = document.getElementById('btn-pay-stage2');
    const payLbl = document.getElementById('btn-pay-label');
    if (!payBtn || !payLbl) return;

    if (covered){
      payLbl.textContent = 'Confirm Booking';
      payBtn.disabled = false;
      payBtn.classList.remove('d-none');
    } else {
      const price = +(modalEl.dataset.price || 0);
      payLbl.textContent = `Pay $${price} - Complete Booking`;
      payBtn.disabled = true; // will be enabled by validatePayment()
      payBtn.classList.remove('d-none');
    }
  }

  function setHeaderForStage(n){
    const title = document.getElementById('bookingModalLabel');
    const sub   = document.getElementById('bookingModalSub');
    const flag  = document.getElementById('bk-secure-flag');

    if (n===2){
      title.textContent = 'Complete Your Booking';
      if (needsPaymentFlag) {
        sub.textContent = 'Secure payment powered by FlyDreamAir';
        flag.classList.remove('d-none');
      } else {
        sub.textContent = 'Review and confirm your booking';
        flag.classList.add('d-none');
      }
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

  // ---------- time options + summary ----------
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

    const covered = !needsPaymentFlag;
    $('#sum-total').textContent  = covered ? '$0' : `$${(price*people).toFixed(0)}`;

    validateStage1();
  }

  function validateStage1(){
    if (activeStage !== 1) return;
    const hasFlight = ($('#bk-flight').value || '').trim().length > 0 && !$('#bk-flight-card').classList.contains('d-none');
    const hasDate   = !!$('#bk-date').value;
    const hasStart  = !!$('#bk-start').value;
    const hasEnd    = !!$('#bk-end').value;
    primaryBtn.disabled = !(hasFlight && hasDate && hasStart && hasEnd);
    primaryLabel.textContent = 'Confirm Booking';
  }

  function validatePayment(){
    if (!needsPaymentFlag) {
      const bigPay = document.getElementById('btn-pay-stage2');
      if (bigPay) bigPay.disabled = false;
      return;
    }
    const ok = $('#pay-name')?.value.trim()
           && $('#pay-number')?.value.replace(/\s+/g,'').length >= 12
           && /^\d{2}\/\d{2}$/.test($('#pay-exp')?.value.trim() || '')
           && /^\d{3,4}$/.test($('#pay-cvv')?.value.trim() || '')
           && $('#pay-addr')?.value.trim();
    const bigPay = document.getElementById('btn-pay-stage2');
    if (bigPay) bigPay.disabled = !ok;
  }

  // ---------- stage 2 hydration ----------
  function hydrateStage2(){
    const covered = !needsPaymentFlag;
    const unitPrice = +(modalEl.dataset.price || 0);

    const title   = $('#bk-title').textContent;
    const airport = $('#bk-airport').textContent;
    const date    = $('#sum-date').textContent;
    const time    = $('#sum-time').textContent;
    const guests  = $('#sum-people').textContent;
    const flight  = ($('#bk-flight').value || '').toUpperCase();
    const depT    = $('#bk-dep-time').value;

    const [st, et] = [$('#bk-start').value, $('#bk-end').value];
    const mins = Math.max(0, hhmmToMinutes(et) - hhmmToMinutes(st));
    const durLabel = mins ? `(${minutesToHours(mins)})` : '';

    $('#sum2-title').textContent   = title;
    $('#sum2-airport').textContent = airport;
    $('#sum2-amount').textContent  = covered ? '0' : unitPrice.toString();
    $('#sum2-date').textContent    = date;
    $('#sum2-time').textContent    = time;
    $('#sum2-duration').textContent= durLabel || '';
    $('#sum2-people').textContent  = guests.replace(' people','').replace(' person','');
    $('#sum2-flight').textContent  = flight;
    $('#sum2-flight-sub').textContent = depT ? `Departs ${depT}` : '';

    toggleStage2PaymentForm(!covered);
    setupStage2Button(covered);
  }

  function showStage(n){
    activeStage = n;
    [...modalEl.querySelectorAll('.booking-stage')]
      .forEach(s => s.classList.toggle('d-none', +s.dataset.stage !== n));
    setHeaderForStage(n);
    backBtn.classList.toggle('d-none', n === 1 || n === 3);

    const footer = modalEl.querySelector('.modal-footer');
    if (footer) footer.classList.toggle('d-none', n === 2 || n === 3);

    if (n === 1) validateStage1();
    if (n === 3) {
      document.getElementById('btn-done-continue')?.addEventListener('click', () => {
        bootstrap.Modal.getInstance(modalEl)?.hide();
      }, { once:true });
    }
  }

  // ---------- bootstrap from card click ----------
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-bs-target="#bookingModal"][data-lounge-title]');
    if (!btn) return;

    showStage(1);

    selected.loungeId  = parseInt(btn.getAttribute('data-lounge-id') || '0', 10);
    selected.isPremium = parseInt(btn.getAttribute('data-lounge-premium') || '0', 10);

    $('#bk-title').textContent   = btn.getAttribute('data-lounge-title')  || '';
    $('#bk-airport').textContent = btn.getAttribute('data-lounge-airport')|| '';
    $('#bk-hours').textContent   = btn.getAttribute('data-lounge-hours')  || '';
    $('#bk-occ').textContent     = btn.getAttribute('data-lounge-occ')    || '—';
    const unit = +(btn.getAttribute('data-lounge-price') || '0');
    modalEl.dataset.price = String(unit);
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
    $('#bk-flight-airport').textContent = 'Enter flight (e.g., FD123)…';

    // membership coverage pre-check (so stage 1 can hide method card immediately)
    const plan = planFromDataset();
    const covered = loungeCoveredByPlan(plan, !!selected.isPremium);
    needsPaymentFlag = !covered;

    // Stage-1 price chip reflects coverage
    $('#bk-price-chip').textContent = covered ? '$0' : `$${unit}`;

    console.log('[booking] open modal for lounge:', {
      isPremium: !!selected.isPremium,
      plan,
      covered,
      needsPaymentFlag
    });

    setPlanNote(plan, covered);
    toggleStage1PaymentCard(!covered);

    // Stage 2 defaults reflecting current coverage
    toggleStage2PaymentForm(!covered);
    setupStage2Button(covered);

    validateStage1();
  });

  // ---------- flight lookup (DB-backed) ----------
  const doFlightLookup = async () => {
    const vRaw = $('#bk-flight').value;
    const v = vRaw.trim().toUpperCase();
    const statusEl = document.getElementById('bk-flight-airport');

    if (!v) {
      $('#bk-flight-card').classList.add('d-none');
      $('#bk-after-flight').classList.add('d-none');
      statusEl.textContent = 'Enter flight (e.g., FD123)…';
      validateStage1();
      return;
    }

    if (!/^[A-Z]{2,3}\d{1,4}$/.test(v)) {
      statusEl.textContent = 'Format looks off (try e.g., FD123)';
      validateStage1();
      return;
    }

    statusEl.textContent = 'Searching…';

    const res = await postForm('?r=flight_lookup', { flight: v, date: $('#bk-date').value });
    if (!res.ok) {
      $('#bk-flight-card').classList.add('d-none');
      $('#bk-after-flight').classList.add('d-none');
      statusEl.textContent = res.error || 'Flight not found for selected date.';
      validateStage1();
      return;
    }

    if (res.flight_date) $('#bk-date').value = res.flight_date;

    // hydrate UI from DB
    $('#bk-dep-time').value = res.dep.sched.substring(11,16);
    const equip = res.equipment ? ` · ${res.equipment}` : '';
    $('#bk-flight-title').textContent = `${res.airline}${res.number}${equip}`;

    const d = new Date(res.dep.sched.replace(' ', 'T'));
    const a = new Date(res.arr.sched.replace(' ', 'T'));
    const mm = String(d.getMonth()+1).padStart(2,'0');
    const dd = String(d.getDate()).padStart(2,'0');
    const yyyy = d.getFullYear();

    $('#bk-dep-dt').textContent = `${mm}/${dd}/${yyyy} at ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
    $('#bk-arr-dt').textContent = `${String(a.getMonth()+1).padStart(2,'0')}/${String(a.getDate()).padStart(2,'0')}/${a.getFullYear()} at ${String(a.getHours()).padStart(2,'0')}:${String(a.getMinutes()).padStart(2,'0')}`;

    $('#bk-flight-card').classList.remove('d-none');
    $('#bk-after-flight').classList.remove('d-none');
    statusEl.textContent = `Departing from ${res.dep.airport_name} (${res.dep.airport_iata})`;

    populateTimes({
      '11:00':'88/120','11:30':'90/120','12:00':'94/120',
      '12:30':'98/120','13:00':'105/120','13:30':'110/120','14:00':'89/120'
    });
    updateSelectedSlot();
  };

  const flightInput = document.getElementById('bk-flight');
  flightInput.addEventListener('input', debounce(doFlightLookup, 400));
  flightInput.addEventListener('change', doFlightLookup);
  flightInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); doFlightLookup(); }
  });

  $('#bk-date').addEventListener('change', () => {
    if (($('#bk-flight').value || '').trim()) doFlightLookup();
  });

  // ---------- reactive updates ----------
  ['bk-start','bk-end','bk-guests'].forEach(id=>{
    $('#'+id).addEventListener('change', updateSelectedSlot);
  });

  // ---------- Stage 1 primary -> server quote, then Stage 2 review ----------
  primaryBtn.addEventListener('click', async () => {
    if (activeStage !== 1) return;

    const people = 1 + (+$('#bk-guests').value || 0);
    const payload = {
      lounge_id:  String(selected.loungeId || ''),
      visit_date: $('#bk-date').value,
      start_time: $('#bk-start').value,
      end_time:   $('#bk-end').value,
      people:     String(people),
      // NEW: tell server what plan we have and lounge type
      plan_slug:        (modalEl.dataset.planSlug || 'basic'),
      lounge_is_premium:String(selected.isPremium ? 1 : 0)
    };

    console.log('[booking] quote payload:', payload);

    const q = await postForm('?r=booking_quote', payload);
    console.log('[booking] /booking_quote response:', q);
    if (!q.ok) return;

    // Trust the server: if total==0, no payment; if >0, payment required
    needsPaymentFlag = !!q.needs_payment;

    const plan = q.plan || planFromDataset();
    setPlanNote(plan, !needsPaymentFlag);
    toggleStage1PaymentCard(needsPaymentFlag);

    // Always show Stage 2 for review.
    hydrateStage2();
    showStage(2);
    validatePayment();

    console.log('[booking] proceeding to stage 2; final flag:', needsPaymentFlag);
  });

  backBtn.addEventListener('click', () => showStage(Math.max(1, activeStage-1)));
  modalEl.addEventListener('hidden.bs.modal', () => showStage(1));

  // ---------- Stage 2 big button -> commit booking ----------
  document.getElementById('btn-pay-stage2')?.addEventListener('click', async () => {
    const btn = document.getElementById('btn-pay-stage2');
    if (!btn) return;

    if (needsPaymentFlag && btn.disabled) return;

    const people = 1 + (+$('#bk-guests').value || 0);
    const payload = {
      lounge_id:  String(selected.loungeId || ''),
      visit_date: $('#bk-date').value,
      start_time: $('#bk-start').value,
      end_time:   $('#bk-end').value,
      people:     String(people),
      flight:     ($('#bk-flight').value || '').toUpperCase(),
      // NEW: include membership + lounge type for server-side consistency
      plan_slug:        (modalEl.dataset.planSlug || 'basic'),
      lounge_is_premium:String(selected.isPremium ? 1 : 0)
    };

    console.log('[booking] store payload:', payload, 'needsPaymentFlag=', needsPaymentFlag);

    const res = await postForm('?r=booking_store', payload);
    if (!res.ok) return;

    document.getElementById('done-title').textContent = res.booking.title;
    document.getElementById('done-datetime').innerHTML = `${res.booking.date}<br>${res.booking.start} – ${res.booking.end}`;
    const amtEl = document.getElementById('done-amount');
    amtEl && (amtEl.textContent = (res.booking.total > 0) ? `$${Number(res.booking.total).toFixed(0)} Paid` : '$0.00');
    showStage(3);
  });

  // payment inputs validation (only enforced if needed)
  ['pay-name','pay-number','pay-exp','pay-cvv','pay-addr'].forEach(id=>{
    document.getElementById(id)?.addEventListener('input', validatePayment);
  });
})();
