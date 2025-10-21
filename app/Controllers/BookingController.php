<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/Membership.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

class BookingController extends BaseController
{
    public function index()
    {
        if (!is_logged_in()) {
            header('Location: ?r=signin');
            exit;
        }

        $uid  = (int) current_user()['id'];
        $rows = Booking::listUserBookings($uid);

        $now = new DateTimeImmutable();
        $upcoming = [];
        $past = [];

        foreach ($rows as $r) {
            $endDt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $r['visit_date'] . ' ' . $r['end_time']);
            if (!$endDt) {
                $past[] = $r;
                continue;
            }
            if ($endDt >= $now && strtolower($r['status']) !== 'cancelled') {
                $upcoming[] = $r;
            } else {
                $past[] = $r;
            }
        }

        $data = [
            'upcoming'       => $upcoming,
            'past'           => $past,
            'count_upcoming' => count($upcoming),
            'count_past'     => count($past),
        ];

        $this->render('bookings', 'My Bookings', 'main', $data);
    }

    public function cancel()
    {
        header('Content-Type: application/json');

        if (!is_logged_in()) {
            echo json_encode(['ok' => false, 'error' => 'Sign in required']);
            return;
        }

        $uid = (int) current_user()['id'];
        $id  = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Invalid booking id']);
            return;
        }

        $ok = Booking::cancelBooking($id, $uid);
        echo json_encode(['ok' => $ok, 'status' => $ok ? 'cancelled' : null]);
    }

    public function qr()
    {
        header('Location: https://www.google.com', true, 302);
        exit;
    }

    public function qrImg()
    {
        $code = trim($_GET['code'] ?? '');
        $size = max(120, min(1024, (int) ($_GET['s'] ?? 300)));

        $deeplink = base_href('qr') . '&code=' . urlencode($code ?: 'demo');

        $qr = QrCode::create($deeplink)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize($size)
            ->setMargin(12)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $writer = new PngWriter();
        $png = $writer->write($qr)->getString();

        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=3600, immutable');
        header('Content-Disposition: inline; filename="booking-qr-' . ($code ?: 'demo') . '.png"');
        echo $png;
        exit;
    }

    public function flightLookup()
    {
        header('Content-Type: application/json');

        $flight = strtoupper(trim($_POST['flight'] ?? ''));
        $date   = trim($_POST['date'] ?? '');

        if (!preg_match('/^[A-Z]{2,3}\d{1,4}$/', $flight)) {
            echo json_encode(['ok' => false, 'error' => 'Invalid flight format (e.g., FD123).']);
            return;
        }

        preg_match('/^([A-Z]{2,3})(\d{1,4})$/', $flight, $m);
        [$all, $ac, $fn] = $m;

        if ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $row = Booking::findFlight($ac, $fn, $date);
        } else {
            $row = Booking::findNearestFlight($ac, $fn);
        }

        if (!$row) {
            echo json_encode(['ok' => false, 'error' => 'Flight not found.']);
            return;
        }

        echo json_encode([
            'ok'          => true,
            'airline'     => $ac,
            'number'      => $fn,
            'equipment'   => $row['equipment'],
            'status'      => $row['status'],
            'flight_date' => $row['flight_date'],
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
        ]);
    }

    public function quote()
    {
        header('Content-Type: application/json');

        if (!is_logged_in()) {
            echo json_encode(['ok' => false, 'error' => 'Sign in required']);
            return;
        }

        $uid   = (int) current_user()['id'];
        $lid   = (int) ($_POST['lounge_id'] ?? 0);
        $date  = trim($_POST['visit_date'] ?? '');
        $start = trim($_POST['start_time'] ?? '');
        $end   = trim($_POST['end_time'] ?? '');
        $ppl   = max(1, (int) ($_POST['people'] ?? 1));

        if (
            !$lid ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ||
            !preg_match('/^\d{2}:\d{2}$/', $start) ||
            !preg_match('/^\d{2}:\d{2}$/', $end)
        ) {
            echo json_encode(['ok' => false, 'error' => 'Missing/invalid fields']);
            return;
        }

        $lounge = Booking::loungeById($lid);
        if (!$lounge) {
            echo json_encode(['ok' => false, 'error' => 'Lounge not found']);
            return;
        }

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

    public function store()
    {
        header('Content-Type: application/json');

        if (!is_logged_in()) {
            echo json_encode(['ok' => false, 'error' => 'Sign in required']);
            return;
        }

        $uid    = (int) current_user()['id'];
        $user   = current_user();
        $lid    = (int) ($_POST['lounge_id'] ?? 0);
        $date   = trim($_POST['visit_date'] ?? '');
        $start  = trim($_POST['start_time'] ?? '');
        $end    = trim($_POST['end_time'] ?? '');
        $ppl    = max(1, (int) ($_POST['people'] ?? 1));
        $flight = strtoupper(trim($_POST['flight'] ?? ''));

        if (
            !$lid ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ||
            !preg_match('/^\d{2}:\d{2}$/', $start) ||
            !preg_match('/^\d{2}:\d{2}$/', $end)
        ) {
            echo json_encode(['ok' => false, 'error' => 'Missing/invalid fields']);
            return;
        }

        $lounge = Booking::loungeById($lid);
        if (!$lounge) {
            echo json_encode(['ok' => false, 'error' => 'Lounge not found']);
            return;
        }

        $q = Booking::quotePrice($uid, $lounge, $ppl);

        try {
            $bid = Booking::createBooking([
                'user_id'        => $uid,
                'guest_name'     => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
                'guest_email'    => $user['email'] ?? null,
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

            $qrUrl = base_href('qr') . '&code=' . urlencode($b['qr_code']);
            $qrImg = base_href('qr_img') . '&code=' . urlencode($b['qr_code']) . '&s=300';

            echo json_encode([
                'ok'      => true,
                'booking' => [
                    'id'      => $bid,
                    'title'   => $b['lounge_name'],
                    'airport' => $b['airport_name'] . ' (' . $b['iata'] . ')',
                    'date'    => $b['visit_date'],
                    'start'   => substr($b['start_time'], 0, 5),
                    'end'     => substr($b['end_time'], 0, 5),
                    'people'  => $b['people_count'],
                    'method'  => $b['method'],
                    'total'   => (float) $b['total_usd'],
                    'qr_url'  => $qrUrl,
                    'qr_img'  => $qrImg,
                    'contact' => [
                        'name'    => $b['contact_name'],
                        'email'   => $b['contact_email'],
                        'user_id' => (int) $b['user_id'],
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    public function slots()
    {
        header('Content-Type: application/json');

        $lid  = (int) ($_POST['lounge_id'] ?? 0);
        $date = trim($_POST['date'] ?? '');

        if ($lid <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            echo json_encode(['ok' => false, 'error' => 'Invalid lounge or date']);
            return;
        }

        $slots = Booking::slotsForDate($lid, $date);
        if ($slots === null) {
            echo json_encode(['ok' => false, 'error' => 'Lounge not found']);
            return;
        }

        echo json_encode([
            'ok'    => true,
            'slots' => $slots['rows'],
            'open'  => $slots['open_time'],
            'close' => $slots['close_time'],
            'cap'   => $slots['capacity'],
        ]);
    }
}
