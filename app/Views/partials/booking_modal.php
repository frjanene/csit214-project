<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog booking-modal-dialog auth-modal-dialog modal-dialog-centered">
    <div class="modal-content booking-modal auth-modal">

      <div class="modal-header border-0 pb-0 align-items-start">
        <button type="button" class="btn btn-link p-0 booking-back d-none me-2" aria-label="Back">
          <i class="fa-solid fa-arrow-left me-2"></i>
        </button>

        <div class="flex-grow-1">
          <h5 class="modal-title" id="bookingModalLabel">Book Lounge Access</h5>
          <div class="text-muted small" id="bookingModalSub">
            Select your preferred time and complete your lounge booking
          </div>
        </div>

        <div class="secure-flag d-none" id="bk-secure-flag">
          <i class="fa-solid fa-lock"></i> Secure
        </div>

        <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pt-3">

        <!-- Stage 1 -->
        <div class="booking-stage" data-stage="1">
          <div class="booking-lounge-head d-flex gap-3 p-3 rounded-3 border mb-3">
            <img id="bk-thumb" class="rounded-3 flex-shrink-0" src="" width="72" height="72" style="object-fit:cover" alt="">
            <div class="flex-grow-1">
              <div class="fw-semibold" id="bk-title">—</div>
              <div class="text-muted small" id="bk-airport">—</div>

              <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                <span class="chip chip-soft">
                  <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted me-1" alt="">
                  <span id="bk-occ">—</span>
                </span>
                <span class="chip chip-soft">
                  <img src="assets/img/time-icon.svg" class="inline-icon tint-muted me-1" alt="">
                  <span id="bk-hours">—</span>
                </span>
              </div>

              <span class="premium-chip-modal mt-2">⭐ Premium Lounge</span>
              <div class="text-muted small mt-2">Basic member · pay-per-use for all lounges</div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-12 col-sm-6">
              <label class="small fw-semibold mb-1">Flight Number</label>
              <div class="input-group input-icon-left">
                <span class="input-group-text"><i class="fa-solid fa-plane-departure"></i></span>
                <input type="text" class="form-control" id="bk-flight" placeholder="e.g., FD123">
              </div>
              <div class="form-text mt-1" id="bk-flight-airport">
                Departing from Singapore Changi Airport (SIN)
              </div>
            </div>

            <div class="col-12 col-sm-6">
              <label class="small fw-semibold mb-1">Date</label>
              <div class="input-group input-icon-left">
                <span class="input-group-text"><i class="fa-solid fa-calendar-day"></i></span>
                <input type="date" class="form-control" id="bk-date">
              </div>
            </div>
          </div>

          <div class="flight-info card mt-3 d-none" id="bk-flight-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <div class="fw-semibold" id="bk-flight-title">FlyDreamAir FD123</div>
                  <div class="text-muted small">Boeing 777-300ER</div>
                </div>
                <span class="badge bg-success-subtle text-success fw-semibold">On Time</span>
              </div>

              <div class="row g-3 small">
                <div class="col-12 col-sm-6">
                  <div class="text-muted">Departure</div>
                  <div>SIN – Terminal 1</div>
                  <div id="bk-dep-dt">12/20/2026 at 14:30</div>
                  <div>Gate: A12</div>
                </div>
                <div class="col-12 col-sm-6">
                  <div class="text-muted">Arrival</div>
                  <div>LAX – Terminal 2</div>
                  <div id="bk-arr-dt">12/20/2026 at 23:45</div>
                </div>
              </div>

              <hr class="flight-found-sep my-3">
              <div class="note-info small">
                ✅ Flight found! Flight Departure Time and recommended lounge arrival time have been automatically set.
              </div>
            </div>
          </div>

          <div class="mt-3 d-none" id="bk-after-flight">
            <div class="mb-3">
              <label class="small fw-semibold mb-1">Flight Departure Time</label>
              <input type="time" class="form-control" id="bk-dep-time" disabled>
              <div class="form-text">Auto-filled from flight</div>
            </div>

            <div class="mb-3">
              <label class="small fw-semibold mb-1">Additional Guests</label>
              <select class="form-select" id="bk-guests">
                <option value="0" selected>0 guests</option>
                <option value="1">1 guest</option>
                <option value="2">2 guests</option>
                <option value="3">3 guests</option>
              </select>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="small fw-semibold mb-1">Start Time</label>
                <div class="d-flex align-items-center gap-2">
                  <select class="form-select" id="bk-start"></select>
                </div>
              </div>
              <div class="col-6">
                <label class="small fw-semibold mb-1">End Time</label>
                <div class="d-flex align-items-center gap-2">
                  <select class="form-select" id="bk-end"></select>
                </div>
              </div>
            </div>

            <div class="occ-guide box-muted p-3 rounded-3 mb-3">
              <div class="small fw-semibold mb-2">
                <i class="fa-regular fa-circle-question me-1"></i> Lounge Occupancy Guide
              </div>
              <div class="d-flex flex-wrap gap-3 small">
                <div class="d-flex align-items-center gap-2"><span class="legend-dot low"></span><span>Low &lt;50%</span></div>
                <div class="d-flex align-items-center gap-2"><span class="legend-dot mid"></span><span>Medium 50–80%</span></div>
                <div class="d-flex align-items-center gap-2"><span class="legend-dot high"></span><span>Busy 80–100%</span></div>
                <div class="d-flex align-items-center gap-2"><span class="legend-dot full"></span><span>Full 100%</span></div>
                <div class="d-flex align-items-center gap-2"><span class="legend-dot unavail"></span><span>Unavailable</span></div>
              </div>
            </div>

            <div class="selected-slot card slot-card mb-3">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div class="small">
                  <div class="fw-semibold mb-1">Selected Time Slot</div>
                  <div class="text-muted" id="bk-slot-text">—</div>
                </div>
                <span class="slot-occ-pill" id="bk-slot-occ">—</span>
              </div>
            </div>

            <div class="card method-card mb-3">
              <div class="card-body d-flex justify-content-between align-items-center">
                <label class="d-flex align-items-center gap-2 mb-0">
                  <input class="form-check-input" type="radio" name="bk-method" value="pay" checked>
                  <span class="fw-semibold">Pay Per Use</span>
                </label>
                <span class="price-chip" id="bk-price-chip">$—</span>
              </div>
              <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                <div class="text-muted small">Basic member · pay-per-use for all lounges</div>
              </div>
            </div>

            <div class="booking-summary card summary-card d-none" id="bk-summary">
              <div class="card-body small">
                <div class="d-flex justify-content-between mb-2"><span class="fw-semibold">Date:</span><span id="sum-date">—</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="fw-semibold">Time:</span><span id="sum-time">—</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="fw-semibold">Flight:</span><span id="sum-flight">—</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="fw-semibold">People:</span><span id="sum-people">—</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="fw-semibold">Expected Occupancy:</span><span id="sum-occ">—</span></div>
                <hr class="glass-hr my-2">
                <div class="d-flex justify-content-between fw-semibold"><span>Total Cost:</span><span id="sum-total">$—</span></div>
              </div>
            </div>
          </div>
        </div>
        <!-- /Stage 1 -->

        <!-- Stage 2 -->
        <div class="booking-stage d-none" data-stage="2">
          <div class="sum-panel rounded-4 mb-3">
            <div class="sum-header d-flex align-items-center gap-2">
              <i class="fa-solid fa-location-dot"></i>
              <span class="fw-semibold">Booking Summary</span>
            </div>

            <div class="sum-body">
              <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                <div>
                  <div class="fw-normal fs-6" id="sum2-title">—</div>
                  <div class="text-muted small" id="sum2-airport">—</div>
                  <span class="premium-chip-modal mt-2 d-inline-block">⭐ Premium Lounge</span>
                </div>
                <div class="text-end">
                  <div class="sum-amount" id="sum2-amount">—</div>
                  <div class="text-muted small">Membership Access</div>
                </div>
              </div>

              <hr class="sum-sep">

              <div class="row g-3">
                <div class="col-12 col-sm-6">
                  <div class="sum-kv">
                    <div class="icon-circle"><i class="fa-regular fa-calendar"></i></div>
                    <div>
                      <div class="sum-k">Date</div>
                      <div class="sum-v sum-blue" id="sum2-date">—</div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-6">
                  <div class="sum-kv">
                    <div class="icon-circle"><i class="fa-regular fa-clock"></i></div>
                    <div>
                      <div class="sum-k">Time</div>
                      <div class="sum-v sum-blue" id="sum2-time">—</div>
                      <div class="sum-sub" id="sum2-duration">—</div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-6">
                  <div class="sum-kv">
                    <div class="icon-circle"><i class="fa-regular fa-user"></i></div>
                    <div>
                      <div class="sum-k">Guests</div>
                      <div class="sum-v sum-blue" id="sum2-people">—</div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-6">
                  <div class="sum-kv">
                    <div class="icon-circle"><i class="fa-solid fa-plane-departure"></i></div>
                    <div>
                      <div class="sum-k">Flight</div>
                      <div class="sum-v sum-blue" id="sum2-flight">—</div>
                      <div class="sum-sub" id="sum2-flight-sub">—</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="pay-card rounded-4 mb-3">
            <div class="pay-title">
              <i class="fa-regular fa-credit-card me-2"></i> Payment Information
            </div>

            <div class="row g-3">
              <div class="col-12">
                <label class="sum-k mb-1"><i class="fa-regular fa-id-badge me-2"></i>Cardholder Name</label>
                <input type="text" class="form-control pay-input" id="pay-name" placeholder="John Smith">
              </div>
              <div class="col-12">
                <label class="sum-k mb-1"><i class="fa-regular fa-credit-card me-2"></i>Card Number</label>
                <input type="text" class="form-control pay-input" id="pay-number" placeholder="1234 5678 9012 3456">
              </div>
              <div class="col-6">
                <label class="sum-k mb-1"><i class="fa-regular fa-calendar me-2"></i>Expiry Date</label>
                <input type="text" class="form-control pay-input" id="pay-exp" placeholder="MM/YY">
              </div>
              <div class="col-6">
                <label class="sum-k mb-1"><i class="fa-solid fa-shield-halved me-2"></i>CVV</label>
                <input type="text" class="form-control pay-input" id="pay-cvv" placeholder="123">
              </div>
              <div class="col-12">
                <label class="sum-k mb-1"><i class="fa-regular fa-map me-2"></i>Billing Address</label>
                <input type="text" class="form-control pay-input" id="pay-addr" placeholder="123 Main St, City, Country">
              </div>
            </div>
          </div>

          <div class="secure-note">
            <i class="fa-solid fa-lock me-2"></i>
            Your payment is secured with 256-bit SSL encryption
          </div>

          <button class="btn btn-pay w-100" id="btn-pay-stage2" disabled>
            <i class="fa-solid fa-lock me-2"></i>
            <span id="btn-pay-label">Pay $— - Complete Booking</span>
          </button>

          <div class="tos small text-muted mt-2">
            By completing this booking, you agree to our Terms of Service and Privacy Policy.
            Your payment will be processed immediately.
          </div>
        </div>
        <!-- /Stage 2 -->

        <!-- Stage 3 -->
        <div class="booking-stage d-none text-center" data-stage="3">
          <div class="icon-check-circle mx-auto mb-3">
            <img src="assets/img/check.svg" class="icon-check" alt="">
          </div>
          <h5 class="fw-bold mb-1">🎉 Booking Confirmed!</h5>
          <div class="text-muted mb-3">Your lounge access is all set.</div>

          <div class="p-3 rounded border text-start mb-3">
            <div class="fw-semibold" id="done-title">—</div>
            <div class="text-muted small" id="done-airport">—</div>
            <div class="text-muted small" id="done-datetime">—</div>
            <span class="badge bg-success-subtle text-success fw-semibold mt-2">Paid</span>
          </div>
        </div>
        <!-- /Stage 3 -->

      </div>

      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-fda btn-fda-ghost btn-fda-fit" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-fda btn-fda-primary btn-fda-fit" id="bk-primary" disabled>
          <i class="fa-regular fa-credit-card me-2"></i>
          <span id="bk-primary-label">Confirm Booking</span>
        </button>
      </div>

    </div>
  </div>
</div>
