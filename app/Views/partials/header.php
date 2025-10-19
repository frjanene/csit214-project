<?php
  // Use a unique variable name for the active route to avoid conflicts
  $activeRoute = $_GET['r'] ?? 'dashboard';
  $is = fn($route) => $activeRoute === $route ? 'active' : '';

  $user = current_user();
  $isGuest = !$user;

  // Determine current membership plan for signed-in users
  $planSlug = 'basic';
  if ($user) {
    require_once __DIR__ . '/../../Models/Membership.php';
    try {
      $p = Membership::userCurrent((int)$user['id']);
      if ($p && !empty($p['slug'])) {
        $planSlug = strtolower($p['slug']);
      }
    } catch (Throwable $e) {
      // Avoid breaking header on DB errors
      $planSlug = 'basic';
    }
  }

  // Display badge text based on plan
  $badgeText = $isGuest ? 'GUEST' : (strtoupper($planSlug) . ' Member');

  // Avatar initials
  $initials = $isGuest ? 'GU' : initials_from($user['first_name'], $user['last_name']);
?>
<nav class="navbar navbar-expand-lg header-bar bg-white">
  <div class="container align-items-center">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_href('dashboard') ?>">
      <img src="assets/img/logo.svg" alt="FlyDreamAir" class="brand-logo" width="40" height="40">
      <div class="d-flex flex-column brand-stack">
        <span class="fw-bold brand-title">FlyDreamAir</span>
        <small class="brand-sub">Premium Lounges</small>
      </div>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav app-nav ms-3 me-auto">
        <li class="nav-item">
          <a class="nav-link app-pill <?= $is('dashboard') ?>" href="<?= base_href('dashboard') ?>">
            <img class="nav-icon" src="assets/img/dashboard-icon.svg" alt="">
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link app-pill <?= $is('find') ?>" href="<?= base_href('find') ?>">
            <img class="nav-icon" src="assets/img/search-icon.svg" alt="">
            <span>Find Lounges</span>
          </a>
        </li>
        <?php if (!$isGuest): ?>
          <li class="nav-item">
            <a class="nav-link app-pill <?= $is('bookings') ?>" href="<?= base_href('bookings') ?>">
              <img class="nav-icon" src="assets/img/booking-icon.svg" alt="">
              <span>My Bookings</span>
            </a>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link app-pill <?= $is('memberships') ?>" href="<?= base_href('memberships') ?>">
            <img class="nav-icon" src="assets/img/membership-icon.svg" alt="">
            <span>Membership</span>
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-3">
        <span class="member-badge"><?= htmlspecialchars($badgeText) ?></span>
        <?php if ($isGuest): ?>
          <a href="<?= base_href('welcome') ?>" class="btn btn-sm btn-outline-dark">Sign in</a>
        <?php else: ?>
          <a href="<?= base_href('profile') ?>" class="avatar-initials text-decoration-none">
            <?= htmlspecialchars($initials) ?>
          </a>
          <a href="<?= base_href('signout') ?>" class="btn btn-sm btn-outline-dark">Sign out</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
