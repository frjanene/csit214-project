<!-- Dashboard page -->
<?php
  $u = $user ?? (function_exists('current_user') ? current_user() : null);
  $isGuest = empty($u) || (int)($u['id'] ?? 0) === 0;

  $greet = $u ? trim(($u['first_name'] ?? '').' '.($u['last_name'] ?? '')) : 'Guest';

  $activeBookings = (int)($metrics['active_bookings'] ?? 0);
  $totalVisits    = (int)($metrics['total_visits'] ?? 0);

  $plan = $plan ?? ['slug'=>'basic','name'=>'Basic','guest_allowance'=>0,'normal_access'=>'pay_per_use','premium_access'=>'pay_per_use'];
  $planName  = $plan['name'] ?? 'Basic';
  $guestAllow= (int)($plan['guest_allowance'] ?? 0);
  $guestText = $guestAllow > 0 ? ("{$guestAllow} per visit") : 'Pay-per-use';

  // Member ID: clearer label for guests
  $memberId  = $isGuest ? 'GUEST' : ('U'.str_pad((string)($u['id'] ?? 0), 6, '0', STR_PAD_LEFT));

  // Helper to format a compact date like "Tue, Dec 15"
  $fmtShortDate = function(string $ymd): string {
    $d = DateTime::createFromFormat('Y-m-d', $ymd);
    return $d ? $d->format('D, M j') : htmlspecialchars($ymd);
  };
?>
<div class="container py-4">

  <!-- Page header -->
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h2 class="mb-1 fw-bold">Welcome<?= $isGuest ? '' : ' back' ?>, <?= htmlspecialchars($greet ?: 'Guest') ?></h2>
      <div class="text-muted">Here's what's happening with your lounge access</div>
    </div>
    <a href="<?= base_href('find') ?>" class="btn btn-fda btn-fda-primary btn-fda-fit" style="height:36px; padding:0 14px;">
      Find Lounges
    </a>
  </div>

  <!-- KPI / metric cards -->
  <div class="row g-3 mb-4">
    <!-- Active bookings -->
    <div class="col-lg-3 col-md-6">
      <div class="card metric-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-muted small">Active Bookings</div>
            <img src="assets/img/booking-icon.svg" class="metric-icon tint-blue" alt="">
          </div>
          <div class="display-6 fw-bold"><?= (int)$activeBookings ?></div>
        </div>
      </div>
    </div>

    <!-- Total visits (completed) -->
    <div class="col-lg-3 col-md-6">
      <div class="card metric-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-muted small">Total Visits</div>
            <img src="assets/img/location-icon.svg" class="metric-icon tint-green" alt="">
          </div>
          <div class="display-6 fw-bold"><?= (int)$totalVisits ?></div>
        </div>
      </div>
    </div>

    <!-- Guest allowance -->
    <div class="col-lg-3 col-md-6">
      <div class="card metric-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-muted small">Guest Allowance</div>
            <img src="assets/img/guest-icon.svg" class="metric-icon tint-purple" alt="">
          </div>
          <div class="h4 mb-0 fw-bold"><?= htmlspecialchars($guestText) ?></div>
        </div>
      </div>
    </div>

    <!-- Membership tier -->
    <div class="col-lg-3 col-md-6">
      <div class="card metric-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-muted small">Membership Tier</div>
            <img src="assets/img/membership-tier-icon.svg" class="metric-icon tint-orange" alt="">
          </div>
          <div class="h4 mb-0 fw-bold"><?= htmlspecialchars($planName) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Upcoming Visits -->
    <div class="col-lg-8">
      <div class="card panel-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-semibold">Upcoming Visits</div>
            <a href="<?= base_href('bookings') ?>" class="btn btn-sm btn-outline-plain">View All</a>
          </div>

          <?php if (!empty($upcoming)): ?>
            <?php foreach ($upcoming as $v): ?>
              <div class="visit-item card mb-3">
                <div class="card-body d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars($v['lounge_name']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($v['airport_iata']) ?></div>
                    <div class="text-muted small mt-2 d-flex align-items-center gap-3 flex-wrap">
                      <span>
                        <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted me-1" alt="">
                        <?= $fmtShortDate($v['visit_date']) ?>
                      </span>
                      <span>
                        <img src="assets/img/time-icon.svg" class="inline-icon tint-muted me-1" alt="">
                        <?= htmlspecialchars(substr($v['start_time'],0,5) . ' - ' . substr($v['end_time'],0,5)) ?>
                      </span>
                      <span>
                        <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted me-1" alt="">
                        <?= (int)$v['people_count'] ?> <?= ((int)$v['people_count']>1?'people':'person') ?>
                      </span>
                    </div>
                  </div>
                  <?php
                    $st = strtolower($v['status']);
                    $pillClass = ($st==='cancelled'?'status-cancel':($st==='completed'?'status-done':'status-ok'));
                  ?>
                  <span class="status-pill <?= $pillClass ?>"><?= htmlspecialchars($st) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-muted small">
              <?= $isGuest
                ? 'No upcoming visits yet. You can still explore lounges below.'
                : 'You have no upcoming visits. ' ?>
              <a href="<?= base_href('find') ?>">Find a lounge</a> to book.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Membership Status -->
    <div class="col-lg-4">
      <div class="card panel-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="">Membership Status</div>
            <span class="status-pill <?= $isGuest ? 'status-ok' : 'status-ok' ?>">
              <?= $isGuest ? 'Guest' : 'Active' ?>
            </span>
          </div>

          <div class="fw-bold mb-2"><?= htmlspecialchars($planName) ?> Membership</div>
          <div class="text-muted small mb-1">Member ID: <?= htmlspecialchars($memberId) ?></div>
          <div class="text-muted small mb-1">
            Monthly Fee: <?= strtolower($planName)==='basic' ? 'Free' : ('$'.number_format((float)($plan['monthly_fee_usd'] ?? $plan['price_usd'] ?? 0), 2)) ?>
          </div>
          <div class="text-muted small mb-3">Guest Allowance: <?= htmlspecialchars($guestText) ?></div>

          <div class="fw-semibold mb-2">Benefits</div>
          <ul class="list-unstyled small text-muted mb-4">
            <li class="d-flex align-items-center gap-2 mb-1">
              <img src="assets/img/star-icons.svg" width="16" height="16" alt=""> Free membership signup
            </li>
            <li class="d-flex align-items-center gap-2 mb-1">
              <img src="assets/img/star-icons.svg" width="16" height="16" alt=""> Buy single-visit pass for all lounges, including premium lounges
            </li>
            <li class="d-flex align-items-center gap-2">
              <img src="assets/img/star-icons.svg" width="16" height="16" alt=""> Wi-Fi access
            </li>
          </ul>

          <a class="btn w-100 btn-outline-plain" href="<?= base_href('memberships') ?>">
            <?= $isGuest ? 'See Membership Options' : 'Manage Membership' ?>
          </a>
        </div>
      </div>
    </div>
  </div>

</div>
