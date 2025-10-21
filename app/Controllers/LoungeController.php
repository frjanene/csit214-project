<?php
require_once __DIR__ . '/../Models/Lounge.php';

class LoungeController extends BaseController
{
    public function index()
    {
        $q       = trim($_GET['q'] ?? '');
        $country = trim($_GET['country'] ?? '');
        $amen    = $_GET['amen'] ?? [];
        if (!is_array($amen)) {
            $amen = [];
        }

        $amenities = Lounge::allAmenities();
        $countries = Lounge::countriesWithLounges();

        $lounges = Lounge::search($q, $country, $amen, 50, 0);

        $this->render('find_lounges', 'Find Lounges', 'main', [
            'lounges'   => $lounges,
            'amenities' => $amenities,
            'countries' => $countries,
            'filters'   => ['q' => $q, 'country' => $country, 'amen' => $amen],
        ]);
    }
}
