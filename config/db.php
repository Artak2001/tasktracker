<?php
function get_pdo(): PDO {
  $app = require __DIR__ . '/app.php';
  $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $app['mysql_host'], $app['mysql_port'] ?? 3306, $app['mysql_db']
  );
  $pdo = new PDO($dsn, $app['mysql_user'], $app['mysql_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}
