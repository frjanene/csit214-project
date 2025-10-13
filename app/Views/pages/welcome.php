<?php // Welcome – Bootstrap-first, with in-page Auth Modal ?>
<div class="d-flex flex-column align-items-center">

  <!-- Brand: logo left, text right -->
  <div class="brand-row mb-3">
    <img src="assets/img/logo.svg" alt="FlyDreamAir" class="brand-logo">
    <div class="d-flex flex-column">
      <span class="brand-title">FlyDreamAir</span>
      <span class="brand-sub">Premium Lounges</span>
    </div>
  </div>

  <!-- Title + subtitle -->
  <h1 class="welcome-title text-center mb-1">Welcome to FlyDreamAir</h1>
  <p class="welcome-sub text-center mb-4">Your gateway to premium airline lounges worldwide</p>

  <!-- Glass card (exact figma width/height) -->
  <div class="glass-card p-4 p-md-4 d-flex flex-column align-items-center">
    <p class="text-center mb-5 text-muted">Get Started</p>

    <!-- Triggers open modal + pick tab -->
    <button class="btn btn-fda btn-fda-primary mb-2"
            data-bs-toggle="modal" data-bs-target="#authModal"
            data-auth-tab="signin">
      <img src="assets/img/sign-in-icon.svg" width="16" height="16" alt="">
      <span>Sign In to Your Account</span>
    </button>

    <button class="btn btn-fda btn-fda-ghost mb-2"
            data-bs-toggle="modal" data-bs-target="#authModal"
            data-auth-tab="signup">
      <img src="assets/img/create-account-icon.svg" width="16" height="16" alt="">
      <span>Create New Account</span>
    </button>

    <div class="hr-or my-2 w-100" style="max-width: var(--fda-btn-width);">OR</div>

    <button class="btn btn-fda btn-fda-secondary"
            data-bs-toggle="modal" data-bs-target="#authModal"
            data-auth-tab="signin">
      <img src="assets/img/guest-icon.svg" width="16" height="16" alt="">
      <span>Continue as Guest</span>
    </button>
  </div>

  <!-- Why choose (page footer) -->
  <div class="text-center mt-5" style="max-width:640px;">
    <p class="why-title mb-3">Why choose FlyDreamAir?</p>
    <ul class="list-unstyled text-start mx-auto why-list">
      <li class="why-item mb-2"><i class="bullet bullet-blue"></i>Access to 3+ premium lounges worldwide</li>
      <li class="why-item mb-2"><i class="bullet bullet-green"></i>Flexible booking and cancellation</li>
      <li class="why-item mb-2"><i class="bullet bullet-purple"></i>Exclusive member benefits and rewards</li>
    </ul>
    <p class="demo-note">Demo application - no real authentication required</p>
  </div>

</div>

