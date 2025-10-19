<?php
/** app/Views/pages/memberships.php */

$plans    = $plans    ?? [];
$current  = $current  ?? null;
$user     = $user     ?? null;
$isGuest  = !$user;

/**
 * Decide which slug is "current" for pill highlighting.
 * Prefer the controller-provided $currSlug (already normalized),
 * else fall back to the current plan's slug, else "basic".
 */
$currSlug = isset($currSlug)
  ? strtolower($currSlug)
  : strtolower($current['slug'] ?? 'basic');

// If controller didn’t resolve a $current plan (edge case), default to basic
if (!$current && isset($plans['basic'])) {
  $current = $plans['basic'];
}

// Small helper for price formatting
$fmtPrice = fn($p) => (float)$p > 0 ? ('$' . number_format((float)$p, 0)) : 'Free';
?>
<div class="container py-4">
  <div class="mb-3">
    <h2 class="fw-bold mb-1">Membership Management</h2>
    <div class="text-muted">Choose the perfect membership tier for your travel needs</div>
  </div>

  <!-- Current membership summary -->
  <div class="card member-current mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
        <div class="label-small">Your Current Membership</div>
        <span class="badge badge-active">Active</span>
      </div>
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-semibold"><?= htmlspecialchars($current['name'] ?? 'Basic (Free)') ?> Member</div>
        <div class="fw-semibold"><?= $fmtPrice($current['monthly_fee_usd'] ?? 0) ?></div>
      </div>
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted small">
        <div>Member ID: <span class="text-muted"><?= $user ? ('U' . $user['id']) : '—' ?></span></div>
        <div><?= ($current['slug'] ?? 'basic') === 'basic' ? 'No renewal required' : 'Renews monthly' ?></div>
      </div>

      <div class="row g-3 mt-3">
        <div class="col-12 col-md-4">
          <div class="member-tile vcenter">
            <div class="tile-ico"><img src="assets/img/lounge-icon.svg" class="inline-icon tint-blue" alt=""></div>
            <div class="tile-lines">
              <div class="small text-muted">Lounge Access</div>
              <div class="fw-semibold">
                <?= ($current['premium_access'] ?? 'pay_per_use') === 'free'
                      ? 'All incl. Premium'
                      : (($current['normal_access'] ?? 'pay_per_use') === 'free'
                          ? 'Normal lounges free'
                          : 'Pay-per-use') ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="member-tile vcenter">
            <div class="tile-ico"><img src="assets/img/guest-icon.svg" class="inline-icon tint-green" alt=""></div>
            <div class="tile-lines">
              <div class="small text-muted">Guest Allowance</div>
              <div class="fw-semibold"><?= (int)($current['guest_allowance'] ?? 0) ?> per visit</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="member-tile vcenter">
            <div class="tile-ico"><img src="assets/img/star.svg" class="inline-icon tint-purple" alt=""></div>
            <div class="tile-lines">
              <div class="small text-muted">Benefits</div>
              <div class="fw-semibold"><?= isset($current['benefits']) ? count($current['benefits']) : 0 ?> included</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tiers -->
  <div class="mb-2 fw-semibold">Available Membership Tiers</div>
  <div class="row g-3 mb-4">
    <?php foreach ($plans as $slug => $p): ?>
      <?php
        $slugLower = strtolower($slug);
        $isCurrent = ($slugLower === $currSlug);
        $price     = $fmtPrice($p['monthly_fee_usd']);
      ?>
      <div class="col-12 col-md-6 col-xl-3">
        <div class="card tier-card h-100 position-relative <?= $isCurrent ? 'current' : '' ?>">
          <?php if ($isCurrent): ?>
            <span class="pill-current pill-centered">Current Plan</span>
          <?php endif; ?>
          <div class="card-body d-flex flex-column align-items-stretch">
            <div class="tier-top text-center">
              <span class="ico-circle mb-2">
                <?php if ($slugLower==='silver'): ?><i class="fa-solid fa-bolt"></i>
                <?php elseif ($slugLower==='gold'): ?><i class="fa-regular fa-star"></i>
                <?php elseif ($slugLower==='platinum'): ?><i class="fa-solid fa-crown"></i>
                <?php else: ?><i class="fa-regular fa-id-badge"></i><?php endif; ?>
              </span>
              <div class="fw-semibold"><?= htmlspecialchars($p['name']) ?></div>
              <div class="h3 fw-bold my-1"><?= $price ?></div>
              <div class="text-muted small mb-3"><?= (float)$p['monthly_fee_usd'] > 0 ? 'per month' : '&nbsp;' ?></div>
            </div>

            <ul class="member-list small">
              <?php foreach (array_slice($p['benefits'] ?? [], 0, 6) as $b): ?>
                <li><?= htmlspecialchars($b) ?></li>
              <?php endforeach; ?>
            </ul>

            <hr class="tier-sep">
            <div class="small">
              <div class="d-flex justify-content-between">
                <span class="text-muted">Guest Allowance:</span>
                <span class="fw-semibold"><?= (int)$p['guest_allowance'] ?> per visit</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Lounge Access:</span>
                <span class="fw-semibold">Global</span>
              </div>
            </div>

            <div class="mt-auto pt-3">
              <?php if ($isCurrent): ?>
                <button class="btn btn-disabled w-100" disabled>Current Plan</button>
              <?php else: ?>
                <?php if ($isGuest): ?>
                  <a class="btn btn-fda btn-fda-primary w-100" href="<?= base_href('welcome') ?>">Sign up to Upgrade</a>
                <?php else: ?>
                  <button
                    class="btn btn-fda btn-fda-primary w-100 btn-upgrade-tier"
                    data-plan="<?= htmlspecialchars($slugLower) ?>"
                    data-price="<?= htmlspecialchars((string)$p['monthly_fee_usd']) ?>"
                    data-benefits='<?= json_encode($p["benefits"] ?? []) ?>'>
                    Upgrade to <?= htmlspecialchars($p['name']) ?>
                  </button>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- (Optional) Feature comparison table can remain below if you have it in a separate partial -->
</div>

<?php require __DIR__ . '/../partials/membership_upgrade_modal.php'; ?>
