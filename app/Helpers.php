
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
