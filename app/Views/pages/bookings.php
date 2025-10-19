<!-- My Bookings -->
<div class="container py-4">

  <!-- Page title + filters -->
  <div class="d-flex align-items-start justify-content-between mb-3">
    <div>
      <h2 class="fw-bold mb-1">My Bookings</h2>
      <div class="text-muted">View and manage your lounge reservations</div>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div class="dropdown filter-dd">
        <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          All Status
        </button>
        <ul class="dropdown-menu shadow-sm">
          <li><a class="dropdown-item active" href="#">All Status</a></li>
          <li><a class="dropdown-item" href="#">Confirmed</a></li>
          <li><a class="dropdown-item" href="#">Cancelled</a></li>
          <li><a class="dropdown-item" href="#">Completed</a></li>
        </ul>
      </div>

      <div class="dropdown filter-dd">
        <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          By Date
        </button>
        <ul class="dropdown-menu shadow-sm">
          <li><a class="dropdown-item active" href="#">Newest first</a></li>
          <li><a class="dropdown-item" href="#">Oldest first</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Tabs: same rail/pills style as auth modal -->
  <div class="auth-tab-rail bk-tab-rail mb-3">
    <ul class="nav nav-pills auth-tabs" id="bookingTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming-pane" type="button" role="tab">
          Upcoming (3)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="past-tab" data-bs-toggle="pill" data-bs-target="#past-pane" type="button" role="tab">
          Past (2)
        </button>
      </li>
    </ul>
  </div>

  <div class="tab-content">

    <!-- ========== UPCOMING ========== -->
    <div class="tab-pane fade show active" id="upcoming-pane" role="tabpanel" aria-labelledby="upcoming-tab">
      <div class="d-flex flex-column gap-3">

        <!-- Card A (confirmed) -->
        <div class="visit-item card booking-item">
          <div class="card-body">
            <span class="status-pill status-ok booking-status">confirmed</span>

            <div class="fw-semibold">FlyDreamAir Sydney Lounge</div>
            <div class="text-muted small">SYD</div>

            <div class="meta-list text-muted small mt-2">
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted" alt="">
                <span>Sunday, December 20, 2026</span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                <span>09:00 - 13:00</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted" alt="">
                <span>3 people</span>
              </div>
            </div>

            <div class="small fw-semibold mt-3">Membership Access</div>
            <div class="text-muted small">Booked on 12/3/2026</div>

            <!-- Kebab actions (bottom-right, plain) -->
            <div class="dropdown booking-actions">
              <button class="btn btn-kebab-plain" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More">
                <i class="fa-solid fa-ellipsis-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end action-menu shadow-sm">
                <li>
                    <a class="dropdown-item"
                        href="#"
                        data-bs-toggle="modal"
                        data-bs-target="#bookingDetailsModal"
                        data-bd-title="FlyDreamAir Sydney Lounge"
                        data-bd-airport="SYD"
                        data-bd-date="Sunday, December 20, 2026"
                        data-bd-time="09:00 - 13:00"
                        data-bd-people="3 people total"
                        data-bd-flight="FD123 at 14:30"
                        data-bd-status="confirmed"
                        data-bd-total="FREE">
                        View Details
                    </a>
                    </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#">Cancel Booking</a></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Card B (confirmed) -->
        <div class="visit-item card booking-item">
          <div class="card-body">
            <span class="status-pill status-ok booking-status">confirmed</span>

            <div class="fw-semibold">FlyDreamAir Premium Lounge</div>
            <div class="text-muted small">SIN</div>

            <div class="meta-list text-muted small mt-2">
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted" alt="">
                <span>Tuesday, December 15, 2026</span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                <span>14:00 - 18:00</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted" alt="">
                <span>2 people</span>
              </div>
            </div>

            <div class="small fw-semibold mt-3">Membership Access</div>
            <div class="text-muted small">Booked on 12/1/2026</div>

            <div class="dropdown booking-actions">
              <button class="btn btn-kebab-plain" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More">
                <i class="fa-solid fa-ellipsis-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end action-menu shadow-sm">
                <li><a class="dropdown-item" href="#">View Details</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#">Cancel Booking</a></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Card C (cancelled example) -->
        <div class="visit-item card booking-item">
          <div class="card-body">
            <span class="status-pill status-cancel booking-status">cancelled</span>

            <div class="fw-semibold">FlyDreamAir Dubai Lounge</div>
            <div class="text-muted small">DXB</div>

            <div class="meta-list text-muted small mt-2">
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted" alt="">
                <span>Friday, December 25, 2026</span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                <span>11:00 - 13:00</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted" alt="">
                <span>1 person</span>
              </div>
            </div>

            <div class="small fw-semibold mt-3">Membership Access</div>
            <div class="text-muted small">Booked on 12/5/2026</div>

            <div class="dropdown booking-actions">
              <button class="btn btn-kebab-plain" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More">
                <i class="fa-solid fa-ellipsis-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end action-menu shadow-sm">
                <li><a class="dropdown-item" href="#">View Details</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#">Cancel Booking</a></li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ========== PAST ========== -->
    <div class="tab-pane fade" id="past-pane" role="tabpanel" aria-labelledby="past-tab">
      <div class="d-flex flex-column gap-3">

        <!-- Past 1 (completed) -->
        <div class="visit-item card booking-item">
          <div class="card-body">
            <span class="status-pill status-done booking-status">completed</span>

            <div class="fw-semibold">FlyDreamAir Melbourne Lounge</div>
            <div class="text-muted small">MEL</div>

            <div class="meta-list text-muted small mt-2">
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted" alt="">
                <span>Monday, November 10, 2026</span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                <span>08:00 - 10:00</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted" alt="">
                <span>1 person</span>
              </div>
            </div>

            <div class="small fw-semibold mt-3">Membership Access</div>
            <div class="text-muted small">Booked on 11/1/2026</div>

            <div class="dropdown booking-actions">
              <button class="btn btn-kebab-plain" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More">
                <i class="fa-solid fa-ellipsis-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end action-menu shadow-sm">
                <li><a class="dropdown-item" href="#">View Details</a></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Past 2 (cancelled) -->
        <div class="visit-item card booking-item">
          <div class="card-body">
            <span class="status-pill status-cancel booking-status">cancelled</span>

            <div class="fw-semibold">FlyDreamAir Paris Lounge</div>
            <div class="text-muted small">CDG</div>

            <div class="meta-list text-muted small mt-2">
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/booking-icon.svg" class="inline-icon tint-muted" alt="">
                <span>Friday, October 30, 2026</span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/img/time-icon.svg" class="inline-icon tint-muted" alt="">
                <span>12:00 - 14:00</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <img src="assets/img/guest-icon.svg" class="inline-icon tint-muted" alt="">
                <span>2 people</span>
              </div>
            </div>

            <div class="small fw-semibold mt-3">Membership Access</div>
            <div class="text-muted small">Booked on 10/20/2026</div>

            <div class="dropdown booking-actions">
              <button class="btn btn-kebab-plain" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More">
                <i class="fa-solid fa-ellipsis-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end action-menu shadow-sm">
                <li><a class="dropdown-item" href="#">View Details</a></li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div><!-- /tab-content -->

</div>

<?php require __DIR__ . '/../partials/booking_details_modal.php'; ?>
