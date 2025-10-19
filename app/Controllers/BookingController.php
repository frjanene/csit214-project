<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/Membership.php';

class BookingController extends BaseController {
  public function index() {
    $this->render('bookings', 'My Bookings');
  }

  /** GET /?r=qr&code=... -> always redirect to google.com (demo) */
  public function qr() {
    header('Location: https://www.google.com', true, 302);
    exit;
  }

  /** POST /?r=flight_lookup  {flight: 'FD123', date:'YYYY-MM-DD'} */
  public function flightLookup() {
    header('Content-Type: application/json');

    $flight = strtoupper(trim($_POST['flight'] ?? ''));
    $date   = trim($_POST['date'] ?? ''); // optional now

    // flight format required; date is optional
    if (!preg_match('/^[A-Z]{2,3}\d{1,4}$/', $flight)) {
      echo json_encode(['ok'=>false, 'error'=>'Invalid flight format (e.g., FD123).']);
      return;
    }

    // split airline + number
    preg_match('/^([A-Z]{2,3})(\d{1,4})$/', $flight, $m);
    [$all, $ac, $fn] = $m;

    // if a valid date was provided, search that specific day; otherwise pick nearest flight (future first)
    if ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
      $row = Booking::findFlight($ac, $fn, $date);
    } else {
      $row = Booking::findNearestFlight($ac, $fn); // see model addition below
    }

    if (!$row) {
      echo json_encode(['ok'=>false, 'error'=>'Flight not found.']);
      return;
    }

    // Always return flight_date so UI can auto-fill the Date input
    $payload = [
      'ok'          => true,
      'airline'     => $ac,
      'number'      => $fn,
      'equipment'   => $row['equipment'],
      'status'      => $row['status'],
      'flight_date' => $row['flight_date'], // <-- add this
      'dep'         => [
        'airport_iata' => $row['dep_iata'],
        'airport_name' => $row['dep_name'],
        'terminal'     => $row['dep_terminal'],
        'gate'         => $row['dep_gate'],
        'sched'        => $row['sched_dep'],
      ],
      'arr'         => [
        'airport_iata' => $row['arr_iata'],
        'airport_name' => $row['arr_name'],
        'terminal'     => $row['arr_terminal'],
        'gate'         => $row['arr_gate'],
        'sched'        => $row['sched_arr'],
      ],
    ];
    echo json_encode($payload);
  }

  /** POST /?r=booking_quote */
  public function quote() {
    header('Content-Type: application/json');
    if (!is_logged_in()) { echo json_encode(['ok'=>false,'error'=>'Sign in required']); return; }

    $uid   = (int)current_user()['id'];
    $lid   = (int)($_POST['lounge_id'] ?? 0);
    $date  = trim($_POST['visit_date'] ?? '');
    $start = trim($_POST['start_time'] ?? '');
    $end   = trim($_POST['end_time'] ?? '');
    $ppl   = max(1, (int)($_POST['people'] ?? 1));

    if (!$lid || !preg_match('/^\d{4}-\d{2}-\d{2}$/',$date) || !preg_match('/^\d{2}:\d{2}$/',$start) || !preg_match('/^\d{2}:\d{2}$/',$end)) {
      echo json_encode(['ok'=>false,'error'=>'Missing/invalid fields']); return;
    }

    $lounge = Booking::loungeById($lid);
    if (!$lounge) { echo json_encode(['ok'=>false,'error'=>'Lounge not found']); return; }

    $q = Booking::quotePrice($uid, $lounge, $ppl);
    echo json_encode([
      'ok'            => true,
      'method'        => $q['method'],
      'unit'          => $q['unit'],
      'total'         => $q['total'],
      'plan'          => $q['plan'],
      'needs_payment' => $q['total'] > 0,
    ]);
  }

  /** POST /?r=booking_store  (creates booking; payment assumed demo-paid if needed) */
  public function store() {
    header('Content-Type: application/json');
    if (!is_logged_in()) { echo json_encode(['ok'=>false,'error'=>'Sign in required']); return; }

    $uid   = (int)current_user()['id'];
    $user  = current_user(); // <-- add
    $lid   = (int)($_POST['lounge_id'] ?? 0);
    $date  = trim($_POST['visit_date'] ?? '');
    $start = trim($_POST['start_time'] ?? '');
    $end   = trim($_POST['end_time'] ?? '');
    $ppl   = max(1, (int)($_POST['people'] ?? 1));
    $flight= strtoupper(trim($_POST['flight'] ?? ''));

    if (!$lid || !preg_match('/^\d{4}-\d{2}-\d{2}$/',$date) || !preg_match('/^\d{2}:\d{2}$/',$start) || !preg_match('/^\d{2}:\d{2}$/',$end)) {
      echo json_encode(['ok'=>false,'error'=>'Missing/invalid fields']); return;
    }

    $lounge = Booking::loungeById($lid);
    if (!$lounge) { echo json_encode(['ok'=>false,'error'=>'Lounge not found']); return; }

    $q = Booking::quotePrice($uid, $lounge, $ppl);

    try {
      $bid = Booking::createBooking([
        'user_id'        => $uid,
        'guest_name'     => trim(($user['first_name'] ?? '').' '.($user['last_name'] ?? '')), // <-- NEW
        'guest_email'    => $user['email'] ?? null,                                           // <-- NEW
        'lounge_id'      => $lid,
        'flight_number'  => $flight,
        'visit_date'     => $date,
        'start_time'     => $start,
        'end_time'       => $end,
        'people_count'   => $ppl,
        'method'         => $q['method'],
        'unit_price_usd' => $q['unit'],
        'total_usd'      => $q['total'],
      ]);

      $b = Booking::getBookingSummary($bid);
      $qrUrl = base_href('qr') . '&code=' . urlencode($b['qr_code']); // demo

      echo json_encode([
        'ok'      => true,
        'booking' => [
          'id'        => $bid,
          'title'     => $b['lounge_name'],
          'airport'   => $b['airport_name'] . ' (' . $b['iata'] . ')',
          'date'      => $b['visit_date'],
          'start'     => substr($b['start_time'],0,5),
          'end'       => substr($b['end_time'],0,5),
          'people'    => $b['people_count'],
          'method'    => $b['method'],
          'total'     => (float)$b['total_usd'],
          'qr_url'    => $qrUrl,
          // contact echo-back (proves “not guest”)
          'contact'   => [
            'name'  => $b['contact_name'],
            'email' => $b['contact_email'],
            'user_id' => (int)$b['user_id'],
          ],
        ]
      ]);
    } catch (\Throwable $e) {
      echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
  }

}
