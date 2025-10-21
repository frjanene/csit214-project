<?php
require_once __DIR__ . '/../Models/Membership.php';

class MembershipController extends BaseController
{
    public function index()
    {
        $plans    = Membership::allPlans();
        $user     = current_user();
        $current  = null;
        $currSlug = 'basic';

        if ($user) {
            $dbPlan = Membership::userCurrent((int) $user['id']);
            if ($dbPlan) {
                $current  = $dbPlan;
                $currSlug = strtolower($dbPlan['slug']);
            }

            $sessSlug = strtolower($_SESSION['user']['plan_slug'] ?? '');
            if ($sessSlug && isset($plans[$sessSlug])) {
                $current  = $plans[$sessSlug];
                $currSlug = $sessSlug;
            }
        }

        if (!$current && isset($plans['basic'])) {
            $current  = $plans['basic'];
            $currSlug = 'basic';
        }

        $this->render('memberships', 'Memberships', 'main', [
            'plans'    => $plans,
            'current'  => $current,
            'user'     => $user,
            'currSlug' => $currSlug,
        ]);
    }

    public function upgrade()
    {
        if (!is_logged_in()) {
            set_flash('error', 'Please sign up or sign in to upgrade your membership.');
            header('Location: ' . base_href('welcome'));
            exit;
        }

        $plan = trim($_POST['plan'] ?? '');
        $name = trim($_POST['card_name'] ?? '');
        $num  = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
        $exp  = trim($_POST['card_exp'] ?? '');
        $cvv  = trim($_POST['card_cvv'] ?? '');
        $addr = trim($_POST['billing_addr'] ?? '');

        $errors = [];
        if ($plan === '') { $errors[] = 'Missing plan.'; }
        if ($name === '') { $errors[] = 'Cardholder name required.'; }
        if (!preg_match('/^\d{12,19}$/', $num)) { $errors[] = 'Card number looks invalid.'; }
        if (!preg_match('/^\d{2}\/\d{2}$/', $exp)) { $errors[] = 'Expiry must be MM/YY.'; }
        if (!preg_match('/^\d{3,4}$/', $cvv)) { $errors[] = 'CVV must be 3–4 digits.'; }
        if ($addr === '') { $errors[] = 'Billing address required.'; }

        if ($errors) {
            set_flash('error', implode(' ', $errors));
            header('Location: ' . base_href('memberships'));
            exit;
        }

        try {
            $uid = (int) current_user()['id'];
            Membership::setUserPlan($uid, $plan);
            $_SESSION['user']['plan_slug'] = strtolower($plan);
            set_flash('success', 'Membership upgraded to ' . htmlspecialchars(strtoupper($plan)) . ' successfully.');
        } catch (\Throwable $e) {
            set_flash('error', 'Could not upgrade membership: ' . $e->getMessage());
        }

        header('Location: ' . base_href('memberships'));
        exit;
    }
}
