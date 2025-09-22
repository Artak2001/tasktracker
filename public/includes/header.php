<?php
/** Общий header (RU)
 * Ожидает:
 *   $activeNav ∈ {'tasks','task_create','company','company_create','auth'(опц.)}
 *   $currentUserId (truthy/falsey) — гость/вошёл
 *   $currentUserName (опц.) — имя для показа
 */
if (!isset($activeNav)) { $activeNav = ''; }
$isAuth = !empty($currentUserId);
?>
<header class="tt-header" data-tt-header>
  <div class="tt-container">
    <a class="tt-logo">TaskTracker</a>

    

    <div class="tt-user">
      <nav class="tt-nav" aria-label="Основная навигация">
      <button class="tt-burger" aria-label="Открыть меню" aria-expanded="false" aria-controls="tt-nav-menu" data-tt-burger>
        <span class="tt-burger__bar"></span>
        <span class="tt-burger__bar"></span>
        <span class="tt-burger__bar"></span>
      </button>

      <ul id="tt-nav-menu" class="tt-nav__list" data-tt-menu>
        <li class="tt-nav__item">
          <a class="tt-nav__link <?= $activeNav==='tasks' ? 'tt-nav__link--active' : '' ?>" href="<?= BASE_URL ?>/?page=tasks">Задачи</a>
        </li>
        <li class="tt-nav__item">
          <a class="tt-nav__link <?= $activeNav==='task_create' ? 'tt-nav__link--active' : '' ?>" href="<?= BASE_URL ?>/?page=task_create">Создать задачу</a>
        </li>
        <li class="tt-nav__item">
          <a class="tt-nav__link <?= $activeNav==='company' ? 'tt-nav__link--active' : '' ?>" href="<?= BASE_URL ?>/?page=company">Компания</a>
        </li>
        <li class="tt-nav__item">
          <a class="tt-nav__link <?= $activeNav==='company_create' ? 'tt-nav__link--active' : '' ?>" href="<?= BASE_URL ?>/?page=company_create">Создать компанию</a>
        </li>
      </ul>
    </nav>
    <button class="tt-btn tt-btn--ghost" data-theme-toggle>🌙/☀️</button>
      <?php if (!$isAuth): ?>
        <a class="tt-btn tt-btn--ghost" href="<?= BASE_URL ?>/?page=auth#login">Войти</a>
        <a class="tt-btn tt-btn--primary" href="<?= BASE_URL ?>/?page=auth#register">Регистрация</a>
      <?php else: ?>
        <div class="tt-user__box">
          <span class="tt-user__avatar" aria-hidden="true"><?= strtoupper(substr(($currentUserName ?? 'U'),0,1)) ?></span>
          <span class="tt-user__name"><?= htmlspecialchars($currentUserName ?? ('User #'.(int)$currentUserId)) ?></span>
          <a class="tt-btn tt-btn--ghost" href="<?= BASE_URL ?>/../src/controller/logout.php">Выйти</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</header>
