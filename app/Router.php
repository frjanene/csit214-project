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
  'dashboard'   => 'DashboardController@index',
  'find'        => 'LoungeController@index',
  'bookings'    => 'BookingController@index',
  'memberships' => 'MembershipController@index',
  'profile'     => 'ProfileController@index',
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
