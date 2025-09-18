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
<body class="container">
<nav class="nav burger-nav left-minus">
  <!-- Ссылки на разделы -->
  <a class="tasks <?= $activePage==='tasks' ? 'is-active' : '' ?>"
     href="<?= BASE_URL ?>/?page=tasks">Задачи</a>
  <a class="tasks-create <?= $activePage==='task_create' ? 'is-active' : '' ?>"
         href="<?= BASE_URL ?>/?page=task_create">Создать Задачи</a>
  <a class="company <?= $activePage==='company' ? 'is-active' : '' ?>"
     href="<?= BASE_URL ?>/?page=company">Компания</a>
  
  <a class="company-create <?= $activePage==='company_create' ? 'is-active' : '' ?>"
     href="<?= BASE_URL ?>/?page=company_create">Создать Компанию</a>   
</nav>
<?php
  require __DIR__.'/includes/header.php';

  if (!empty($_GET['error']))   echo '<div class="msg error">'.htmlspecialchars($_GET['error']).'</div>';
  if (!empty($_GET['success'])) echo '<div class="msg ok">'.htmlspecialchars($_GET['success']).'</div>';

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
