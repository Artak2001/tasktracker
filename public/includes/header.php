<header class="header">
  <div class="brand">
    <div class="logo"></div>
    <a href="<?= BASE_URL ?>/">Task Tracker</a>
  </div>

  <nav class="nav">
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
  <div class="button-wrap">
    <div class="burger-wrapper">
      <div class="burger-line"></div>
      <div class="burger-line"></div>
      <div class="burger-line"></div>
    </div>
    <!-- Если пользователь не авторизован -->
    <?php if (empty($currentUserId)): ?>
      <div class="register-btn btn">Регистрация</div>
      <div class="sign-in-btn btn">Войти</div>
    <?php else: ?>
      <!-- Если авторизован -->
      <span class="username">User #<?= (int)$currentUserId ?></span>
      
      <a class="log-out" href="<?= BASE_URL ?>/../src/controller/logout.php">Выйти</a>
    <?php endif; ?>
  </div>
</header>
