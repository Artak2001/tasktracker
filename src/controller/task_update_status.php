<?php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/?page=tasks&error=Метод+не+поддерживается');
  exit;
}

$pdo = get_pdo();

// Авторизация
$token   = $_COOKIE['tt_token'] ?? '';
$payload = $token ? jwt_verify($token, JWT_SECRET) : null;
$currentUserId = $payload['uid'] ?? null;
if (!$currentUserId) {
  header('Location: ' . BASE_URL . '/?error=Нужна+авторизация');
  exit;
}

// Данные формы
$taskId = (int)($_POST['task_id'] ?? 0);
$newStatus = $_POST['status'] ?? '';

$allowed = ['new','in_progress','done'];
if (!$taskId || !in_array($newStatus, $allowed, true)) {
  header('Location: ' . BASE_URL . '/?page=tasks&error=Некорректные+данные');
  exit;
}

try {
  // 1) Тянем задачу
  $st = $pdo->prepare("
    SELECT t.id, t.status, t.company_id, t.created_by_user_id, t.assigned_to_user_id
    FROM tasks t
    WHERE t.id = ?
    LIMIT 1
  ");
  $st->execute([$taskId]);
  $task = $st->fetch();
  if (!$task) {
    header('Location: ' . BASE_URL . '/?page=tasks&error=Задача+не+найдена');
    exit;
  }

  // 2) Проверка прав
  $allowedToChange = false;

  if ($task['company_id'] === null) {
    // Личная задача — менять может владелец (и при желании можно разрешить исполнителю)
    if ((int)$task['created_by_user_id'] === (int)$currentUserId) {
      $allowedToChange = true;
    }
  } else {
    // Корпоративная — ТОЛЬКО ADMIN/MANAGER компании
    $st = $pdo->prepare("
      SELECT role
      FROM company_users
      WHERE company_id = ? AND user_id = ?
      LIMIT 1
    ");
    $st->execute([(int)$task['company_id'], (int)$currentUserId]);
    $row = $st->fetch();
    if ($row && in_array($row['role'], ['ADMIN','MANAGER'], true)) {
      $allowedToChange = true;
    }
  }

  if (!$allowedToChange) {
    header('Location: ' . BASE_URL . '/?page=tasks&error=Нет+прав+изменять+эту+задачу');
    exit;
  }

  if ($task['status'] === $newStatus) {
    header('Location: ' . BASE_URL . '/?page=tasks&success=Статус+без+изменений');
    exit;
  }

  // 3) Обновляем
  $st = $pdo->prepare("UPDATE tasks SET status = :s, updated_at = NOW() WHERE id = :id");
  $st->execute([':s' => $newStatus, ':id' => (int)$taskId]);

  // 4) Лог
  $st = $pdo->prepare("
    INSERT INTO task_logs (task_id, action, old_value, new_value, performed_by, created_at)
    VALUES (:tid, 'status_change', :old, :new, :uid, NOW())
  ");
  $st->execute([
    ':tid' => (int)$taskId,
    ':old' => $task['status'],
    ':new' => $newStatus,
    ':uid' => (int)$currentUserId,
  ]);

  header('Location: ' . BASE_URL . '/?page=tasks&success=Статус+обновлён');
  exit;

} catch (Throwable $e) {
  header('Location: ' . BASE_URL . '/?page=tasks&error=' . urlencode('Ошибка: '.$e->getMessage()));
  exit;
}
