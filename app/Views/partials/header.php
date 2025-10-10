
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_href('dashboard') ?>">FlyDreamAir</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?= base_href('dashboard') ?>">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_href('find') ?>">Find Lounges</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_href('bookings') ?>">My Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_href('memberships') ?>">Memberships</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_href('profile') ?>">Profile</a></li>
      </ul>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_href('welcome') ?>">Exit</a>
      </div>
    </div>
  </div>
</nav>
