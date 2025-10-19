<?php
  // Simple active helper using the router's query param
  $current = $_GET['r'] ?? 'dashboard';
  $is = fn($route) => $current === $route ? 'active' : '';
?>
<nav class="navbar navbar-expand-lg header-bar bg-white">
  <div class="container align-items-center">

    <!-- Brand -->
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
      <!-- Primary nav -->
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
        <li class="nav-item">
          <a class="nav-link app-pill <?= $is('bookings') ?>" href="<?= base_href('bookings') ?>">
            <img class="nav-icon" src="assets/img/booking-icon.svg" alt="">
            <span>My Bookings</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link app-pill <?= $is('memberships') ?>" href="<?= base_href('memberships') ?>">
            <img class="nav-icon" src="assets/img/membership-icon.svg" alt="">
            <span>Membership</span>
          </a>
        </li>
      </ul>

      <!-- Right side: membership badge + avatar -->
      <div class="d-flex align-items-center gap-3">
        <span class="member-badge">BASIC Member</span>
        <a href="<?= base_href('profile') ?>" class="avatar-initials text-decoration-none">JS</a>
      </div>
    </div>
  </div>
</nav>
