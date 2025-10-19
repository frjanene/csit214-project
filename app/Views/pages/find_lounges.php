<?php
// helper to color occupancy (icon + text) by percentage
$occClass = function(int $used, int $cap): string {
  $pct = $cap > 0 ? ($used / $cap) * 100 : 0;
  if ($pct < 50)  return 'occ-low';
  if ($pct <= 80) return 'occ-mid';
  return 'occ-high';
};

$lounges   = $lounges   ?? [];
$amenities = $amenities ?? [];
$countries = $countries ?? [];
$filters   = $filters   ?? ['q'=>'','country'=>'','amen'=>[]];
$q         = $filters['q'] ?? '';
$country   = $filters['country'] ?? '';
$amenSel   = $filters['amen'] ?? [];
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
      <!-- NOTE: action to root + hidden r=find to satisfy Router -->
      <form id="loungeFilter" method="get" action="<?= base_href() ?>">
        <input type="hidden" name="r" value="find">

        <div class="d-flex align-items-center gap-3 flex-wrap">
          <div class="flex-grow-1 position-relative">
            <i class="fa-solid fa-magnifying-glass text-muted position-absolute" style="left:12px; top:10px;"></i>
            <input
              type="text"
              name="q"
              class="form-control ps-5"
              placeholder="Search by airport, city, or lounge name…"
              value="<?= htmlspecialchars($q) ?>"
              data-autosubmit="debounce"
            >
          </div>

          <!-- Countries dropdown -->
          <div style="min-width: 220px;">
            <select class="form-select form-select-sm country-select" name="country" data-autosubmit="instant">
              <option <?= $country==='' || $country==='All Countries' ? 'selected' : '' ?>>All Countries</option>
              <?php foreach ($countries as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $country===$c ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Amenities checklist (icons removed) -->
        <div class="row g-3 mt-3">
          <div class="col-12 small text-muted">Amenities</div>
          <div class="col-12 d-flex flex-wrap gap-4 small">
            <?php foreach ($amenities as $a): ?>
              <?php $checked = in_array($a['code'], $amenSel, true) ? 'checked' : ''; ?>
              <label class="form-check d-flex align-items-center gap-2 mb-0">
                <input
                  class="form-check-input"
                  type="checkbox"
                  name="amen[]"
                  value="<?= htmlspecialchars($a['code']) ?>"
                  <?= $checked ?>
                  data-autosubmit="instant"
                >
                <span><?= htmlspecialchars($a['label']) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Results grid -->
  <div class="row g-3">
    <?php if (empty($lounges)): ?>
      <div class="col-12">
        <div class="alert alert-light border">No lounges match your filters.</div>
      </div>
    <?php else: ?>
      <?php foreach ($lounges as $L): ?>
        <div class="col-lg-6">
          <div class="card lounge-card h-100">
            <div class="lounge-media">
              <img src="<?= htmlspecialchars($L['image_url'] ?: 'assets/img/lounge-placeholder.jpg') ?>" class="img-fluid" alt="">
              <?php if ((int)$L['is_premium'] === 1): ?>
                <span class="badge premium-badge">⭐ Premium</span>
              <?php endif; ?>
            </div>

            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="fw-semibold"><?= htmlspecialchars($L['name']) ?></div>

                  <div class="text-muted small d-flex align-items-center gap-2 mt-1">
                    <img src="assets/img/location-secondary.svg" class="inline-icon tint-slate" alt="">
                    <span>
                      <?= htmlspecialchars($L['airport_name']) ?>
                      (<?= htmlspecialchars($L['iata']) ?>)
                      <?= $L['terminal'] ? ' – ' . htmlspecialchars($L['terminal']) : '' ?>
                    </span>
                  </div>

                  <div class="text-muted small mt-1 d-flex align-items-center gap-2">
                    <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                    <span><?= htmlspecialchars(substr($L['open_time'],0,5)) ?> – <?= htmlspecialchars(substr($L['close_time'],0,5)) ?></span>
                  </div>
                </div>

                <?php
                  // Occupancy placeholder (booking later). Show capacity as "0/capacity" for now.
                  $used = 0;
                  $cap  = (int)$L['capacity'];
                  $cls  = $occClass($used, $cap);
                ?>
                <div class="small d-flex align-items-center gap-1 occupancy <?= $cls ?>">
                  <img src="assets/img/guest-icon.svg" class="inline-icon occ-icon" alt="">
                  <span class="occ-text"><?= $used ?>/<?= $cap ?></span>
                </div>
              </div>

              <!-- Feature chips -->
              <div class="d-flex flex-wrap gap-2 mt-3">
                <?php
                  $maxChips = 4;
                  $chips = array_slice($L['amenities'] ?? [], 0, $maxChips);
                  foreach ($chips as $a) {
                    echo '<span class="chip">' . htmlspecialchars($a['label']) . '</span>';
                  }
                  $remaining = max(0, count($L['amenities']) - $maxChips);
                  if ($remaining > 0) echo '<span class="chip chip-more">+' . $remaining . ' more</span>';
                ?>
              </div>

              <hr class="glass-hr my-3">

              <div class="d-flex justify-content-between align-items-center">
                <div class="small">
                  <div class="fw-semibold">$<?= number_format((float)$L['price_usd'], 2) ?> per person</div>
                  <div class="text-muted">
                    Basic member · pay-per-use for <?= (int)$L['is_premium'] ? 'premium lounges' : 'all lounges' ?>
                  </div>
                </div>

                <!-- Booking later -->
                <a href="#"
                   class="btn btn-fda btn-fda-primary btn-fda-fit"
                   style="height:32px; padding:0 14px;"
                   data-bs-toggle="modal"
                   data-bs-target="#bookingModal"
                   data-lounge-occ="<?= $used ?>/<?= $cap ?>"
                   data-lounge-title="<?= htmlspecialchars($L['name']) ?>"
                   data-lounge-airport="<?= htmlspecialchars($L['airport_name']) ?> (<?= htmlspecialchars($L['iata']) ?>)<?= $L['terminal'] ? ' – '.htmlspecialchars($L['terminal']) : '' ?>"
                   data-lounge-city="<?= htmlspecialchars(($L['city'] ?? '').($L['country'] ? ', '.$L['country'] : '')) ?>"
                   data-lounge-hours="<?= htmlspecialchars(substr($L['open_time'],0,5) . ' – ' . substr($L['close_time'],0,5)) ?>"
                   data-lounge-price="<?= htmlspecialchars($L['price_usd']) ?>"
                   data-lounge-img="<?= htmlspecialchars($L['image_url'] ?: 'assets/img/lounge-placeholder.jpg') ?>">
                  Book Now
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div><!-- /row -->
</div>

<?php require __DIR__ . '/../partials/booking_modal.php'; ?>
