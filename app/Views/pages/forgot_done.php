<?php // Forgot Password – Success (check your email)
$email = $email ?? ''; ?>
<div class="d-flex flex-column align-items-center">

  <!-- Brand -->
  <div class="brand-row mb-3">
    <img src="assets/img/logo.svg" alt="FlyDreamAir" class="brand-logo">
    <div class="d-flex flex-column">
      <span class="brand-title">FlyDreamAir</span>
      <span class="brand-sub">Premium Lounges</span>
    </div>
  </div>

  <div class="glass-card p-4 p-md-4 text-center" style="width:392px;">
    <!-- Check icon in green circle (SVG, 28px; circle 56px) -->
    <div class="icon-check-circle mx-auto mb-3">
      <img src="assets/img/check-icon.svg" alt="" class="icon-check">
    </div>

    <h2 class="h5 fw-semibold mb-2">Check Your Email</h2>
    <p class="welcome-sub mb-1">We've sent password reset instructions to:</p>
    <p class="fw-bold mb-3"><?= htmlspecialchars($email) ?></p>

    <p class="text-muted small mb-3">
      Please check your inbox and follow the link to reset your password.
      The link will expire in 24 hours.
    </p>

    <div class="info-box note-info text-start mb-3">
      <div class="fw-semibold mb-1">Didn't receive the email?</div>
      <div class="small text-muted-2">
        Check your spam folder or try again in a few minutes.
      </div>
    </div>

    <a class="btn btn-fda btn-fda-primary w-100 mb-2" href="<?= base_href('welcome') ?>">
      <i class="fa-solid fa-arrow-left me-2"></i> Back to Login
    </a>
    <a class="btn btn-fda btn-fda-ghost w-100" href="<?= base_href('forgot') ?>">
      Use a Different Email
    </a>
  </div>
</div>
