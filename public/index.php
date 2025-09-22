<?php
require __DIR__.'/../config/constants.php';
require __DIR__.'/../config/db.php';
require __DIR__.'/../src/controller/jwt.php';

$pdo = get_pdo();

// JWT → текущий пользователь
$token   = $_COOKIE['tt_token'] ?? '';
$payload = $token ? jwt_verify($token, JWT_SECRET) : null;
$currentUserId = $payload['uid'] ?? null;

// страница из URL (белый список)
$allowed = ['home','tasks','task_create','company','company_create'];
$page = $_GET['page'] ?? 'home';
if (!in_array($page, $allowed, true)) $page = 'home';

// для подсветки активного пункта в header.php
$activePage = $page === 'home' ? 'tasks' : $page;
?><!DOCTYPE html>
<html lang="ru">
<?php
  $pageTitle = 'Task Tracker';
  require __DIR__.'/includes/head.php';
?>
<body class="container theme-dark">

<?php
  require __DIR__.'/includes/header.php';

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
?>
  <div class="tt-container">
    <?php
    if (!empty($_GET['error']))   echo '<div class="msg error">'.htmlspecialchars($_GET['error']).'</div>';
    if (!empty($_GET['success'])) echo '<div class="msg ok">'.htmlspecialchars($_GET['success']).'</div>';
  ?>
    </div>
 
  <?php
  // если гость — показываем auth вне зависимости от ?page=
  if (!$currentUserId) {
    require __DIR__.'/views/auth.php';
  } else {
    switch ($page) {
      case 'home':
      case 'tasks':
        require __DIR__.'/views/tasks.php';
        break;

      case 'task_create':
        require __DIR__.'/views/task_create.php';
        break;

      case 'company':
        require __DIR__.'/views/company.php';
        break;

      case 'company_create':
        require __DIR__.'/views/company_create.php';
        break;
    }
  }
?>
</body>
</html>
