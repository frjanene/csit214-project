<?php
session_start();
require_once __DIR__ . '/Helpers.php';

// BaseController
require_once __DIR__ . '/Controllers/BaseController.php';

// Route map
$r = $_GET['r'] ?? 'welcome';
$routes = [
  'welcome'     => 'WelcomeController@index',
  'auth'        => 'AuthController@index',
  'signin'      => 'AuthController@signin',
  'signup'      => 'AuthController@signup',
  'signout'     => 'AuthController@signout',
  'dashboard'   => 'DashboardController@index',
  'find'        => 'LoungeController@index',
  'bookings'    => 'BookingController@index',
  'flight_lookup'   => 'BookingController@flightLookup',
  'booking_quote'   => 'BookingController@quote',
  'booking_store'   => 'BookingController@store',
  'qr'              => 'BookingController@qr',     
  'memberships' => 'MembershipController@index',
  'membership_upgrade' => 'MembershipController@upgrade',
  'profile'     => 'ProfileController@index',
  'profile_update'   => 'ProfileController@update',
  'profile_password' => 'ProfileController@password',
  'profile_prefs'    => 'ProfileController@preferences',
  'forgot'      => 'PasswordController@request',
  'forgot_done' => 'PasswordController@sent',
];

// Resolver
if (!isset($routes[$r])) {
  http_response_code(404);
  echo "Page not found";
  exit;
}
list($controller, $method) = explode('@', $routes[$r]);

// Load requested controller
require_once __DIR__ . "/Controllers/{$controller}.php";
$instance = new $controller();
$instance->$method();
