<?php
require_once __DIR__ . '/../Models/User.php';

class AuthController extends BaseController {
  public function index() {
    $this->render('auth', 'Auth', 'bare');
  }

  public function signin() {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
      set_flash('error', 'Email and password are required.');
      header('Location: ' . base_href('welcome') . '#signin');
      exit;
    }

    $user = User::findByEmail($email);
    if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
      set_flash('error', 'Invalid credentials.');
      header('Location: ' . base_href('welcome') . '#signin');
      exit;
    }

    signin_user($user);
    set_flash('success', 'Welcome back, ' . htmlspecialchars($user['first_name']) . '!');
    header('Location: ' . base_href('dashboard'));
    exit;
  }

  public function signup() {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pw    = $_POST['password'] ?? '';
    $pw2   = $_POST['password_confirm'] ?? '';

    if ($first==='' || $last==='' || $email==='' || $pw==='') {
      set_flash('error', 'Please fill all required fields.');
      header('Location: ' . base_href('welcome') . '#signup');
      exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      set_flash('error', 'Please enter a valid email.');
      header('Location: ' . base_href('welcome') . '#signup');
      exit;
    }
    if ($pw !== $pw2) {
      set_flash('error', 'Passwords do not match.');
      header('Location: ' . base_href('welcome') . '#signup');
      exit;
    }
    if (User::findByEmail($email)) {
      set_flash('error', 'Email is already registered.');
      header('Location: ' . base_href('welcome') . '#signup');
      exit;
    }

    $hash = password_hash($pw, PASSWORD_BCRYPT);
    $userId = User::create([
      'first_name'    => $first,
      'last_name'     => $last,
      'email'         => $email,
      'password_hash' => $hash,
    ]);

    // Give Basic membership on signup
    User::giveBasicMembership($userId);

    // Auto sign-in
    $user = User::findByEmail($email);
    signin_user($user);

    set_flash('success', 'Account created. Welcome, ' . htmlspecialchars($first) . '!');
    header('Location: ' . base_href('dashboard'));
    exit;
  }

  public function signout() {
    signout_user();
    set_flash('success', 'You have been signed out.');
    header('Location: ' . base_href('welcome'));
    exit;
  }
}
