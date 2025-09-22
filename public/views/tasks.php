<?php
// public/views/tasks.php

if (!isset($pdo, $currentUserId)) {
  echo '<div class="msg error">Нет контекста БД/пользователя</div>';
  return;
}

// $activeNav = 'tasks'; // если используешь подсветку меню

// 1) Роли пользователя по компаниям
$st = $pdo->prepare("SELECT company_id, role FROM company_users WHERE user_id = ?");
$st->execute([(int)$currentUserId]);
$myRoles = [];
foreach ($st->fetchAll() as $r) {
  $myRoles[(int)$r['company_id']] = $r['role'];
}
$canManage = function (?int $companyId) use ($myRoles): bool {
  return $companyId && isset($myRoles[$companyId]) && in_array($myRoles[$companyId], ['ADMIN','MANAGER'], true);
};

// 2) Личные задачи = все задачи, где я исполнитель (и из компаний, и без компании)
try {
  $st = $pdo->prepare("
      SELECT t.id, t.title, t.description, t.status, t.created_at,
      t.company_id, c.name AS company_name
      FROM tasks t
      LEFT JOIN companies c ON c.id = t.company_id
      WHERE t.assigned_to_user_id = :uid
      ORDER BY t.id DESC
  ");
  $st->execute([':uid' => (int)$currentUserId]);
  $personal = $st->fetchAll();
} catch (Throwable $e) {
  echo '<div class="msg error">Ошибка личных задач: ' . htmlspecialchars($e->getMessage()) . '</div>';
  $personal = [];
}

// 3) Корпоративные задачи = все задачи компаний, где я состою (видны даже если не я исполнитель)
try {
  $st = $pdo->prepare("
    SELECT t.id, t.title, t.description, t.status, t.created_at,
           t.company_id, c.name AS company_name,
           au.name AS assignee_name, au.email AS assignee_email, au.id AS assignee_id
    FROM tasks t
    JOIN companies c ON c.id = t.company_id
    LEFT JOIN users au ON au.id = t.assigned_to_user_id
    WHERE t.company_id IN (SELECT cu.company_id FROM company_users cu WHERE cu.user_id = :uid)
    ORDER BY t.id DESC
  ");
  $st->execute([':uid' => (int)$currentUserId]);
  $corp = $st->fetchAll();
} catch (Throwable $e) {
  echo '<div class="msg error">Ошибка корпоративных задач: ' . htmlspecialchars($e->getMessage()) . '</div>';
  $corp = [];
}

// 4) Справочник участников по компаниям для селекта «Исполнитель»
$companyMembers = []; // [company_id => [ {id,name,email}, ... ]]
if (!empty($corp)) {
  $companyIds = array_values(array_unique(array_map(fn($t)=> (int)$t['company_id'], $corp)));
  if ($companyIds) {
    $in = implode(',', array_fill(0, count($companyIds), '?'));
    $sql = "
      SELECT cu.company_id, u.id, u.name, u.email
      FROM company_users cu
      JOIN users u ON u.id = cu.user_id
      WHERE cu.company_id IN ($in)
      ORDER BY u.name
    ";
    $st = $pdo->prepare($sql);
    $st->execute($companyIds);
    foreach ($st->fetchAll() as $row) {
      $cid = (int)$row['company_id'];
      $companyMembers[$cid][] = [
        'id'    => (int)$row['id'],
        'name'  => (string)$row['name'],
        'email' => (string)$row['email'],
      ];
    }
  }
}

// 5) Хелперы для статуса (чтобы не рвать верстку в class="")
function badgeLabel(string $status): string {
  return $status === 'in_progress' ? 'В работе' : ($status === 'done' ? 'Выполнена' : 'Новая');
}
function badgeClass(string $status): string {
  return $status === 'in_progress' ? 'status-in-work' : ($status === 'done' ? 'status-complete' : 'status-new');
}
?>

<main class="tt-main">
  <div class="tt-page tt-page--tasks">

    <!-- Личные задачи -->
    <section class="panel" aria-labelledby="personal-tasks-title">
      <header class="panel__head">
        <div>
          <h2 id="personal-tasks-title" class="panel__title">Личные задачи</h2>
          <p class="panel__subtitle">Все задачи, назначенные на вас</p>
        </div>
        <a class="btn" href="<?= BASE_URL ?>/?page=task_create" aria-label="Создать задачу">+ Создать задачу</a>
      </header>

      <?php if (empty($personal)): ?>
        <div class="empty" role="status" aria-live="polite">
          <div class="empty__icon" aria-hidden="true">📝</div>
          <div class="empty__text">Пока нет личных задач.</div>
        </div>
      <?php else: ?>
        <div class="cards">
          <?php foreach ($personal as $t): ?>
            <article class="card task" aria-labelledby="task-title-<?= (int)$t['id'] ?>">
              <div class="task__top">
                <span class="task__id">#<?= (int)$t['id'] ?></span>
                <span class="badge <?= badgeClass($t['status']) ?>"><?= htmlspecialchars(badgeLabel($t['status'])) ?></span>
              </div>

              <h3 id="task-title-<?= (int)$t['id'] ?>" class="task__title"><?= htmlspecialchars($t['title'] ?? '') ?></h3>

              <?php if (!empty($t['description'])): ?>
                <p class="task__desc"><?= nl2br(htmlspecialchars($t['description'])) ?></p>
              <?php endif; ?>

              <div class="task__meta">
                <?php if (!empty($t['company_name'])): ?>
                  <span class="meta__item">Компания: <b><?= htmlspecialchars($t['company_name']) ?></b></span>
                <?php else: ?>
                  <span class="meta__item">Тип: Личная</span>
                <?php endif; ?>
                <span class="meta__item">Создано: <?= htmlspecialchars((string)$t['created_at']) ?></span>
              </div>

              <div class="task__actions">
                <form method="post" action="<?= BASE_URL ?>/jwt_hook/task_update_status.php" class="inline-form">
                  <input type="hidden" name="company_id" value="<?= (int)($t['company_id'] ?? 0) ?>">
                  <input type="hidden" name="task_id" value="<?= (int)$t['id'] ?>">
                  <label class="visually-hidden" for="status-p<?= (int)$t['id'] ?>">Статус</label>
                  <div class="select-wrap">
                    <select id="status-p<?= (int)$t['id'] ?>" name="status" class="select">
                      <option value="new"         <?= $t['status']==='new'?'selected':'' ?>>Новая</option>
                      <option value="in_progress" <?= $t['status']==='in_progress'?'selected':'' ?>>В работе</option>
                      <option value="done"        <?= $t['status']==='done'?'selected':'' ?>>Выполнена</option>
                    </select>
                    <button class="btn btn--secondary" type="submit">OK</button>
                  </div>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <!-- Корпоративные задачи -->
    <section class="panel" aria-labelledby="corp-tasks-title">
      <header class="panel__head">
        <div>
          <h2 id="corp-tasks-title" class="panel__title">Задачи компании</h2>
          <p class="panel__subtitle">Задачи во всех компаниях, где вы состоите</p>
        </div>
      </header>

      <?php if (empty($corp)): ?>
        <div class="empty" role="status" aria-live="polite">
          <div class="empty__icon" aria-hidden="true">🏢</div>
          <div class="empty__text">Пока нет корпоративных задач.</div>
        </div>
      <?php else: ?>
        <div class="cards">
          <?php foreach ($corp as $t): ?>
            <?php
              $cid = (int)$t['company_id'];
              $iCanManage = $canManage($cid);
              $assigneeLabel = $t['assignee_name'] ?: ($t['assignee_email'] ?: '—');
              $members = $companyMembers[$cid] ?? [];
            ?>
            <article class="card task" aria-labelledby="task-title-<?= (int)$t['id'] ?>">
              <div class="task__top">
                <span class="task__id">#<?= (int)$t['id'] ?></span>
                <span class="badge <?= badgeClass($t['status']) ?>"><?= htmlspecialchars(badgeLabel($t['status'])) ?></span>
              </div>

              <h3 id="task-title-<?= (int)$t['id'] ?>" class="task__title"><?= htmlspecialchars($t['title'] ?? '') ?></h3>

              <?php if (!empty($t['description'])): ?>
                <p class="task__desc"><?= nl2br(htmlspecialchars($t['description'])) ?></p>
              <?php endif; ?>

              <div class="task__meta">
                <span class="meta__item">Компания: <b><?= htmlspecialchars($t['company_name'] ?? '') ?></b></span>
                <span class="meta__item">Исполнитель: <b><?= htmlspecialchars($assigneeLabel) ?></b></span>
                <span class="meta__item">Создано: <?= htmlspecialchars((string)$t['created_at']) ?></span>
              </div>

              <?php if ($iCanManage || (int)($t['assignee_id'] ?? 0) === (int)$currentUserId): ?>
                <div class="task__actions task__actions--grid">
                  <!-- смена статуса -->
                  <form method="post" action="<?= BASE_URL ?>/jwt_hook/task_update_status.php" class="inline-form">
                    <input type="hidden" name="company_id" value="<?= $cid ?>">
                    <input type="hidden" name="task_id" value="<?= (int)$t['id'] ?>">
                    <label class="label" for="status-c<?= (int)$t['id'] ?>">Статус</label>
                    <div class="select-wrap">
                      <select id="status-c<?= (int)$t['id'] ?>" name="status" class="select">
                        <option value="new"         <?= $t['status']==='new'?'selected':'' ?>>Новая</option>
                        <option value="in_progress" <?= $t['status']==='in_progress'?'selected':'' ?>>В работе</option>
                        <option value="done"        <?= $t['status']==='done'?'selected':'' ?>>Выполнена</option>
                      </select>
                      <button class="btn btn--secondary" type="submit">Сохранить</button>
                    </div>
                  </form>

                  <!-- переназначение исполнителя (только ADMIN/MANAGER) -->
                  <?php if ($iCanManage): ?>
                    <form method="post" action="<?= BASE_URL ?>/jwt_hook/task_assign.php" class="inline-form">
                      <input type="hidden" name="company_id" value="<?= $cid ?>">
                      <input type="hidden" name="task_id" value="<?= (int)$t['id'] ?>">
                      <label class="label" for="assignee-<?= (int)$t['id'] ?>">Исполнитель</label>
                      <div class="select-wrap">
                        <select id="assignee-<?= (int)$t['id'] ?>" name="assignee_user_id" class="select" required>
                          <?php foreach ($members as $m): ?>
                            <?php
                              $label = $m['name'] ?: $m['email'];
                              $sel = ((int)($t['assignee_id'] ?? 0) === (int)$m['id']) ? 'selected' : '';
                            ?>
                            <option value="<?= (int)$m['id'] ?>" <?= $sel ?>><?= htmlspecialchars($label) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <button class="btn btn--secondary" type="submit">Назначить</button>
                      </div>
                    </form>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="task__note" role="note">Изменение доступно исполнителю или ADMIN/MANAGER.</div>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

  </div>
</main>
