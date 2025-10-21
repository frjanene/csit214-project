(() => {
  const modalEl = document.getElementById('bookingModal');
  if (!modalEl) return;

  const $ = sel => modalEl.querySelector(sel);

  const backBtn      = $('.booking-back');
  const primaryBtn   = $('#bk-primary');
  const primaryLabel = $('#bk-primary-label');

  let activeStage = 1;
  let needsPaymentFlag = true; // kept in sync with server quote
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
  const debounce = (fn, wait=350) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),wait); }; };

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
    modalEl.querySelector('.method-card')?.classList.toggle('d-none', !show);
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

    const total = +(modalEl.dataset.quotedTotal || 0);

    if (covered || total === 0){
      payLbl.textContent = 'Confirm Booking';
      payBtn.disabled = false;
      payBtn.classList.remove('d-none');
    } else {
      payLbl.textContent = `Pay $${Number(total).toFixed(0)} - Complete Booking`;
      payBtn.disabled = true; // enabled by validatePayment()
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

  // ---------- END-TIME FILTER (hide < start) ----------
  function filterEndOptions(){
    const st = $('#bk-start').value;
    const endSel = $('#bk-end');
    if (!st || !endSel) return;

    const minStart = hhmmToMinutes(st);
    let firstValid = null;

    [...endSel.options].forEach(opt => {
      const valid = hhmmToMinutes(opt.value) >= minStart;
      opt.hidden = !valid;         // don't display
      opt.disabled = !valid;       // also make it unselectable
      if (valid && firstValid === null) firstValid = opt.value;
    });

    // If current end is now invalid, snap to the first valid end
    if (!endSel.value || hhmmToMinutes(endSel.value) < minStart) {
      endSel.value = firstValid || '';
    }
  }

  // ---------- time options + summary (DB-driven) ----------
  function populateTimesFromArray(arr){
    const startSel = $('#bk-start');
    const endSel   = $('#bk-end');
    startSel.innerHTML = '';
    endSel.innerHTML   = '';

    arr.forEach(s => {
      // expecting: {start:'HH:MM', end:'HH:MM', used:int, cap:int}
      const occText = `${s.used}/${s.cap}`;
      const add = (sel, timeValue, occ) => {
        const o = document.createElement('option');
        o.value = timeValue;
        o.textContent = `${timeValue} — ${occ}`;
        if (s.used >= s.cap) o.disabled = true; // mark full slots unselectable
        sel.appendChild(o);
      };
      add(startSel, s.start, occText);
      add(endSel,   s.end,   occText);
    });

    if (startSel.options.length) startSel.selectedIndex = 0;
    if (endSel.options.length)   endSel.selectedIndex   = Math.min(1, endSel.options.length-1);

    // NEW: filter end options based on the initial start selection
    filterEndOptions();

    updateSelectedSlot();
  }

  async function loadSlotsAndPopulate() {
    const date = $('#bk-date').value;
    if (!selected.loungeId || !date) return;

    const res = await postForm('?r=slots', {
      lounge_id: String(selected.loungeId),
      date
    });

    if (!res || !res.ok) {
      // clear selects on failure
      $('#bk-start').innerHTML = '';
      $('#bk-end').innerHTML   = '';
      updateSelectedSlot();
      return;
    }

    populateTimesFromArray(res.slots || []);
  }

  const selectedOccText = (sel) => {
    const txt = sel.selectedOptions[0]?.textContent || '';
    const parts = txt.split('—');
    return parts[1]?.trim() || '';
  };

  function updateSelectedSlot(){
    const date = $('#bk-date').value;
    const st   = $('#bk-start').value;
    let   et   = $('#bk-end').value; // ← make mutable

    // Enforce: end time can’t be earlier than start time
    if (st && et && hhmmToMinutes(et) < hhmmToMinutes(st)) {
      const endSel = $('#bk-end');
      let fixed = false;
      for (const opt of endSel.options) {
        if (opt.disabled) continue;
        if (hhmmToMinutes(opt.value) >= hhmmToMinutes(st)) {
          endSel.value = opt.value;
          et = opt.value;
          fixed = true;
          break;
        }
      }
      // If no valid option ≥ start exists, snap end to start
      if (!fixed) {
        endSel.value = st;
        et = st;
      }
    }

    $('#bk-slot-text').textContent = (date && st && et) ? `${st} – ${et} on ${date}` : '—';
    $('#bk-slot-occ').textContent  = selectedOccText($('#bk-end')) || selectedOccText($('#bk-start')) || '—';

    const guests = +$('#bk-guests').value || 0;
    const people = 1 + guests;
    const flight = ($('#bk-flight').value || '').toUpperCase();
    const depT   = $('#bk-dep-time').value ? ` (${ $('#bk-dep-time').value })` : '';
    const ready  = !!(date && st && et && flight);

    $('#bk-summary').classList.toggle('d-none', !ready);

    $('#sum-date').textContent   = date || '—';
    $('#sum-time').textContent   = (st && et) ? `${st} – ${et}` : '—';
    $('#sum-flight').textContent = flight ? `${flight}${depT}` : '—';
    $('#sum-people').textContent = `${people} ${people>1?'people':'person'}`;
    $('#sum-occ').textContent    = $('#bk-slot-occ').textContent;

    // If fields are ready, fetch a fresh server quote and update totals/chips
    if (isQuoteReady()) debouncedQuote();
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
    const bigPay = document.getElementById('btn-pay-stage2');
    if (!needsPaymentFlag || +(modalEl.dataset.quotedTotal || 0) === 0) {
      if (bigPay) bigPay.disabled = false;
      return;
    }
    const ok = $('#pay-name')?.value.trim()
           && $('#pay-number')?.value.replace(/\s+/g,'').length >= 12
           && /^\d{2}\/\d{2}$/.test($('#pay-exp')?.value.trim() || '')
           && /^\d{3,4}$/.test($('#pay-cvv')?.value.trim() || '')
           && $('#pay-addr')?.value.trim();
    if (bigPay) bigPay.disabled = !ok;
  }

  // ---------- QUOTING ----------
  const isQuoteReady = () => {
    // Only quote when we have the fields server needs
    return !!(selected.loungeId
      && $('#bk-date').value
      && $('#bk-start').value
      && $('#bk-end').value);
  };

  async function requestQuoteAndUpdateUI() {
    if (!isQuoteReady()) return;

    const people = 1 + (+$('#bk-guests').value || 0);
    const payload = {
      lounge_id:  String(selected.loungeId || ''),
      visit_date: $('#bk-date').value,
      start_time: $('#bk-start').value,
      end_time:   $('#bk-end').value,
      people:     String(people),
      plan_slug:        (modalEl.dataset.planSlug || 'basic'),
      lounge_is_premium:String(selected.isPremium ? 1 : 0)
    };

    const q = await postForm('?r=booking_quote', payload);
    if (!q.ok) return;

    // Persist total and recompute flags/UI
    modalEl.dataset.quotedTotal = String(q.total || 0);
    needsPaymentFlag = (q.total > 0);

    // Stage 1 price chip and summary total use the **server total**
    const chip = $('#bk-price-chip');
    if (chip) chip.textContent = (q.total > 0) ? `$${Number(q.total).toFixed(0)}` : 'FREE';

    const sumTotal = $('#sum-total');
    if (sumTotal) sumTotal.textContent = (q.total > 0) ? `$${Number(q.total).toFixed(0)}` : '$0';

    // Plan note + payment card visibility reflect real coverage
    const plan = q.plan || planFromDataset();
    setPlanNote(plan, !needsPaymentFlag);
    toggleStage1PaymentCard(needsPaymentFlag);

    // Keep Stage 2 button + payment form in sync too (if user jumps immediately)
    toggleStage2PaymentForm(needsPaymentFlag);
    setupStage2Button(!needsPaymentFlag);
    validatePayment();
  }

  const debouncedQuote = debounce(requestQuoteAndUpdateUI, 250);

  // ---------- stage 2 hydration ----------
  function hydrateStage2(){
    const totalQuoted = +(modalEl.dataset.quotedTotal || 0);
    const covered = (totalQuoted === 0);

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

    const amtEl = $('#sum2-amount');
    if (amtEl) {
      if (covered) {
        amtEl.textContent = 'FREE';
        amtEl.classList.add('price-chip'); // treat as a chip when free
      } else {
        amtEl.textContent = `$${Number(totalQuoted).toFixed(0)}`;
        amtEl.classList.remove('price-chip');
      }
    }

    $('#sum2-date').textContent     = date;
    $('#sum2-time').textContent     = time;
    $('#sum2-duration').textContent = durLabel || '';
    $('#sum2-people').textContent   = guests.replace(' people','').replace(' person','');
    $('#sum2-flight').textContent   = flight;
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
    modalEl.dataset.quotedTotal = '0';
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

    // membership pre-check (UI hint only; real price comes from quote)
    const plan = planFromDataset();
    const coveredByPlan = loungeCoveredByPlan(plan, !!selected.isPremium);
    needsPaymentFlag = !coveredByPlan;

    // Stage-1 initial chip (will be replaced by server total once we quote)
    $('#bk-price-chip').textContent = coveredByPlan ? 'FREE' : `$${unit}`;

    setPlanNote(plan, coveredByPlan);
    toggleStage1PaymentCard(!coveredByPlan);

    // Stage 2 defaults
    toggleStage2PaymentForm(!coveredByPlan);
    setupStage2Button(coveredByPlan);

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

    // Load real slots + occupancy for this lounge & date
    await loadSlotsAndPopulate();

    // Once times are populated, try the first quote immediately if other fields ready
    if (isQuoteReady()) debouncedQuote();
    updateSelectedSlot();
  };

  const flightInput = document.getElementById('bk-flight');
  flightInput.addEventListener('input', debounce(doFlightLookup, 400));
  flightInput.addEventListener('change', doFlightLookup);
  flightInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); doFlightLookup(); }
  });

  $('#bk-date').addEventListener('change', async () => {
    // If a flight has been entered, re-run flight lookup (it may change the date)
    if (($('#bk-flight').value || '').trim()) {
      await doFlightLookup();
    } else {
      // Otherwise just load slots for the picked date
      await loadSlotsAndPopulate();
      if (isQuoteReady()) debouncedQuote();
      updateSelectedSlot();
    }
  });

  // ---------- reactive updates ----------
  // Start: filter end options first, then update + quote
  $('#bk-start').addEventListener('change', () => {
    filterEndOptions();           // hide earlier-than-start end times
    updateSelectedSlot();
    if (isQuoteReady()) debouncedQuote();
  });

  ['bk-end','bk-guests'].forEach(id=>{
    $('#'+id).addEventListener('change', () => {
      updateSelectedSlot();
      if (isQuoteReady()) debouncedQuote();
    });
  });

  // ---------- Stage 1 primary -> ensure we have the freshest quote, then Stage 2 ----------
  primaryBtn.addEventListener('click', async () => {
    if (activeStage !== 1) return;

    // If we don't have a quote yet, fetch one now to avoid stale UI
    if (!modalEl.dataset.quotedTotal) await requestQuoteAndUpdateUI();

    // Always show Stage 2 for review.
    hydrateStage2();
    showStage(2);
    validatePayment();
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
      plan_slug:        (modalEl.dataset.planSlug || 'basic'),
      lounge_is_premium:String(selected.isPremium ? 1 : 0)
    };

    const res = await postForm('?r=booking_store', payload);
    if (!res.ok) return;

    document.getElementById('done-title').textContent = res.booking.title;
    document.getElementById('done-datetime').innerHTML = `${res.booking.date}<br>${res.booking.start} – ${res.booking.end}`;
    const amtEl = document.getElementById('done-amount');
    amtEl && (amtEl.textContent = (res.booking.total > 0) ? `$${Number(res.booking.total).toFixed(0)} Paid` : '$0.00');

    // QR image & actions — always show the image asset (ignore server for src)
    const qrImgEl = modalEl.querySelector('.qr-img');
    if (qrImgEl) {
      qrImgEl.src = 'assets/img/demo-qr.png';
      qrImgEl.alt = 'Entry QR Code';
      // optional: still allow clicking to open server deeplink if available
      if (res.booking.qr_url) {
        qrImgEl.style.cursor = 'pointer';
        qrImgEl.onclick = () => window.open(res.booking.qr_url, '_blank');
      } else {
        qrImgEl.onclick = null;
        qrImgEl.style.cursor = 'default';
      }
    }

    const dlBtn = document.getElementById('btn-download-pass');
    if (dlBtn) {
      dlBtn.onclick = () => {
        // keep download using local image (since we’re always displaying the asset)
        const href = 'assets/img/demo-qr.png';
        const a = document.createElement('a');
        a.href = href;
        a.download = `FlyDreamAir-QR-${res.booking.id}.png`;
        document.body.appendChild(a);
        a.click();
        a.remove();
      };
    }

    const shBtn = document.getElementById('btn-share-pass');
    if (shBtn) {
      shBtn.onclick = async () => {
        const shareUrl = res.booking.qr_url || location.href;
        const shareText = `Your lounge pass for ${res.booking.title} on ${res.booking.date} (${res.booking.start}–${res.booking.end}).`;
        if (navigator.share) {
          try { await navigator.share({ title: 'FlyDreamAir Lounge Pass', text: shareText, url: shareUrl }); } catch(_) {}
        } else {
          window.open(shareUrl, '_blank');
        }
      };
    }

    showStage(3);
  });

  // payment inputs validation (only enforced if needed)
  ['pay-name','pay-number','pay-exp','pay-cvv','pay-addr'].forEach(id=>{
    document.getElementById(id)?.addEventListener('input', validatePayment);
  });
})();