<!-- =========================
     AUTH MODAL (Sign In / Sign Up tabs)
     ========================= -->
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
  <!-- width now matches glass card via .auth-modal-dialog (392px) -->
  <div class="modal-dialog modal-dialog-centered auth-modal-dialog">
    <div class="modal-content auth-modal">
      <div class="modal-header border-0">
        <button type="button" class="btn-close me-1" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pt-0">
        <h2 class="h4 text-center fw-bold mb-1">Welcome</h2>
        <p class="text-center text-muted mb-4">Access your premium lounge experience</p>

        <!-- Tab rail: 31.5px rail, 25.5px active pill -->
        <div class="auth-tab-rail mb-4">
          <ul class="nav nav-pills auth-tabs" id="authTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="signin-tab" data-bs-toggle="pill" data-bs-target="#signin-pane" type="button" role="tab">Sign In</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="signup-tab" data-bs-toggle="pill" data-bs-target="#signup-pane" type="button" role="tab">Create Account</button>
            </li>
          </ul>
        </div>

        <div class="tab-content">
          <!-- Sign In -->
          <div class="tab-pane fade show active" id="signin-pane" role="tabpanel" aria-labelledby="signin-tab">
            <form>
              <div class="mb-3">
                <label class="form-label small fw-semibold d-flex align-items-center gap-2">
                  <img src="assets/img/email-icon.svg" width="16" height="16" alt=""><span>Email Address</span>
                </label>
                <input type="email" class="form-control fda-input" placeholder="john@example.com">
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold d-flex align-items-center gap-2">
                  <img src="assets/img/password-icon.svg" width="16" height="16" alt=""><span>Password</span>
                </label>
                <div class="fda-input-wrap">
                  <input type="password" class="form-control fda-input" placeholder="Enter your password" data-password>
                  <button type="button" class="toggle-pass" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-3 auth-links">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="rememberMe">
                  <label class="form-check-label small" for="rememberMe">Remember me</label>
                </div>
                <a href="<?= base_href('forgot') ?>" class="small fw-semibold forgot">Forgot password?</a>

              </div>

              <button type="submit" class="btn btn-auth btn-auth-primary w-100 mb-3">
                Sign In
              </button>

              <hr class="my-3">

              <!-- Why choose inside modal -->
              <div>
                <p class="why-title mb-2">Why choose FlyDreamAir?</p>
                <ul class="list-unstyled mb-0">
                  <li class="why-item mb-2">
                    <img src="assets/img/flight-icon.svg" width="16" height="16" class="me-2" alt="">
                    Access to 3+ premium lounges worldwide
                  </li>
                  <li class="why-item mb-2">
                    <img src="assets/img/check-icon.svg" width="16" height="16" class="me-2" alt="">
                    Flexible booking and cancellation
                  </li>
                  <li class="why-item">
                    <img src="assets/img/guest-icon-2.svg" width="16" height="16" class="me-2" alt="">
                    Guest privileges and family plans
                  </li>
                </ul>
              </div>
            </form>
          </div>

          <!-- Sign Up -->
          <div class="tab-pane fade" id="signup-pane" role="tabpanel" aria-labelledby="signup-tab">
            <form>
              <div class="row g-3">
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold">First Name</label>
                  <input type="text" class="form-control fda-input" placeholder="John">
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold">Last Name</label>
                  <input type="text" class="form-control fda-input" placeholder="Doe">
                </div>
              </div>

              <div class="mt-3">
                <label class="form-label small fw-semibold d-flex align-items-center gap-2">
                  <img src="assets/img/email-icon.svg" width="16" height="16" alt=""><span>Email Address</span>
                </label>
                <input type="email" class="form-control fda-input" placeholder="john@example.com">
              </div>

              <div class="mt-3">
                <label class="form-label small fw-semibold d-flex align-items-center gap-2">
                  <img src="assets/img/password-icon.svg" width="16" height="16" alt=""><span>Password</span>
                </label>
                <div class="fda-input-wrap">
                  <input type="password" class="form-control fda-input" placeholder="Create a strong password" data-password>
                  <button type="button" class="toggle-pass" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                </div>
              </div>

              <div class="mt-3">
                <label class="form-label small fw-semibold d-flex align-items-center gap-2">
                  <img src="assets/img/password-icon.svg" width="16" height="16" alt=""><span>Confirm Password</span>
                </label>
                <div class="fda-input-wrap">
                  <input type="password" class="form-control fda-input" placeholder="Confirm your password" data-password>
                  <button type="button" class="toggle-pass" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                </div>
              </div>

              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" id="tos">
                <label class="form-check-label small" for="tos">
                  I accept the <a href="#" class="text-decoration-none fw-semibold">Terms of Service</a> and <a href="#" class="text-decoration-none fw-semibold">Privacy Policy</a>
                </label>
              </div>

              <div class="form-check mt-2 mb-3">
                <input class="form-check-input" type="checkbox" id="newsletter">
                <label class="form-check-label small" for="newsletter">Subscribe to newsletter for exclusive offers</label>
              </div>

              <button type="submit" class="btn btn-auth btn-auth-primary w-100 mb-3">
                Create Account
              </button>

              <hr class="my-3">

              <div>
                <p class="why-title mb-2">Why choose our service?</p>
                <ul class="list-unstyled mb-0">
                  <li class="why-item mb-2">
                    <img src="assets/img/flight-icon.svg" width="16" height="16" class="me-2" alt="">
                    Access to 3+ premium lounges worldwide
                  </li>
                  <li class="why-item mb-2">
                    <img src="assets/img/check-icon.svg" width="16" height="16" class="me-2" alt="">
                    Flexible booking and cancellation
                  </li>
                  <li class="why-item">
                    <img src="assets/img/guest-icon-2.svg" width="16" height="16" class="me-2" alt="">
                    Guest privileges and family plans
                  </li>
                </ul>
              </div>
            </form>
          </div>
        </div> <!-- /tab-content -->
      </div> <!-- /modal-body -->
    </div>
  </div>
</div>
