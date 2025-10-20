<?php
// app/Views/pages/bookings.php

// Small helpers for the view (no side effects)
$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

$statusClass = function ($status) {
  $s = strtolower(trim((string)$status));
  return $s === 'cancelled' ? 'status-cancel' : ($s === 'completed' ? 'status-done' : 'status-ok');
};

$prettyDate = function ($ymd) {
  // Expect Y-m-d; fallback to original on parse errors
  try {
    $dt = new DateTimeImmutable($ymd);
    return $dt->format('l, F j, Y');
  } catch (Throwable $e) {
    return $ymd;
  }
};

$timeRange = function ($start, $end) {
  $s = substr((string)$start, 0, 5);
  $e = substr((string)$end,   0, 5);
  return trim($s.' - '.$e);
};

$peopleLabel = fn($n) => ($n = (int)$n) <= 1 ? '1 person' : ($n.' people');

$paymentLabel = function ($method, $total) {
  $m = strtolower((string)$method);
  if ($m === 'membership') return 'Membership Access';
  $t = (float)$total;
  return $t > 0 ? 'Pay Per Use' : 'Membership Access';
};

$totalLabel = function ($total) {
  $t = (float)$total;
  return $t > 0 ? '$'.number_format($t, 0) : 'FREE';
};

$airportBadge = function ($iata) use ($h) {
  $iata = strtoupper(trim((string)$iata));
  return $iata !== '' ? $h($iata) : '—';
};

// Defensive defaults if controller didn’t pass something
$upcoming        = $upcoming        ?? [];
$past            = $past            ?? [];
$count_upcoming  = $count_upcoming  ?? count($upcoming);
$count_past      = $count_past      ?? count($past);
?>

