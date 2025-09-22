<?php
// public/install.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

$app = require __DIR__ . '/../config/app.php';
$host = $app['mysql_host'];
$port = (int)$app['mysql_port'];
$user = $app['mysql_user'];
$pass = $app['mysql_pass'];
$db   = $app['mysql_db'];

$rootDir    = realpath(__DIR__ . '/..');
$lockPath   = $rootDir . DIRECTORY_SEPARATOR . 'install.lock';
$schemaPath = __DIR__ . '/../config/schema_mysql.sql';

echo "=== INSTALL ===\n";

try {
  // 0) Блокируем повторную установку
  if (file_exists($lockPath)) {
    exit("Already installed. Lock: $lockPath\n");
  }

  // 1) Соединение без выбора БД
  $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);

  // 2) Создаём БД и выбираем её
  $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
  echo "CREATE DATABASE `$db` OK\n";
  $pdo->exec("USE `$db`;");
  echo "USE `$db` OK\n";

  // 3) Если есть schema_mysql.sql — выполняем (он должен содержать ТОЛЬКО таблицы/данные, без CREATE DATABASE/USE)
  if (is_file($schemaPath)) {
    $sql = file_get_contents($schemaPath);
    if ($sql === false) throw new RuntimeException("Cannot read $schemaPath");
    $pdo->exec($sql);
    echo "Schema applied from config/schema_mysql.sql\n";
  } else {
    echo "No schema file found, skipping.\n";
  }

  // 4) Гарантируем наличие хотя бы одной таблицы — чтобы БД точно была видна в phpMyAdmin
  $pdo->exec("CREATE TABLE IF NOT EXISTS __install_probe (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB;");
  echo "Probe table created.\n";

  // 5) Проверяем, что БД существует
  $exists = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=".$pdo->quote($db))->fetchColumn();
  if (!$exists) throw new RuntimeException("DB `$db` still not visible in INFORMATION_SCHEMA");

  // 6) Пишем install.lock
  if (file_put_contents($lockPath, date('c')) === false) {
    throw new RuntimeException("Cannot write install.lock at: $lockPath (check folder permissions)");
  }
  echo "install.lock written: $lockPath\n";

  // 7) Показываем идентичность сервера
  $id = $pdo->query("SELECT @@hostname AS host, @@port AS port, @@version AS version")->fetch();
  echo "Server: host={$id['host']} port={$id['port']} version={$id['version']}\n";

  echo "\nDONE. Open phpMyAdmin → обнови список БД. Должна появиться `$db`.\n";
} catch (Throwable $e) {
  http_response_code(500);
  echo "Install error: " . $e->getMessage() . "\n";
}
