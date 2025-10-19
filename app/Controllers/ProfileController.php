<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Membership.php';

class ProfileController extends BaseController {
  public function index() {
    if (!is_logged_in()) {
      set_flash('error', 'Please sign in to view your profile.');
      header('Location: ' . base_href('welcome'));
      exit;
    }

    $user = User::findById((int)current_user()['id']);
    if (!$user) {
      set_flash('error', 'User not found.');
      header('Location: ' . base_href('welcome'));
      exit;
    }

    // Membership badge (dynamic)
    $plan = Membership::userCurrent((int)$user['id']);
    $planSlug = $plan['slug'] ?? 'basic';
    $planName = $plan['name'] ?? 'Basic';

    // Preferences (ensure row exists)
    $prefs = User::getOrCreatePreferences((int)$user['id']);

    $this->render('profile', 'Profile', 'main', [
      'user'     => $user,
      'planSlug' => $planSlug,
      'planName' => $planName,
      'prefs'    => $prefs,
    ]);
  }

  /** POST /profile_update */
  public function update() {
    if (!is_logged_in()) {
      set_flash('error', 'Please sign in to update your profile.');
      header('Location: ' . base_href('welcome'));
      exit;
    }

    $uid = (int)current_user()['id'];

    // Read from scoped array profile[...]
    $in = $_POST['profile'] ?? [];
    $data = [
      'first_name' => trim($in['first_name'] ?? ''),
      'last_name'  => trim($in['last_name']  ?? ''),
      'email'      => trim($in['email']      ?? ''),
      'phone'      => trim($in['phone']      ?? ''),
      'dob'        => trim($in['dob']        ?? ''), // mm/dd/yyyy or yyyy-mm-dd
      'city'       => trim($in['city']       ?? ''),
      'country'    => trim($in['country']    ?? ''),
      'address'    => trim($in['address']    ?? ''),
    ];

    // Basic validation
    $errors = [];
    if ($data['first_name'] === '' || $data['last_name'] === '') $errors[] = 'First and last name are required.';
    if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';

    // Email uniqueness (excluding current user)
    if (User::emailExists($data['email'], $uid)) $errors[] = 'Email is already in use by another account.';

    // Normalize DOB to yyyy-mm-dd if provided (allow mm/dd/yyyy)
    if ($data['dob'] !== '') {
      if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['dob'])) {
        [$m,$d,$y] = explode('/', $data['dob']);
        $data['dob'] = sprintf('%04d-%02d-%02d', $y, $m, $d);
      }
      if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['dob'])) {
        $errors[] = 'Date of birth must be YYYY-MM-DD or MM/DD/YYYY.';
      }
    } else {
      $data['dob'] = null;
    }

    if ($errors) {
      set_flash('error', implode(' ', $errors));
      header('Location: ' . base_href('profile'));
      exit;
    }

    try {
      User::updateProfile($uid, $data);

      // Keep session minisync for header etc.
      $_SESSION['user']['first_name'] = $data['first_name'];
      $_SESSION['user']['last_name']  = $data['last_name'];
      $_SESSION['user']['email']      = $data['email'];

      set_flash('success', 'Profile updated successfully.');
    } catch (\Throwable $e) {
      set_flash('error', 'Could not update profile: ' . $e->getMessage());
    }

    header('Location: ' . base_href('profile'));
    exit;
  }

  /** POST /profile_password */
  public function password() {
    if (!is_logged_in()) {
      set_flash('error', 'Please sign in to change your password.');
      header('Location: ' . base_href('welcome'));
      exit;
    }

    $uid = (int)current_user()['id'];
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $errors = [];
    if ($new === '' || $confirm === '' || $current === '') $errors[] = 'All password fields are required.';
    if ($new !== $confirm) $errors[] = 'New passwords do not match.';
    if (strlen($new) < 8) $errors[] = 'New password must be at least 8 characters.';

    // Verify current
    $row = User::findAuthById($uid);
    if (!$row || empty($row['password_hash']) || !password_verify($current, $row['password_hash'])) {
      $errors[] = 'Current password is incorrect.';
    }

    if ($errors) {
      set_flash('error', implode(' ', $errors));
      header('Location: ' . base_href('profile') . '#pane-security');
      exit;
    }

    try {
      $hash = password_hash($new, PASSWORD_BCRYPT);
      User::updatePassword($uid, $hash);
      set_flash('success', 'Password updated successfully.');
    } catch (\Throwable $e) {
      set_flash('error', 'Could not update password: ' . $e->getMessage());
    }

    header('Location: ' . base_href('profile') . '#pane-security');
    exit;
  }

  /** POST /profile_prefs */
  public function preferences() {
    if (!is_logged_in()) {
      set_flash('error', 'Please sign in to update preferences.');
      header('Location: ' . base_href('welcome'));
      exit;
    }

    $uid = (int)current_user()['id'];

    // Load existing (ensures row exists)
    $existing = User::getOrCreatePreferences($uid);

    // Forms post to same route:
    // - Notifications form => $_POST['notify'][...]
    // - Preferences form   => $_POST['prefs']['language'|'currency']
    $notifyIn = $_POST['notify'] ?? [];
    $prefsIn  = $_POST['prefs']  ?? [];

    // Language/Currency: keep old if not posted
    $language = $prefsIn['language'] ?? ($existing['language'] ?? 'en');
    $currency = $prefsIn['currency'] ?? ($existing['currency'] ?? 'USD');

    // Switches: thanks to hidden 0 fields, keys will exist with "0" or "1" if that form submitted.
    // If the notifications form wasn't used, keep existing DB values.
    $nb  = array_key_exists('notif_booking', $notifyIn) ? (int)$notifyIn['notif_booking'] : (int)$existing['notif_booking'];
    $na  = array_key_exists('notif_account', $notifyIn) ? (int)$notifyIn['notif_account'] : (int)$existing['notif_account'];
    $np  = array_key_exists('notif_promos',  $notifyIn) ? (int)$notifyIn['notif_promos']  : (int)$existing['notif_promos'];
    $ns  = array_key_exists('notif_sms',     $notifyIn) ? (int)$notifyIn['notif_sms']     : (int)$existing['notif_sms'];
    $npu = array_key_exists('notif_push',    $notifyIn) ? (int)$notifyIn['notif_push']    : (int)$existing['notif_push'];
    $wd  = array_key_exists('weekly_digest', $notifyIn) ? (int)$notifyIn['weekly_digest'] : (int)$existing['weekly_digest'];

    $prefs = [
      'language'      => $language,
      'currency'      => $currency,
      'notif_booking' => $nb,
      'notif_account' => $na,
      'notif_promos'  => $np,
      'notif_sms'     => $ns,
      'notif_push'    => $npu,
      'weekly_digest' => $wd,
    ];

    try {
      User::savePreferences($uid, $prefs);
      set_flash('success', 'Preferences saved.');
    } catch (\Throwable $e) {
      set_flash('error', 'Could not save preferences: ' . $e->getMessage());
    }

    // Stay on the tab that submitted:
    $anchor = !empty($prefsIn) ? '#pane-preferences' : '#pane-notifications';
    header('Location: ' . base_href('profile') . $anchor);
    exit;
  }
}