<!-- My Bookings -->
<div class="container py-4">

  <!-- Page title + filters -->
  <div class="d-flex align-items-start justify-content-between mb-3">
    <div>
      <h2 class="fw-bold mb-1">My Bookings</h2>
      <div class="text-muted">View and manage your lounge reservations</div>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div class="dropdown filter-dd">
        <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          All Status
        </button>
        <ul class="dropdown-menu shadow-sm">
          <li><a class="dropdown-item active" href="#">All Status</a></li>
          <li><a class="dropdown-item" href="#">Confirmed</a></li>
          <li><a class="dropdown-item" href="#">Cancelled</a></li>
          <li><a class="dropdown-item" href="#">Completed</a></li>
        </ul>
      </div>

      <div class="dropdown filter-dd">
        <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          By Date
        </button>
        <ul class="dropdown-menu shadow-sm">
          <li><a class="dropdown-item active" href="#">Newest first</a></li>
          <li><a class="dropdown-item" href="#">Oldest first</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Tabs: same rail/pills style as auth modal -->
  <div class="auth-tab-rail bk-tab-rail mb-3">
    <ul class="nav nav-pills auth-tabs" id="bookingTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming-pane" type="button" role="tab">
          Upcoming (<?= (int)$count_upcoming ?>)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="past-tab" data-bs-toggle="pill" data-bs-target="#past-pane" type="button" role="tab">
          Past (<?= (int)$count_past ?>)
        </button>
      </li>
    </ul>
  </div>

  <div class="tab-content">

    <!-- ========== UPCOMING ========== -->
    <div class="tab-pane fade show active" id="upcoming-pane" role="tabpanel" aria-labelledby="upcoming-tab">
      <div class="d-flex flex-column gap-3">

        <?php if (empty($upcoming)): ?>
          <div class="card booking-item">
            <div class="card-body text-center text-muted">
              <div class="mb-1 fw-semibold">No upcoming bookings</div>
              <div class="small">When you book a lounge, it’ll appear here.</div>
            </div>
          </div>
        <?php else: ?>
          <?php foreach ($upcoming as $r): ?>
            <?php
              // Expect keys: id, lounge_name, iata, visit_date, start_time, end_time, people_count, method, total_usd, status, flight_number, booked_on, qr_code
              $title    = $r['lounge_name'] ?? 'Lounge';
              $iata     = $r['iata'] ?? '';
              $dateTxt  = $prettyDate($r['visit_date'] ?? '');
              $timeTxt  = $timeRange($r['start_time'] ?? '', $r['end_time'] ?? '');
              $people   = $peopleLabel($r['people_count'] ?? 1);
              $status   = $r['status'] ?? 'confirmed';
              $payLbl   = $paymentLabel($r['method'] ?? '', $r['total_usd'] ?? 0);
              $totalLbl = $totalLabel($r['total_usd'] ?? 0);
              $flight   = trim((string)($r['flight_number'] ?? ''));
              $flightTxt= $flight ? ($flight.' at '.substr($r['end_time'] ?? '', 0, 5)) : '—';
              $qrImg    = !empty($r['qr_code']) ? (base_href('qr_img').'&code='.rawurlencode($r['qr_code']).'&s=180') : '';
            ?>
            <div class="visit-item card booking-item">
              <div class="card-body">
                <span class="status-pill <?= $statusClass($status) ?> booking-status"><?= $h($status) ?></span>

                <div class="fw-semibold"><?= $h($title) ?></div>
                <div class="text-muted small"><?= $airportBadge($iata) ?></div>

                <div class="meta-list text-muted small mt-2">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted" alt="">
                    <span><?= $h($dateTxt) ?></span>
                  </div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                    <span><?= $h($timeTxt) ?></span>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted" alt="">
                    <span><?= $h($people) ?></span>
                  </div>
                </div>

                <div class="small fw-semibold mt-3"><?= $h($payLbl) ?></div>
                <?php if (!empty($r['created_at'])): ?>
                  <div class="text-muted small">Booked on <?= $h((new DateTimeImmutable($r['created_at']))->format('n/j/Y')) ?></div>
                <?php endif; ?>

                <!-- Kebab actions (bottom-right, plain) -->
                <div class="dropdown booking-actions">
                  <button class="btn btn-kebab-plain" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end action-menu shadow-sm">
                    <li>
                      <a class="dropdown-item"
                         href="#"
                         data-bs-toggle="modal"
                         data-bs-target="#bookingDetailsModal"
                         data-bd-title="<?= $h($title) ?>"
                         data-bd-airport="<?= $airportBadge($iata) ?>"
                         data-bd-date="<?= $h($dateTxt) ?>"
                         data-bd-time="<?= $h($timeTxt) ?>"
                         data-bd-people="<?= $h($people.' total') ?>"
                         data-bd-flight="<?= $h($flight ? $flight.' at '.substr($r['start_time'] ?? '', 0, 5) : '—') ?>"
                         data-bd-status="<?= $h($status) ?>"
                         data-bd-total="<?= $h($totalLbl) ?>"
                         <?php if ($qrImg): ?>data-bd-qr="<?= $h($qrImg) ?>"<?php endif; ?>
                      >View Details</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <a class="dropdown-item text-danger"
                         href="#"
                         data-action="cancel-booking"
                         data-booking-id="<?= (int)($r['id'] ?? 0) ?>"
                      >Cancel Booking</a>
                    </li>
                  </ul>
                </div>

              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>

    <!-- ========== PAST ========== -->
    <div class="tab-pane fade" id="past-pane" role="tabpanel" aria-labelledby="past-tab">
      <div class="d-flex flex-column gap-3">

        <?php if (empty($past)): ?>
          <div class="card booking-item">
            <div class="card-body text-center text-muted">
              <div class="mb-1 fw-semibold">No past bookings</div>
              <div class="small">Completed and cancelled reservations will appear here.</div>
            </div>
          </div>
        <?php else: ?>
          <?php foreach ($past as $r): ?>
            <?php
              $title    = $r['lounge_name'] ?? 'Lounge';
              $iata     = $r['iata'] ?? '';
              $dateTxt  = $prettyDate($r['visit_date'] ?? '');
              $timeTxt  = $timeRange($r['start_time'] ?? '', $r['end_time'] ?? '');
              $people   = $peopleLabel($r['people_count'] ?? 1);
              $status   = $r['status'] ?? 'completed';
              $payLbl   = $paymentLabel($r['method'] ?? '', $r['total_usd'] ?? 0);
              $totalLbl = $totalLabel($r['total_usd'] ?? 0);
              $flight   = trim((string)($r['flight_number'] ?? ''));
              $qrImg    = !empty($r['qr_code']) ? (base_href('qr_img').'&code='.rawurlencode($r['qr_code']).'&s=180') : '';
            ?>
            <div class="visit-item card booking-item">
              <div class="card-body">
                <span class="status-pill <?= $statusClass($status) ?> booking-status"><?= $h($status) ?></span>

                <div class="fw-semibold"><?= $h($title) ?></div>
                <div class="text-muted small"><?= $airportBadge($iata) ?></div>

                <div class="meta-list text-muted small mt-2">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted" alt="">
                    <span><?= $h($dateTxt) ?></span>
                  </div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                    <span><?= $h($timeTxt) ?></span>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted" alt="">
                    <span><?= $h($people) ?></span>
                  </div>
                </div>

                <div class="small fw-semibold mt-3"><?= $h($payLbl) ?></div>

                <div class="dropdown booking-actions">
                  <button class="btn btn-kebab-plain" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end action-menu shadow-sm">
                    <li>
                      <a class="dropdown-item"
                         href="#"
                         data-bs-toggle="modal"
                         data-bs-target="#bookingDetailsModal"
                         data-bd-title="<?= $h($title) ?>"
                         data-bd-airport="<?= $airportBadge($iata) ?>"
                         data-bd-date="<?= $h($dateTxt) ?>"
                         data-bd-time="<?= $h($timeTxt) ?>"
                         data-bd-people="<?= $h($people.' total') ?>"
                         data-bd-flight="<?= $h($flight ?: '—') ?>"
                         data-bd-status="<?= $h($status) ?>"
                         data-bd-total="<?= $h($totalLbl) ?>"
                         <?php if ($qrImg): ?>data-bd-qr="<?= $h($qrImg) ?>"<?php endif; ?>
                      >View Details</a>
                    </li>
                  </ul>
                </div>

              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>

  </div><!-- /tab-content -->

</div>

<?php require __DIR__ . '/../partials/booking_details_modal.php'; ?>
