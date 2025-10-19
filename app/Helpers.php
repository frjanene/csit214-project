
<?php
function view($template, $data = []) {
  extract($data);
  // $layout: 'main' (with header) or 'bare' (no header)
  $layout = $layout ?? 'main';
  require __DIR__ . "/Views/layouts/{$layout}.php";
}

function base_href($route = '') {
  $base = '';
  if (!empty($_SERVER['HTTP_HOST'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base = $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
  }
  return $base . '/' . ($route ? ('?r=' . $route) : '');
}

function set_flash(string $type, string $msg): void {
  $_SESSION['flash'][$type] = $msg;
}
function get_flash(?string $type=null): ?array {
  if (!isset($_SESSION['flash'])) return null;
  $all = $_SESSION['flash'];
  unset($_SESSION['flash']);
  if ($type) return $all[$type] ?? null;
  return $all;
}

function current_user(): ?array {
  return $_SESSION['user'] ?? null;
}
function signin_user(array $user): void {
  $_SESSION['user'] = [
    'id' => $user['id'],
    'first_name' => $user['first_name'],
    'last_name'  => $user['last_name'],
    'email'      => $user['email'],
    'role'       => $user['role'],
  ];
}
function signout_user(): void { unset($_SESSION['user']); }
function is_logged_in(): bool { return (bool) current_user(); }
function initials_from(string $first, string $last): string {
  return strtoupper(mb_substr($first,0,1) . mb_substr($last,0,1));
}