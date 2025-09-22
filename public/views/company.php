<?php
// public/views/company.php — карточная верстка без <table>

// $activeNav = 'company'; // (установи перед require header)

if (!isset($pdo, $currentUserId)) {
  echo '<div class="msg error">Нет контекста БД/пользователя</div>';
  return;
}

/** 1) Компания пользователя (макс. одна по DDL) */
$st = $pdo->prepare("
  SELECT c.id, c.name, cu.role
  FROM company_users cu
  JOIN companies c ON c.id = cu.company_id
  WHERE cu.user_id = ?
  LIMIT 1
");
$st->execute([(int)$currentUserId]);
$company = $st->fetch();                // array|false
$hasCompany = is_array($company);
$isAdmin   = $hasCompany && ($company['role'] === 'ADMIN');
$isManager = $hasCompany && ($company['role'] === 'MANAGER');

/** 2) Участники (если есть компания) */
$members = [];
if ($hasCompany) {
  $st = $pdo->prepare("
    SELECT u.id, u.name, u.email, cu.role
    FROM company_users cu
    JOIN users u ON u.id = cu.user_id
    WHERE cu.company_id = ?
    ORDER BY u.name
  ");
  $st->execute([(int)$company['id']]);
  $members = $st->fetchAll();
}

/** 3) Мои приглашения (если НЕ в компании) */
$invites = [];
if (!$hasCompany) {
  $myEmail = null;
  try {
    $st = $pdo->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
    $st->execute([(int)$currentUserId]);
    $me = $st->fetch();
    $myEmail = $me['email'] ?? null;
  } catch (Throwable $e) {
    $myEmail = null;
  }

  if ($myEmail) {
    $st = $pdo->prepare("
      SELECT i.id, i.company_id, c.name AS company_name, i.created_at
      FROM invitations i
      JOIN companies c ON c.id = i.company_id
      WHERE i.email = ? AND i.status = 'PENDING'
      ORDER BY i.created_at DESC
    ");
    $st->execute([$myEmail]);
    $invites = $st->fetchAll();
  }
}
?>

<main class="tt-main">
  <div class="tt-page tt-page--company">

    <section class="panel" aria-labelledby="company-title">
      <header class="panel__head">
        <div>
          <h2 id="company-title" class="panel__title">Компания</h2>
          <p class="panel__subtitle">Управление участниками и приглашениями</p>
        </div>
        <?php if ($hasCompany): ?>
          <span class="chip" aria-live="polite">Ваша роль: <?= htmlspecialchars($company['role']) ?></span>
        <?php endif; ?>
      </header>

      <?php if (!$hasCompany): ?>
        <div class="empty">
          <div class="empty__icon" aria-hidden="true">🏢</div>
          <div class="empty__text">Вы пока не состоите ни в одной компании.</div>
          <div class="empty__actions">
            <a class="btn" href="<?= BASE_URL ?>/?page=company_create">+ Создать компанию</a>
          </div>
        </div>

        <?php if (!empty($invites)): ?>
          <div class="stack">
            <h3 class="block-title">Мои приглашения</h3>
            <div class="cards">
              <?php foreach ($invites as $inv): ?>
                <article class="card invite">
                  <div class="invite__top">
                    <div class="invite__company"><?= htmlspecialchars($inv['company_name'] ?? '') ?></div>
                    <div class="invite__date"><?= htmlspecialchars((string)$inv['created_at']) ?></div>
                  </div>
                  <form method="post" action="<?= BASE_URL ?>/jwt_hook/company_accept_invite.php" class="inline-form">
                    <input type="hidden" name="invite_id" value="<?= (int)$inv['id'] ?>">
                    <button class="btn" type="submit">Принять</button>
                  </form>
                </article>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

      <?php else: ?>

        <!-- Шапка компании -->
        <div class="company-header">
          <div class="company-header__name"><?= htmlspecialchars($company['name']) ?></div>
          <div class="company-header__meta">
            <span class="chip chip--neutral">Участников: <?= count($members) ?></span>
            <span class="chip"><?= htmlspecialchars($company['role']) ?></span>
          </div>
        </div>

        <!-- Участники -->
        <div class="stack">
          <h3 class="block-title">Сотрудники</h3>

          <?php if (empty($members)): ?>
            <div class="empty empty--thin">
              <div class="empty__icon" aria-hidden="true">👥</div>
              <div class="empty__text">Сотрудников пока нет.</div>
            </div>
          <?php else: ?>
            <div class="cards">
  <?php foreach ($members as $m): ?>
    <article class="card member-card">
      <div class="card__top">
        <span class="member-card__avatar">
          <?= strtoupper(mb_substr($m['name'] ?: $m['email'], 0, 1, 'UTF-8')) ?>
        </span>
        <span class="chip"><?= htmlspecialchars($m['role'] ?? '') ?></span>
      </div>

      <h3 class="card__title">
        <?= htmlspecialchars($m['name'] ?: 'Без имени') ?>
      </h3>

      <p class="card__desc"><?= htmlspecialchars($m['email'] ?? '') ?></p>

      <?php if ($isAdmin): ?>
        <div class="card__actions">
          <form class="inline-form" method="post" action="<?= BASE_URL ?>/jwt_hook/company_role_update.php">
            <input type="hidden" name="company_id" value="<?= (int)$company['id'] ?>">
            <input type="hidden" name="user_id"    value="<?= (int)$m['id'] ?>">

            <div class="select-wrap">
              <label class="visually-hidden" for="role-<?= (int)$m['id'] ?>">Роль</label>
              <select id="role-<?= (int)$m['id'] ?>" name="role" class="select">
                <option value="ADMIN"   <?= ($m['role'] ?? '')==='ADMIN'?'selected':'' ?>>ADMIN</option>
                <option value="MANAGER" <?= ($m['role'] ?? '')==='MANAGER'?'selected':'' ?>>MANAGER</option>
                <option value="DEV"     <?= ($m['role'] ?? '')==='DEV'?'selected':'' ?>>DEV</option>
              </select>
              <button class="btn btn--secondary" type="submit">OK</button>
            </div>
          </form>
        </div>
      <?php endif; ?>
    </article>
  <?php endforeach; ?>
</div>


          <?php endif; ?>
        </div>

        <!-- Пригласить участника -->
        <?php if ($isAdmin): ?>
          <div class="stack">
            <h3 class="block-title">Пригласить участника</h3>
            <form method="post" action="<?= BASE_URL ?>/jwt_hook/company_invite.php" class="invite-form">
              <input type="hidden" name="company_id" value="<?= (int)$company['id'] ?>">

              <label class="field" for="invite-email">
                <span class="field__label">Email</span>
                <input id="invite-email" class="field__input" type="email" name="email" placeholder="user@example.com" required>
              </label>

              <div class="invite-form__actions">
                <button class="btn cmp-create-btn" type="submit">Пригласить</button>
              </div>
            </form>
          </div>
        <?php endif; ?>

      <?php endif; ?>
    </section>

  </div>
</main>
