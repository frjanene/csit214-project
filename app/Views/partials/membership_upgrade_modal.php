<div class="modal fade" id="upgradeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog membership-modal-dialog modal-dialog-centered">
    <div class="modal-content membership-modal">
      <div class="modal-header border-0 pb-0 align-items-start">
        <button type="button" class="btn btn-link p-0 ug-back d-none me-2" aria-label="Back">
          <i class="fa-solid fa-arrow-left me-2"></i>
        </button>
        <div class="flex-grow-1">
          <h5 class="modal-title text-center" id="ug-title">Confirm Membership Upgrade</h5>
          <div class="text-muted small text-center" id="ug-sub">Are you sure you want to upgrade your membership?</div>
        </div>
        <div class="secure-flag d-none" id="ug-secure-flag"><i class="fa-solid fa-lock"></i> Secure</div>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="post" action="<?= base_href('membership_upgrade') ?>" id="ug-form">
        <input type="hidden" name="plan" id="ug-plan-input">
        <div class="modal-body pt-2">
          <!-- Stage 1 -->
          <div class="upgrade-stage" data-stage="1">
            <div class="text-center">
              <div class="ug-chip mx-auto">Upgrading to <span id="ug-plan-chip">—</span></div>
              <div class="ug-price mt-3" id="ug-price">$—</div>
              <div class="ug-sub text-primary-emphasis">Monthly membership fee</div>
              <div class="ug-panel mt-3">
                <div class="ug-panel-title">What you'll get:</div>
                <ul class="ug-bullets" id="ug-benefits"></ul>
              </div>
              <div class="ug-note mt-3">
                <i class="fa-regular fa-credit-card me-2"></i>
                You'll be redirected to our secure payment page
              </div>
            </div>
          </div>

          <!-- Stage 2 -->
          <div class="upgrade-stage d-none" data-stage="2">
            <div class="sum-panel rounded-4 mt-1 mb-3">
              <div class="fw-semibold mb-2">Order Summary</div>
              <div class="d-flex justify-content-between align-items-start">
                <span class="ug-tag" id="ug-plan-tag">— Membership</span>
                <div class="text-end">
                  <div class="sum-amount" id="ug-price-num">—</div>
                  <div class="text-muted small">per month</div>
                </div>
              </div>
              <hr class="sum-sep">
              <div class="small" id="ug-benefits-list">
                <div class="d-flex align-items-start gap-2 mb-2"><i class="fa-regular fa-circle-check text-success"></i><span id="ug-b1">—</span></div>
                <div class="d-flex align-items-start gap-2 mb-2"><i class="fa-regular fa-circle-check text-success"></i><span id="ug-b2">—</span></div>
                <div class="d-flex align-items-start gap-2 mb-2"><i class="fa-regular fa-circle-check text-success"></i><span id="ug-b3">—</span></div>
                <div class="d-flex align-items-start gap-2"><i class="fa-regular fa-circle-check text-success"></i><span id="ug-b4">—</span></div>
              </div>
            </div>

            <div class="pay-card rounded-4 mb-3">
              <div class="pay-title"><i class="fa-regular fa-credit-card me-2"></i> Payment Information</div>
              <div class="row g-3">
                <div class="col-12">
                  <label class="sum-k mb-1"><i class="fa-regular fa-id-badge me-2"></i>Cardholder Name</label>
                  <input type="text" class="form-control pay-input" id="ug-name" name="card_name" placeholder="John Doe" required>
                </div>
                <div class="col-12">
                  <label class="sum-k mb-1"><i class="fa-regular fa-credit-card me-2"></i>Card Number</label>
                  <input type="text" class="form-control pay-input" id="ug-number" name="card_number" placeholder="1234 5678 9012 3456" required>
                </div>
                <div class="col-6">
                  <label class="sum-k mb-1"><i class="fa-regular fa-calendar me-2"></i>Expiry Date</label>
                  <input type="text" class="form-control pay-input" id="ug-exp" name="card_exp" placeholder="MM/YY" required>
                </div>
                <div class="col-6">
                  <label class="sum-k mb-1"><i class="fa-solid fa-shield-halved me-2"></i>CVV</label>
                  <input type="text" class="form-control pay-input" id="ug-cvv" name="card_cvv" placeholder="123" required>
                </div>
                <div class="col-12">
                  <label class="sum-k mb-1"><i class="fa-regular fa-map me-2"></i>Billing Address</label>
                  <input type="text" class="form-control pay-input" id="ug-addr" name="billing_addr" placeholder="123 Main St, City, Country" required>
                </div>
              </div>
            </div>

            <div class="secure-note"><i class="fa-solid fa-lock me-2"></i>Your payment is secured with 256-bit SSL encryption</div>
            <button type="submit" class="btn btn-pay w-100" id="ug-pay" disabled>
              <i class="fa-solid fa-lock me-2"></i><span id="ug-pay-label">Pay $— - Complete Upgrade</span>
            </button>
            <div class="tos small text-muted mt-2">
              By completing this purchase, you agree to our Terms of Service and Privacy Policy.
            </div>
          </div>
        </div>

        <div class="modal-footer border-0 pt-0" id="ug-footer">
          <button type="button" class="btn btn-ug-primary w-100" id="ug-next">
            <i class="fa-regular fa-credit-card me-2"></i>Yes, Upgrade Now
          </button>
          <button type="button" class="btn btn-ug-ghost w-100" data-bs-dismiss="modal">No, Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="fda-toast toast align-items-center text-bg-white border-0" id="membershipToast" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="toast-body d-flex align-items-start gap-3">
    <span class="toast-check"><i class="fa-solid fa-check"></i></span>
    <div>
      <div class="fw-semibold">Congratulations!</div>
      <div>You've successfully upgraded to <span id="toast-plan">—</span> membership!</div>
    </div>
  </div>
</div>
