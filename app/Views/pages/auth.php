
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body p-0">
        <ul class="nav nav-tabs px-3 pt-3" id="authTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="signin-tab" data-bs-toggle="tab" data-bs-target="#signin" type="button" role="tab">Sign in</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup" type="button" role="tab">Create account</button>
          </li>
        </ul>
        <div class="tab-content p-4">
          <div class="tab-pane fade show active" id="signin" role="tabpanel" aria-labelledby="signin-tab">
            <!-- empty scaffold -->
          </div>
          <div class="tab-pane fade" id="signup" role="tabpanel" aria-labelledby="signup-tab">
            <!-- empty scaffold -->
          </div>
        </div>
      </div>
    </div>
    <div class="text-center mt-3">
      <a class="btn btn-outline-dark btn-lg" href="<?= base_href('dashboard') ?>">Continue as Guest</a>
    </div>
  </div>
</div>
