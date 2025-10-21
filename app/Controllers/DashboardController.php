<?php
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/Membership.php';

class DashboardController extends BaseController
{
    public function index()
    {
        $isGuest = !is_logged_in();

        if ($isGuest) {
            $user = ['id' => 0, 'first_name' => 'Guest', 'last_name' => ''];
            $uid  = 0;

            $rows     = [];
            $upcoming = [];
            $past     = [];

            $activeBookings = 0;
            $totalVisits    = 0;

            $plan = [
                'slug'            => 'basic',
                'name'            => 'Basic',
                'monthly_fee_usd' => 0,
                'guest_allowance' => 0,
                'normal_access'   => 'pay_per_use',
                'premium_access'  => 'pay_per_use',
            ];
            $planSlug = 'basic';
        } else {
            $user = current_user();
            $uid  = (int) $user['id'];

            $rows = Booking::listUserBookings($uid);

            $now      = new DateTimeImmutable();
            $upcoming = [];
            $past     = [];

            foreach ($rows as $r) {
                $endDt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $r['visit_date'] . ' ' . $r['end_time']);
                if (!$endDt) {
                    $past[] = $r;
                    continue;
                }
                if ($r['status'] !== 'cancelled' && $endDt >= $now) {
                    $upcoming[] = $r;
                } else {
                    $past[] = $r;
                }
            }

            $activeBookings = count($upcoming);
            $totalVisits    = 0;

            foreach ($rows as $r) {
                if (strtolower($r['status']) === 'completed') {
                    $totalVisits++;
                }
            }

            $plan = Membership::userCurrent($uid)
                ?? [
                    'slug'            => 'basic',
                    'name'            => 'Basic',
                    'monthly_fee_usd' => 0,
                    'guest_allowance' => 0,
                    'normal_access'   => 'pay_per_use',
                    'premium_access'  => 'pay_per_use',
                ];

            $planSlug = strtolower($plan['slug'] ?? 'basic');
        }

        $this->render('dashboard', 'Dashboard', 'main', [
            'user'     => $user,
            'metrics'  => [
                'active_bookings' => $activeBookings,
                'total_visits'    => $totalVisits,
            ],
            'plan'     => $plan,
            'upcoming' => array_slice($upcoming, 0, 2),
            'planSlug' => $planSlug,
        ]);
    }
}
