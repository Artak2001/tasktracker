<?php
// src/controller/task_reassign.php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/?page=tasks&error=Метод+не+поддерживается');
  exit;
}

$pdo = get_pdo();

// auth
$token   = $_COOKIE['tt_token'] ?? '';
$payload = $token ? jwt_verify($token, JWT_SECRET) : null;
$currentUserId = $payload['uid'] ?? null;
if (!$currentUserId) {
  header('Location: ' . BASE_URL . '/?error=Нужна+авторизация');
  exit;
}

$taskId      = (int)($_POST['task_id'] ?? 0);
$newAssignee = (int)($_POST['assignee_user_id'] ?? 0);

if ($taskId <= 0 || $newAssignee <= 0) {
  header('Location: ' . BASE_URL . '/?page=tasks&error=Некорректные+данные');
  exit;
}

try {
  // 1) Получаем задачу
  $st = $pdo->prepare("
    SELECT id, company_id, assigned_to_user_id
    FROM tasks WHERE id = ? LIMIT 1
  ");
  $st->execute([$taskId]);
  $task = $st->fetch();

  if (!$task) {
    header('Location: ' . BASE_URL . '/?page=tasks&error=Задача+не+найдена');
    exit;
  }
  if ($task['company_id'] === null) {
    header('Location: ' . BASE_URL . '/?page=tasks&error=Личные+задачи+не+переназначаются+здесь');
    exit;
  }
  $companyId = (int)$task['company_id'];

  // 2) Право: только ADMIN/MANAGER в этой компании
  $st = $pdo->prepare("
    SELECT role FROM company_users
    WHERE company_id = ? AND user_id = ? LIMIT 1
  ");
  $st->execute([$companyId, $currentUserId]);
  $me = $st->fetch();
  if (!$me || !in_array($me['role'], ['ADMIN','MANAGER'], true)) {
    header('Location: ' . BASE_URL . '/?page=tasks&error=Нет+прав+переназначать');
    exit;
  }

  // 3) Новый исполнитель должен быть участником этой компании
  $st = $pdo->prepare("
    SELECT 1 FROM company_users
    WHERE company_id = ? AND user_id = ? LIMIT 1
  ");
  $st->execute([$companyId, $newAssignee]);
  if (!$st->fetch()) {
    header('Location: ' . BASE_URL . '/?page=tasks&error=Пользователь+не+в+компании');
    exit;
  }

  // 4) Обновляем и логируем
  $st = $pdo->prepare("
    UPDATE tasks SET assigned_to_user_id = :assignee, updated_at = NOW()
    WHERE id = :tid
  ");
  $st->execute([':assignee' => $newAssignee, ':tid' => $taskId]);

  $st = $pdo->prepare("
    INSERT INTO task_logs (task_id, action, old_value, new_value, performed_by, created_at)
    VALUES (:tid, 'assignee_change', :old, :new, :uid, NOW())
  ");
  $st->execute([
    ':tid' => $taskId,
    ':old' => (string)$task['assigned_to_user_id'],
    ':new' => (string)$newAssignee,
    ':uid' => (int)$currentUserId,
  ]);

  header('Location: ' . BASE_URL . '/?page=tasks&success=Исполнитель+обновлён');
  exit;

} catch (Throwable $e) {
  header('Location: ' . BASE_URL . '/?page=tasks&error=' . urlencode('Ошибка: '.$e->getMessage()));
  exit;
}