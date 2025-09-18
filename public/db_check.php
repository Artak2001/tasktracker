<?php
require __DIR__ . '/../config/db.php';

try {
  $pdo = get_pdo();
  $row = $pdo->query("SELECT DATABASE() AS db, VERSION() AS v")->fetch();
  echo "OK: connected to {$row['db']} (MySQL {$row['v']})";
} catch (Throwable $e) {
  http_response_code(500);
  echo "DB ERROR: " . $e->getMessage();
}