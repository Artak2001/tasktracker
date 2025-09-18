<?php
header('Content-Type: text/plain; charset=utf-8');
$app = require __DIR__ . '/../config/app.php';

try {
  $pdo = new PDO(
    "mysql:host={$app['mysql_host']};port={$app['mysql_port']};charset=utf8mb4",
    $app['mysql_user'],
    $app['mysql_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );

  echo "Server identity:\n";
  $row = $pdo->query("SELECT @@hostname AS host, @@port AS port, @@socket AS socket, @@version AS version")->fetch();
  print_r($row);

  echo "\nDatabases like 'tasktracker':\n";
  $r = $pdo->query("SHOW DATABASES LIKE 'tasktracker'")->fetch();
  var_dump($r);

  echo "\nCurrent user:\n";
  $u = $pdo->query("SELECT CURRENT_USER() AS user, USER() AS authenticated_as")->fetch();
  print_r($u);

} catch (Throwable $e) {
  http_response_code(500);
  echo "Diag error: " . $e->getMessage();
}
