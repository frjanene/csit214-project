<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog booking-modal-dialog auth-modal-dialog modal-dialog-centered">
    <div class="modal-content booking-modal auth-modal">
      <!-- Header -->
      <div class="modal-header border-0 pb-1 align-items-start">
        <button type="button" class="btn btn-link p-0 booking-back d-none me-2" aria-label="Back">
          <i class="fa-solid fa-arrow-left me-2"></i>
        </button>

        <div class="flex-grow-1">
          <h5 class="modal-title fw-bold mb-0">Booking Details</h5>
          <div class="text-muted small" id="bd-res-id">Reservation #1757418176481</div>
        </div>

        <span class="status-pill status-ok" id="bd-status">confirmed</span>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pt-0">

        <!-- Success banner -->
        <div class="bd-banner success d-flex align-items-start gap-3 mb-3">
          <i class="fa-regular fa-circle-check mt-1"></i>
          <div>
            <div class="fw-semibold fs-6">Booking Confirmed</div>
            <div class="small text-success-2">Your lounge access is ready!</div>
          </div>
        </div>

        <!-- Lounge Information -->
        <div class="card bd-section mb-3">
          <div class="card-body">
            <div class="d-flex gap-3">
              <div class="bd-thumb rounded-3 flex-shrink-0"></div>
              <div class="flex-grow-1">
                <div class="fw-semibold" id="bd-title">FlyDreamAir Premium Lounge</div>
                <div class="text-muted small" id="bd-airport">SIN</div>
                <div class="text-muted small" id="bd-terminal">Terminal A - Level 2</div>
              </div>
            </div>

            <div class="row g-3 mt-3 small">
              <div class="col-12 col-md-6">
                <div class="fw-semibold mb-1">Contact</div>
                <div class="d-flex align-items-center gap-2 mb-1">
                  <i class="fa-solid fa-phone small text-muted"></i><span>+1 (555) 123-4567</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <i class="fa-regular fa-envelope small text-muted"></i><span>lounge@flydreamair.com</span>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="fw-semibold mb-1">Operating Hours</div>
                <div>5:00 AM - 11:00 PM</div>
                <div>Daily</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Your Reservation -->
        <div class="card bd-section mb-3">
          <div class="card-body">
            <div class="fw-normal mb-2">Your Reservation</div>

            <div class="row g-3 small">
              <div class="col-12 col-md-6">
                <div class="d-flex align-items-start gap-2 mb-2">
                  <i class="fa-regular fa-calendar mt-1 text-muted"></i>
                  <div>
                    <div class="text-muted">Date</div>
                    <div id="bd-date">Sunday, December 20, 2026</div>
                  </div>
                </div>
                <div class="d-flex align-items-start gap-2">
                  <i class="fa-regular fa-clock mt-1 text-muted"></i>
                  <div>
                    <div class="text-muted">Time</div>
                    <div id="bd-time">11:30 - 14:00</div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-6">
                <div class="d-flex align-items-start gap-2 mb-2">
                  <i class="fa-solid fa-user-group mt-1 text-muted"></i>
                  <div>
                    <div class="text-muted">Guests</div>
                    <div id="bd-people">1 people total</div>
                  </div>
                </div>
                <div class="d-flex align-items-start gap-2">
                  <i class="fa-solid fa-plane-departure mt-1 text-muted"></i>
                  <div>
                    <div class="text-muted">Flight</div>
                    <div id="bd-flight">FD123 at 14:30</div>
                  </div>
                </div>
              </div>
            </div>

            <hr class="glass-hr my-3">

            <div class="d-flex justify-content-between align-items-center small">
              <div class="d-flex align-items-start gap-2">
                <i class="fa-regular fa-credit-card mt-1 text-muted"></i>
                <div>
                  <div class="fw-semibold">Payment</div>
                  <div class="text-muted" id="bd-payment">Membership Access</div>
                </div>
              </div>
              <div class="text-end">
                <div class="text-muted">Total Cost</div>
                <div class="text-success fw-semibold" id="bd-total">FREE</div>
              </div>
            </div>
          </div>
        </div>

        <!-- QR code -->
        <div class="card bd-section mb-3 text-center">
          <div class="card-body">
            <div class="fw-semibold mb-2">Entry QR Code</div>
            <img src="assets/img/demo-qr.png" width="180" height="180" alt="QR" class="mb-2">
            <div class="fw-semibold">Scan at lounge entrance</div>
            <div class="text-muted small">Show this QR code to lounge staff for quick entry</div>
          </div>
        </div>

        <!-- Important info -->
        <div class="bd-info card mb-3">
          <div class="card-body">
            <div class="fw-normal mb-2">Important Information</div>
            <ul class="small mb-0">
              <li>Please arrive at least 15 minutes before your scheduled time</li>
              <li>Valid boarding pass and ID required for entry</li>
              <li>Children under 2 years old are complimentary</li>
              <li>Dress code: Smart casual attire required</li>
              <li>Cancellation allowed up to 24 hours before visit</li>
            </ul>
          </div>
        </div>

      </div>

      <!-- Footer -->
      <div class="modal-footer border-0 pt-0 booking-details-footer">
        <button class="btn btn-bd-cancel" id="bd-cancel">Cancel Booking</button>
        <button class="btn btn-bd-close" data-bs-dismiss="modal">Close</button>
      </div>


    </div>
  </div>
</div>
