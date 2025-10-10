
<?php
function db(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $envPath = __DIR__ . '/../.env';
  $env = file_exists($envPath) ? parse_ini_file($envPath, false, INI_SCANNER_TYPED) : [];

  $host = $env['DB_HOST'] ?? '127.0.0.1';
  $port = $env['DB_PORT'] ?? 3306;
  $db   = $env['DB_DATABASE'] ?? 'flydreamair';
  $user = $env['DB_USERNAME'] ?? 'root';
  $pass = $env['DB_PASSWORD'] ?? '';

  $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  return $pdo;
}
