<?php
// helper to color occupancy (icon + text) by percentage
$occClass = function(int $used, int $cap): string {
  $pct = $cap > 0 ? ($used / $cap) * 100 : 0;
  if ($pct < 50)  return 'occ-low';
  if ($pct <= 80) return 'occ-mid';
  return 'occ-high';
};
?>

<!-- Find Lounges -->
<div class="container py-4">

  <!-- Page title -->
  <div class="mb-2">
    <h2 class="fw-bold mb-1">Find Airport Lounges</h2>
    <div class="text-muted">Discover and book premium lounges worldwide</div>
  </div>

  <!-- Search & filters bar -->
  <div class="card mb-3 lounge-filter">
    <div class="card-body">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="flex-grow-1 position-relative">
          <i class="fa-solid fa-magnifying-glass text-muted position-absolute" style="left:12px; top:10px;"></i>
          <input type="text" class="form-control ps-5" placeholder="Search by airport, city, or lounge name…">
        </div>

        <!-- (1) Countries dropdown -->
        <div style="min-width: 220px;">
          <select class="form-select form-select-sm country-select">
            <option selected>All Countries</option>
            <option>Australia</option>
            <option>Canada</option>
            <option>France</option>
            <option>Germany</option>
            <option>Japan</option>
            <option>Singapore</option>
            <option>United Arab Emirates</option>
            <option>United Kingdom</option>
            <option>United States</option>
          </select>
        </div>
      </div>

      <!-- Amenities quick toggles -->
      <div class="row g-3 mt-3">
        <div class="col-12 small text-muted">Amenities</div>
        <div class="col-12 d-flex flex-wrap gap-4 small">
          <label class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox"><i class="fa-solid fa-wifi"></i> Wi-Fi
          </label>
          <label class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox"><i class="fa-solid fa-shower"></i> Showers
          </label>
          <label class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox"><i class="fa-solid fa-briefcase"></i> Business Center
          </label>
          <label class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox"><i class="fa-solid fa-utensils"></i> Premium Dining
          </label>
          <label class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox"><i class="fa-solid fa-bed"></i> Sleep Pods
          </label>
          <label class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox"><i class="fa-solid fa-champagne-glasses"></i> Champagne Bar
          </label>
        </div>
      </div>
    </div>
  </div>

  <!-- Results grid -->
  <div class="row g-3">

    <!-- Lounge card -->
    <div class="col-lg-6">
      <div class="card lounge-card h-100">
        <div class="lounge-media">
          <img src="assets/img/lounge-1.jpg" class="img-fluid" alt="">
          <!-- (3) Premium badge: star + 90% #FE9A00, no border -->
          <span class="badge premium-badge">⭐ Premium</span>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold">FlyDreamAir Premium Lounge</div>

              <!-- (2) Location icon tinted to #717182 -->
              <div class="text-muted small d-flex align-items-center gap-2 mt-1">
                <img src="assets/img/location-secondary.svg" class="inline-icon tint-slate" alt="">
                <span>Singapore Changi Airport (SIN) – Terminal 1</span>
              </div>

              <div class="text-muted small mt-1 d-flex align-items-center gap-2">
                <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                <span>05:00 – 23:00</span>
              </div>
            </div>

            <!-- (5) Occupancy color logic -->
            <?php $used=89; $cap=120; $cls=$occClass($used,$cap); ?>
            <div class="small d-flex align-items-center gap-1 occupancy <?= $cls ?>">
              <img src="assets/img/guest-icon.svg" class="inline-icon occ-icon" alt="">
              <span class="occ-text"><?= $used ?>/<?= $cap ?></span>
            </div>
          </div>

          <!-- Feature chips -->
          <div class="d-flex flex-wrap gap-2 mt-3">
            <span class="chip"><i class="fa-solid fa-wifi"></i> Wi-Fi</span>
            <span class="chip"><i class="fa-solid fa-shower"></i> Showers</span>
            <span class="chip"><i class="fa-solid fa-utensils"></i> Premium Dining</span>
            <span class="chip"><i class="fa-solid fa-champagne-glasses"></i> Champagne Bar</span>
            <span class="chip chip-more">+3 more</span>
          </div>

          <!-- (4) Divider before price section -->
          <hr class="glass-hr my-3">

          <div class="d-flex justify-content-between align-items-center">
            <div class="small">
              <div class="fw-semibold">$55 per person</div>
              <div class="text-muted">Basic member · pay-per-use for all lounges</div>
            </div>
            <!-- <a href="#" class="btn btn-fda btn-fda-primary btn-fda-fit" style="height:32px; padding:0 14px;">Book Now</a> -->
             <a href="#"
                class="btn btn-fda btn-fda-primary btn-fda-fit"
                style="height:32px; padding:0 14px;"
                data-bs-toggle="modal"
                data-bs-target="#bookingModal"
                data-lounge-occ="110/120"
                data-lounge-title="FlyDreamAir Premium Lounge"
                data-lounge-airport="Singapore Changi Airport (SIN) – Terminal 1"
                data-lounge-city="Singapore, Singapore"
                data-lounge-hours="05:00 – 23:00"
                data-lounge-price="55"
                data-lounge-img="assets/img/lounge-1.jpg">
                Book Now
              </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Lounge card -->
    <div class="col-lg-6">
      <div class="card lounge-card h-100">
        <div class="lounge-media">
          <img src="assets/img/lounge-2.jpg" class="img-fluid" alt="">
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold">FlyDreamAir Sydney Lounge</div>
              <div class="text-muted small d-flex align-items-center gap-2 mt-1">
                <img src="assets/img/location-secondary.svg" class="inline-icon tint-slate" alt="">
                <span>Sydney Kingsford Smith Airport (SYD) – Terminal 1</span>
              </div>
              <div class="text-muted small mt-1 d-flex align-items-center gap-2">
                <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                <span>04:30 – 23:30</span>
              </div>
            </div>

            <?php $used=67; $cap=150; $cls=$occClass($used,$cap); ?>
            <div class="small d-flex align-items-center gap-1 occupancy <?= $cls ?>">
              <img src="assets/img/guest-icon.svg" class="inline-icon occ-icon" alt="">
              <span class="occ-text"><?= $used ?>/<?= $cap ?></span>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 mt-3">
            <span class="chip"><i class="fa-solid fa-wifi"></i> Wi-Fi</span>
            <span class="chip"><i class="fa-solid fa-martini-glass"></i> Bar</span>
            <span class="chip"><i class="fa-solid fa-briefcase"></i> Business Center</span>
            <span class="chip chip-more">+1 more</span>
          </div>

          <hr class="glass-hr my-3">

          <div class="d-flex justify-content-between align-items-center">
            <div class="small">
              <div class="fw-semibold">$55 per person</div>
              <div class="text-muted">Basic member · pay-per-use for all lounges</div>
            </div>
            <a href="#" class="btn btn-fda btn-fda-primary btn-fda-fit" style="height:32px; padding:0 14px;">Book Now</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Lounge card -->
    <div class="col-lg-6">
      <div class="card lounge-card h-100">
        <div class="lounge-media">
          <img src="assets/img/lounge-3.jpg" class="img-fluid" alt="">
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold">FlyDreamAir Melbourne Lounge</div>
              <div class="text-muted small d-flex align-items-center gap-2 mt-1">
                <img src="assets/img/location-secondary.svg" class="inline-icon tint-slate" alt="">
                <span>Melbourne Airport (MEL) – Terminal 2</span>
              </div>
              <div class="text-muted small mt-1 d-flex align-items-center gap-2">
                <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                <span>05:00 – 22:30</span>
              </div>
            </div>

            <?php $used=140; $cap=140; $cls=$occClass($used,$cap); ?>
            <div class="small d-flex align-items-center gap-1 occupancy <?= $cls ?>">
              <img src="assets/img/guest-icon.svg" class="inline-icon occ-icon" alt="">
              <span class="occ-text"><?= $used ?>/<?= $cap ?></span>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 mt-3">
            <span class="chip"><i class="fa-solid fa-wifi"></i> Wi-Fi</span>
            <span class="chip"><i class="fa-solid fa-mug-saucer"></i> Coffee Bar</span>
            <span class="chip"><i class="fa-solid fa-briefcase"></i> Business Center</span>
            <span class="chip chip-more">+2 more</span>
          </div>

          <hr class="glass-hr my-3">

          <div class="d-flex justify-content-between align-items-center">
            <div class="small">
              <div class="fw-semibold">$55 per person</div>
              <div class="text-muted">Basic member · pay-per-use for all lounges</div>
            </div>
            <a href="#" class="btn btn-fda btn-fda-primary btn-fda-fit" style="height:32px; padding:0 14px;">Book Now</a>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /row -->
</div>

<?php require __DIR__ . '/../partials/booking_modal.php'; ?>
