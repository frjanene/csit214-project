<?php
$user     = $user     ?? null;
$prefs    = $prefs    ?? [];
$planName = $planName ?? 'Basic';
$planSlug = $planSlug ?? 'basic';

if (!$user) {
  echo '<div class="alert alert-light border">Please sign in.</div>';
  return;
}

$initials = initials_from($user['first_name'], $user['last_name']);
$dobValue = $user['dob'] ?? '';
?>
<div class="container py-4">

  <!-- Page Title -->
  <div class="d-flex align-items-start justify-content-between mb-3">
    <div>
      <h2 class="fw-bold mb-1">Profile Settings</h2>
      <div class="text-muted">Manage your account settings and preferences</div>
    </div>
    <span class="member-badge"><?= strtoupper($planSlug) ?> MEMBER</span>
  </div>

  <!-- Tabs Rail -->
  <div class="auth-tab-rail profile-tab-rail mb-3">
    <ul class="nav nav-pills auth-tabs tabs-4" id="profileTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-profile" data-bs-toggle="pill" data-bs-target="#pane-profile" type="button" role="tab">Profile</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-security" data-bs-toggle="pill" data-bs-target="#pane-security" type="button" role="tab">Security</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-notifications" data-bs-toggle="pill" data-bs-target="#pane-notifications" type="button" role="tab">Notifications</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-preferences" data-bs-toggle="pill" data-bs-target="#pane-preferences" type="button" role="tab">Preferences</button>
      </li>
    </ul>
  </div>

  <div class="tab-content" id="profileTabsContent">

    <!-- ========== PROFILE ========== -->
    <div class="tab-pane fade show active" id="pane-profile" role="tabpanel" aria-labelledby="tab-profile">
      <div class="card panel-card mb-3">
        <div class="card-body profile-card-body">

          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-semibold mb-0">
              <i class="fa-regular fa-user me-2"></i>Personal Information
            </h5>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-dark btn-sm rounded-3 profile-edit-btn">
                <i class="fa-regular fa-pen-to-square me-1"></i> Edit Profile
              </button>
              <button form="profileForm" type="submit" class="btn btn-primary btn-sm rounded-3 d-none profile-save-btn">
                <i class="fa-regular fa-floppy-disk me-1"></i> Save
              </button>
              <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 d-none profile-cancel-btn">
                Cancel
              </button>
            </div>
          </div>

          <!-- Scoped: profile[...] -->
          <form id="profileForm" method="post" action="<?= base_href('profile_update') ?>">
            <div class="row g-3">
              <div class="col-12">
                <span class="avatar-lg-soft"><?= htmlspecialchars($initials) ?></span>
              </div>

              <div class="col-md-6">
                <label class="form-label small">First Name</label>
                <input name="profile[first_name]" type="text" class="form-control profile-input" value="<?= htmlspecialchars($user['first_name']) ?>" disabled data-scope="profile">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Last Name</label>
                <input name="profile[last_name]" type="text" class="form-control profile-input" value="<?= htmlspecialchars($user['last_name']) ?>" disabled data-scope="profile">
              </div>

              <div class="col-md-6">
                <label class="form-label small">Email Address</label>
                <input name="profile[email]" type="email" class="form-control profile-input" value="<?= htmlspecialchars($user['email']) ?>" disabled data-scope="profile">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Phone Number</label>
                <input name="profile[phone]" type="text" class="form-control profile-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" disabled data-scope="profile">
              </div>

              <div class="col-md-6">
                <label class="form-label small">Date of Birth</label>
                <input name="profile[dob]" type="text" class="form-control profile-input" placeholder="YYYY-MM-DD or MM/DD/YYYY" value="<?= htmlspecialchars($dobValue) ?>" disabled data-scope="profile">
              </div>
              <div class="col-md-6">
                <label class="form-label small">City</label>
                <input name="profile[city]" type="text" class="form-control profile-input" value="<?= htmlspecialchars($user['city'] ?? '') ?>" disabled data-scope="profile">
              </div>

              <div class="col-md-6">
                <label class="form-label small">Country</label>
                <input name="profile[country]" type="text" class="form-control profile-input" value="<?= htmlspecialchars($user['country'] ?? '') ?>" disabled data-scope="profile">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Address</label>
                <input name="profile[address]" type="text" class="form-control profile-input" value="<?= htmlspecialchars($user['address'] ?? '') ?>" disabled data-scope="profile">
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card panel-card">
        <div class="card-body">
          <h5 class="fw-semibold mb-3">
            <i class="fa-regular fa-id-badge me-2"></i>Membership Information
          </h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small">Membership Tier</label>
              <div class="fw-semibold text-uppercase text-primary"><?= htmlspecialchars($planName) ?></div>
            </div>
            <div class="col-md-6">
              <label class="form-label small">Membership Number</label>
              <div class="fw-semibold"><?= 'U' . (int)$user['id'] ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========== SECURITY ========== -->
    <div class="tab-pane fade" id="pane-security" role="tabpanel" aria-labelledby="tab-security">
      <div class="card panel-card security-card">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-shield-halved"></i>
            <h5 class="fw-semibold mb-0">Change Password</h5>
          </div>

          <form method="post" action="<?= base_href('profile_password') ?>">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label sec-label">Current Password</label>
                <div class="fda-input-wrap">
                  <input name="current_password" type="password" class="form-control sec-input" data-password>
                  <button type="button" class="toggle-pass" aria-label="Show/Hide password">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label sec-label">New Password</label>
                <div class="fda-input-wrap">
                  <input name="new_password" type="password" class="form-control sec-input" data-password>
                  <button type="button" class="toggle-pass" aria-label="Show/Hide password">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label sec-label">Confirm New Password</label>
                <div class="fda-input-wrap">
                  <input name="confirm_password" type="password" class="form-control sec-input" data-password>
                  <button type="button" class="toggle-pass" aria-label="Show/Hide password">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-3">
              <button class="btn btn-fda-primary btn-fda-fit sec-submit">
                <i class="fa-solid fa-lock me-1"></i> Update Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ========== NOTIFICATIONS ========== -->
    <div class="tab-pane fade" id="pane-notifications" role="tabpanel" aria-labelledby="tab-notifications">
      <div class="card panel-card notify-card">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-regular fa-bell"></i>
            <h5 class="fw-semibold mb-0">Notification Preferences</h5>
          </div>

          <!-- Scoped: notify[...]  -->
          <!-- Hidden 0 fields ensure unchecked boxes post a 0 -->
          <form method="post" action="<?= base_href('profile_prefs') ?>">
            <ul class="list-group list-group-flush notify-list">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">Booking Reminders</div>
                  <small class="text-muted">Get notified about upcoming lounge visits</small>
                </div>
                <div class="form-check form-switch">
                  <input type="hidden" name="notify[notif_booking]" value="0">
                  <input class="form-check-input" type="checkbox" name="notify[notif_booking]" value="1" <?= !empty($prefs['notif_booking']) ? 'checked' : '' ?>>
                </div>
              </li>

              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">Account Updates</div>
                  <small class="text-muted">Important updates about your account</small>
                </div>
                <div class="form-check form-switch">
                  <input type="hidden" name="notify[notif_account]" value="0">
                  <input class="form-check-input" type="checkbox" name="notify[notif_account]" value="1" <?= !empty($prefs['notif_account']) ? 'checked' : '' ?>>
                </div>
              </li>

              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">Promotional Emails</div>
                  <small class="text-muted">Special offers and promotions</small>
                </div>
                <div class="form-check form-switch">
                  <input type="hidden" name="notify[notif_promos]" value="0">
                  <input class="form-check-input" type="checkbox" name="notify[notif_promos]" value="1" <?= !empty($prefs['notif_promos']) ? 'checked' : '' ?>>
                </div>
              </li>

              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">SMS Notifications</div>
                  <small class="text-muted">Receive important updates via SMS</small>
                </div>
                <div class="form-check form-switch">
                  <input type="hidden" name="notify[notif_sms]" value="0">
                  <input class="form-check-input" type="checkbox" name="notify[notif_sms]" value="1" <?= !empty($prefs['notif_sms']) ? 'checked' : '' ?>>
                </div>
              </li>

              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">Push Notifications</div>
                  <small class="text-muted">Browser and mobile push notifications</small>
                </div>
                <div class="form-check form-switch">
                  <input type="hidden" name="notify[notif_push]" value="0">
                  <input class="form-check-input" type="checkbox" name="notify[notif_push]" value="1" <?= !empty($prefs['notif_push']) ? 'checked' : '' ?>>
                </div>
              </li>

              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">Weekly Digest</div>
                  <small class="text-muted">Weekly summary of your activity</small>
                </div>
                <div class="form-check form-switch">
                  <input type="hidden" name="notify[weekly_digest]" value="0">
                  <input class="form-check-input" type="checkbox" name="notify[weekly_digest]" value="1" <?= !empty($prefs['weekly_digest']) ? 'checked' : '' ?>>
                </div>
              </li>
            </ul>

            <div class="mt-3">
              <button class="btn btn-dark rounded-3 pref-save-btn">
                <i class="fa-regular fa-floppy-disk me-1"></i> Save Notification Settings
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ========== PREFERENCES ========== -->
    <div class="tab-pane fade" id="pane-preferences" role="tabpanel" aria-labelledby="tab-preferences">
      <div class="card panel-card mb-3 pref-card">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-gear"></i>
            <h5 class="fw-semibold mb-0">Application Preferences</h5>
          </div>

          <!-- Scoped: prefs[...] -->
          <form method="post" action="<?= base_href('profile_prefs') ?>">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small">Preferred Language</label>
                <select class="form-select pref-select" name="prefs[language]">
                  <?php
                    $langs = ['en'=>'English','fr'=>'French','es'=>'Spanish'];
                    $cur = $prefs['language'] ?? 'en';
                    foreach ($langs as $code=>$label) {
                      $sel = $code === $cur ? 'selected' : '';
                      echo "<option value=\"".htmlspecialchars($code)."\" $sel>".htmlspecialchars($label)."</option>";
                    }
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Preferred Currency</label>
                <select class="form-select pref-select" name="prefs[currency]">
                  <?php
                    $currs = [
                      'USD' => 'USD - US Dollar',
                      'EUR' => 'EUR - Euro',
                      'NGN' => 'NGN - Naira',
                      'AUD' => 'AUD - Australian Dollar',
                      'SGD' => 'SGD - Singapore Dollar',
                    ];
                    $ccur = $prefs['currency'] ?? 'USD';
                    foreach ($currs as $code=>$label) {
                      $sel = $code === $ccur ? 'selected' : '';
                      echo "<option value=\"".htmlspecialchars($code)."\" $sel>".htmlspecialchars($label)."</option>";
                    }
                  ?>
                </select>
              </div>
            </div>

            <div class="mt-3">
              <button class="btn btn-dark rounded-3 pref-save-btn">
                <i class="fa-regular fa-floppy-disk me-1"></i> Save Preferences
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="card panel-card border-danger-subtle danger-card">
        <div class="card-body">
          <h5 class="fw-semibold text-danger mb-2">Danger Zone</h5>
          <div class="alert alert-light border mb-3 d-flex align-items-center gap-2 pref-danger-note">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div class="small">These actions are permanent and cannot be undone.</div>
          </div>
          <button class="btn btn-outline-danger rounded-3 pref-danger-btn" disabled title="Demo only">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> Deactivate Account
          </button>
        </div>
      </div>
    </div>

  </div><!-- /tab-content -->
</div>
