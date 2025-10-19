<!-- Dashboard page -->
<div class="container py-4">

    <!-- Page header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <?php $u = current_user(); $greet = $u ? ($u['first_name'].' '.$u['last_name']) : 'Guest'; ?>
            <h2 class="mb-1 fw-bold">Welcome back, <?= htmlspecialchars($greet) ?></h2>
            <div class="text-muted">Here's what's happening with your lounge access</div>
        </div>
        <a href="<?= base_href('find') ?>" class="btn btn-fda btn-fda-primary btn-fda-fit"
            style="height:36px; padding:0 14px;">
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
                    <div class="display-6 fw-bold">2</div>
                </div>
            </div>
        </div>

        <!-- Total visits -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small">Total Visits</div>
                        <img src="assets/img/location-icon.svg" class="metric-icon tint-green" alt="">
                    </div>
                    <div class="display-6 fw-bold">0</div>
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
                    <div class="h4 mb-0 fw-bold">Pay-per-use</div>
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
                    <div class="h4 mb-0 fw-bold">Basic (Free)</div>
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
                        <!-- same border color as cards + primary text -->
                        <a href="#" class="btn btn-sm btn-outline-plain">View All</a>
                    </div>

                    <!-- Visit item -->
                    <div class="visit-item card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">FlyDreamAir Premium Lounge</div>
                                <div class="text-muted small">SIN</div>
                                <div class="text-muted small mt-2 d-flex align-items-center gap-3 flex-wrap">
                                    <span>
                                        <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted me-1"
                                            alt="">
                                        Tue, Dec 15
                                    </span>
                                    <span>
                                        <img src="assets/img/time-icon.svg" class="inline-icon tint-muted me-1" alt="">
                                        14:00 - 18:00
                                    </span>
                                    <span>
                                        <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted me-1" alt="">
                                        2 people
                                    </span>
                                </div>
                            </div>
                            <span class="status-pill status-ok">confirmed</span>
                        </div>
                    </div>

                    <!-- Visit item -->
                    <div class="visit-item card">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">FlyDreamAir Sydney Lounge</div>
                                <div class="text-muted small">SYD</div>
                                <div class="text-muted small mt-2 d-flex align-items-center gap-3 flex-wrap">
                                    <span>
                                        <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted me-1"
                                            alt="">
                                        Sun, Dec 20
                                    </span>
                                    <span>
                                        <img src="assets/img/time-icon.svg" class="inline-icon tint-muted me-1" alt="">
                                        09:00 - 13:00
                                    </span>
                                    <span>
                                        <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted me-1" alt="">
                                        3 people
                                    </span>
                                </div>
                            </div>
                            <span class="status-pill status-ok">confirmed</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Membership Status -->
        <div class="col-lg-4">
            <div class="card panel-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="">Membership Status</div>
                        <span class="status-pill status-ok">Active</span>
                    </div>

                    <div class="fw-bold mb-2">Basic (Free) Membership</div>
                    <div class="text-muted small mb-1">Member ID: BS123456</div>
                    <div class="text-muted small mb-1">Monthly Fee: Free</div>
                    <div class="text-muted small mb-3">Guest Allowance: 0 per visit</div>

                    <div class="fw-semibold mb-2">Benefits</div>
                    <ul class="list-unstyled small text-muted mb-4">
                        <li class="d-flex align-items-center gap-2 mb-1">
                            <img src="assets/img/star-icons.svg" width="16" height="16" alt=""> Free membership signup
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-1">
                            <img src="assets/img/star-icons.svg" width="16" height="16" alt=""> Buy single-visit pass
                            for all lounges, including premium lounges
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <img src="assets/img/star-icons.svg" width="16" height="16" alt=""> Wi-Fi access
                        </li>
                    </ul>

                    <!-- same 10% border + primary text -->
                    <button class="btn w-100 btn-outline-plain">Manage Membership</button>
                </div>
            </div>
        </div>
    </div>

</div>