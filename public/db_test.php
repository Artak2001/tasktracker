<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

$dsn  = 'mysql:host=127.0.0.1;port=3306;dbname=tasktracker;charset=utf8mb4';
$user = 'root';
$pass = 'root';

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  echo "✅ OK: connected<br>";
  echo "DB time: " . $pdo->query('SELECT NOW()')->fetchColumn();
} catch (PDOException $e) {
  http_response_code(500);
  echo "❌ DB ERROR: " . htmlspecialchars($e->getMessage());
}
