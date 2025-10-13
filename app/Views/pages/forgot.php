<?php // Forgot Password – Request ?>
<div class="d-flex flex-column align-items-center">

  <!-- Brand -->
  <div class="brand-row mb-3">
    <img src="assets/img/logo.svg" alt="FlyDreamAir" class="brand-logo">
    <div class="d-flex flex-column">
      <span class="brand-title">FlyDreamAir</span>
      <span class="brand-sub">Premium Lounges</span>
    </div>
  </div>

  <!-- Title + subtitle -->
  <h1 class="welcome-title text-center mb-1">Reset Your Password</h1>
  <p class="welcome-sub text-center mb-4">
    Enter your email address and we'll send you instructions to reset your password
  </p>

  <!-- Glass card -->
  <div class="glass-card p-4 p-md-4" style="width:392px;">

    <!-- Centered heading with icon -->
    <p class="text-center mb-5 text-muted">
      <img src="assets/img/sign-in-icon.svg" width="16" height="16" alt="" class="svg-primary">
      <span>Account Recovery</span>
    </p>

    <form action="<?= base_href('forgot_done') ?>" method="get">
      <div class="mb-3">
        <label class="form-label small fw-semibold d-flex align-items-center gap-2">
          <img src="assets/img/email-icon.svg" width="16" height="16" alt=""><span>Email Address</span>
        </label>
        <input type="email" class="form-control fda-input" name="email" placeholder="your@email.com" required>
      </div>

      <button type="submit" class="btn btn-fda btn-fda-primary w-100 mb-3">
        <img src="assets/img/email-icon.svg" width="16" height="16" alt="">
        <span>Send Reset Instructions</span>
      </button>

      <div class="info-box note-info mb-3">
        <div class="fw-semibold mb-1">Security Note:</div>
        <div class="small text-muted-2">
          For your protection, we'll only send reset instructions to registered email addresses.
          If you don't receive an email, the address may not be associated with an account.
        </div>
      </div>

      <!-- Divider uses 10% black -->
      <hr class="glass-hr my-3">

      <!-- Plain link-style back button (no bg/border) -->
      <a class="btn-link-plain w-100 text-center d-inline-block" href="<?= base_href('welcome') ?>">
        <i class="fa-solid fa-arrow-left me-2"></i> Back to Login
      </a>
    </form>
  </div>

  <p class="demo-note mt-4">Need help? Contact customer support for assistance</p>
</div>
