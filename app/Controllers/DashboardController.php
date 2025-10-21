<?php
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/Membership.php';

class DashboardController extends BaseController {
  public function index() {
    if (!is_logged_in()) {
      header('Location: ?r=signin'); exit;
    }

    $user = current_user();
    $uid  = (int)$user['id'];

    // Fetch bookings
    $rows = Booking::listUserBookings($uid);

    // Partition into upcoming vs past
    $now = new DateTimeImmutable();
    $upcoming = [];
    $past = [];
    foreach ($rows as $r) {
      $endDt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $r['visit_date'].' '.$r['end_time']);
      if (!$endDt) { $past[] = $r; continue; }
      if ($r['status'] !== 'cancelled' && $endDt >= $now) $upcoming[] = $r; else $past[] = $r;
    }

    // Metrics
    $activeBookings = count($upcoming);
    $totalVisits    = 0;
    foreach ($rows as $r) {
      if (strtolower($r['status']) === 'completed') $totalVisits++;
    }

    // Plan
    $plan = Membership::userCurrent($uid)
      ?? ['slug'=>'basic','name'=>'Basic','monthly_fee_usd'=>0,'guest_allowance'=>0,'normal_access'=>'pay_per_use','premium_access'=>'pay_per_use'];
    $planSlug = strtolower($plan['slug'] ?? 'basic');

    $this->render('dashboard', 'Dashboard', 'main', [
      'user'     => $user,
      'metrics'  => [
        'active_bookings' => $activeBookings,
        'total_visits'    => $totalVisits,
      ],
      'plan'     => $plan,
      // show only the next 2 upcoming visits on the dashboard
      'upcoming' => array_slice($upcoming, 0, 2),
      'planSlug' => $planSlug,
    ]);
  }
}
