<!-- Membership Management -->
<div class="container py-4">

  <!-- Page title -->
  <div class="mb-3">
    <h2 class="fw-bold mb-1">Membership Management</h2>
    <div class="text-muted">Choose the perfect membership tier for your travel needs</div>
  </div>

  <!-- Current membership summary -->
  <div class="card member-current mb-4">
    <div class="card-body">

      <!-- Row 1: label + Active on far ends -->
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
        <div class="label-small">Your Current Membership</div>
        <span class="badge badge-active">Active</span>
      </div>

      <!-- Row 2: plan + Free on same line -->
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-semibold">Basic (Free) Member</div>
        <div class="fw-semibold">Free</div>
      </div>

      <!-- Row 3: member id + renewal note aligned ends -->
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted small">
        <div>Member ID: <span class="text-muted">BS123456</span></div>
        <div>No renewal required</div>
      </div>

      <!-- three white tiles -->
      <div class="row g-3 mt-3">
        <div class="col-12 col-md-4">
          <div class="member-tile vcenter">
            <div class="tile-ico">
              <img src="assets/img/lounge-icon.svg" class="inline-icon tint-blue" alt="">
            </div>
            <div class="tile-lines">
              <div class="small text-muted">Lounge Access</div>
              <div class="fw-semibold">Premium locations</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="member-tile vcenter">
            <div class="tile-ico">
              <img src="assets/img/guest-icon.svg" class="inline-icon tint-green" alt="">
            </div>
            <div class="tile-lines">
              <div class="small text-muted">Guest Allowance</div>
              <div class="fw-semibold">0 per visit</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="member-tile vcenter">
            <div class="tile-ico">
              <img src="assets/img/star.svg" class="inline-icon tint-purple" alt="">
            </div>
            <div class="tile-lines">
              <div class="small text-muted">Benefits</div>
              <div class="fw-semibold">3 included</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tiers -->
  <div class="mb-2 fw-semibold">Available Membership Tiers</div>
  <div class="row g-3 mb-4">
    <!-- Basic (current) -->
    <div class="col-12 col-md-6 col-xl-3">
      <div class="card tier-card current h-100 position-relative">
        <span class="pill-current pill-centered">Current Plan</span>
        <div class="card-body d-flex flex-column align-items-stretch">

          <!-- icon, title, price, per month — stacked & centered -->
          <div class="tier-top text-center">
            <span class="ico-circle mb-2"><i class="fa-regular fa-id-badge"></i></span>
            <div class="fw-semibold">Basic (Free)</div>
            <div class="h3 fw-bold my-1">Free</div>
            <div class="text-muted small mb-3">&nbsp;</div>
          </div>

          <ul class="member-list small">
            <li>Free membership signup</li>
            <li>Buy single-visit pass for all lounges, including premium lounges</li>
            <li>Wi-Fi access</li>
          </ul>

          <!-- line before guest allowance -->
          <hr class="tier-sep">

          <div class="small">
            <div class="d-flex justify-content-between"><span class="text-muted">Guest Allowance:</span><span class="fw-semibold">0 per visit</span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Lounge Access:</span><span class="fw-semibold">Global</span></div>
          </div>

          <div class="mt-auto pt-3">
            <button class="btn btn-disabled w-100" disabled>Current Plan</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Silver -->
    <div class="col-12 col-md-6 col-xl-3">
      <div class="card tier-card h-100">
        <div class="card-body d-flex flex-column align-items-stretch">
          <div class="tier-top text-center">
            <span class="ico-circle mb-2"><i class="fa-solid fa-bolt"></i></span>
            <div class="fw-semibold">Silver</div>
            <div class="h3 fw-bold my-1">$299</div>
            <div class="text-muted small mb-3">per month</div>
          </div>

          <ul class="member-list small">
            <li>Free access to normal lounges</li>
            <li>Pay-per-use for premium lounges</li>
            <li>Wi-Fi &amp; printing</li>
            <li>Light refreshments</li>
          </ul>

          <hr class="tier-sep">

          <div class="small">
            <div class="d-flex justify-content-between"><span class="text-muted">Guest Allowance:</span><span class="fw-semibold">1 per visit</span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Lounge Access:</span><span class="fw-semibold">Global</span></div>
          </div>

          <div class="mt-auto pt-3">
            <button class="btn btn-fda btn-fda-primary w-100 btn-upgrade-tier"
                    data-plan="Silver" data-price="299"
                    data-benefits='["Free access to normal lounges","Pay-per-use for premium lounges","Wi-Fi & printing","Light refreshments"]'>
              Upgrade to Silver
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Gold -->
    <div class="col-12 col-md-6 col-xl-3">
      <div class="card tier-card h-100">
        <div class="card-body d-flex flex-column align-items-stretch">
          <div class="tier-top text-center">
            <span class="ico-circle halo-gold mb-2"><i class="fa-regular fa-star"></i></span>
            <div class="fw-semibold">Gold</div>
            <div class="h3 fw-bold my-1">$499</div>
            <div class="text-muted small mb-3">per month</div>
          </div>

          <ul class="member-list small">
            <li>Free access to all lounges, including premium lounges</li>
            <li>Unlimited time</li>
            <li>Premium amenities</li>
            <li>Full dining</li>
          </ul>

          <hr class="tier-sep">

          <div class="small">
            <div class="d-flex justify-content-between"><span class="text-muted">Guest Allowance:</span><span class="fw-semibold">2 per visit</span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Lounge Access:</span><span class="fw-semibold">Global</span></div>
          </div>

          <div class="mt-auto pt-3">
            <button class="btn btn-fda btn-fda-primary w-100 btn-upgrade-tier"
                    data-plan="Gold" data-price="499"
                    data-benefits='["Free access to all lounges, including premium lounges","Unlimited time","Premium amenities","Full dining"]'>
              Upgrade to Gold
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Platinum -->
    <div class="col-12 col-md-6 col-xl-3">
      <div class="card tier-card h-100">
        <div class="card-body d-flex flex-column align-items-stretch">
          <div class="tier-top text-center">
            <span class="ico-circle mb-2"><i class="fa-solid fa-crown"></i></span>
            <div class="fw-semibold">Platinum</div>
            <div class="h3 fw-bold my-1">$699</div>
            <div class="text-muted small mb-3">per month</div>
          </div>

          <ul class="member-list small">
            <li>Free access to all lounges, including premium lounges</li>
            <li>Unlimited time</li>
            <li>Concierge service</li>
            <li>Private meeting rooms</li>
          </ul>

          <hr class="tier-sep">

          <div class="small">
            <div class="d-flex justify-content-between"><span class="text-muted">Guest Allowance:</span><span class="fw-semibold">3 per visit</span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Lounge Access:</span><span class="fw-semibold">Global</span></div>
          </div>

          <div class="mt-auto pt-3">
            <button class="btn btn-fda btn-fda-primary w-100 btn-upgrade-tier"
                  data-plan="Platinum" data-price="699"
                  data-benefits='["Free access to all lounges, including premium lounges","Unlimited time","Concierge service","Private meeting rooms"]'>
            Upgrade to Platinum
          </button
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Feature comparison (unchanged) -->
  <div class="card panel-card">
    <div class="card-body">
      <div class="fw-semibold mb-3">Feature Comparison</div>
      <div class="table-responsive">
        <table class="table table-borderless align-middle member-table">
          <thead>
            <tr class="text-muted">
              <th style="width:24%">Feature</th>
              <th>Basic (Free)</th>
              <th>Silver</th>
              <th>Gold</th>
              <th>Platinum</th>
            </tr>
          </thead>
          <tbody>
            <tr><td class="text-muted">Monthly Fee</td><td>Free</td><td>$299</td><td>$499</td><td>$699</td></tr>
            <tr><td class="text-muted">Guest Allowance</td><td>0</td><td>1</td><td>2</td><td>3</td></tr>
            <tr><td class="text-muted">Normal Lounges</td><td><a href="#" class="link-blue">Pay-per-use</a></td><td>Free</td><td>Free</td><td>Free</td></tr>
            <tr><td class="text-muted">Premium Lounges</td><td><a href="#" class="link-blue">Pay-per-use</a></td><td><a href="#" class="link-blue">Pay-per-use</a></td><td>Free</td><td>Free</td></tr>
            <tr><td class="text-muted">Concierge Service</td><td>—</td><td>—</td><td>—</td><td>Included</td></tr>
            <tr><td class="text-muted">Private Meeting Rooms</td><td>—</td><td>—</td><td>—</td><td>Included</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php require __DIR__ . '/../partials/membership_upgrade_modal.php'; ?>