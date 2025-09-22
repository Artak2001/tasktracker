<?php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/?error=' . urlencode('Метод не поддерживается')); exit;
}

$pdo = get_pdo();
$token   = $_COOKIE['tt_token'] ?? '';
$payload = $token ? jwt_verify($token, JWT_SECRET) : null;
$currentUserId = isset($payload['uid']) ? (int)$payload['uid'] : 0;
if (!$currentUserId) {
  header('Location: ' . BASE_URL . '/?error=' . urlencode('Нужна авторизация')); exit;
}

$cid = (int)($_POST['company_id'] ?? 0);
$tid = (int)($_POST['task_id'] ?? 0);
$newAssignee = (int)($_POST['assignee_user_id'] ?? 0);
$redirect = BASE_URL . '/?page=tasks';

if (!$cid || !$tid || !$newAssignee) {
  header('Location: ' . $redirect . '&error=' . urlencode('Нет данных')); exit;
}

try {
  $pdo->beginTransaction();

  // Роль текущего пользователя в компании
  $st = $pdo->prepare("SELECT role FROM company_users WHERE company_id=? AND user_id=? LIMIT 1");
  $st->execute([$cid, $currentUserId]);
  $myRole = $st->fetchColumn();
  if (!in_array($myRole, ['ADMIN','MANAGER'], true)) {
    $pdo->rollBack();
    header('Location: ' . $redirect . '&error=' . urlencode('Только ADMIN/MANAGER могут назначать')); exit;
  }

  // Новый исполнитель должен быть участником компании
  $st = $pdo->prepare("SELECT 1 FROM company_users WHERE company_id=? AND user_id=? LIMIT 1");
  $st->execute([$cid, $newAssignee]);
  if (!$st->fetchColumn()) {
    $pdo->rollBack();
    header('Location: ' . $redirect . '&error=' . urlencode('Исполнитель не состоит в компании')); exit;
  }

  // Задача должна принадлежать компании; залочим строку
  $st = $pdo->prepare("SELECT assigned_to_user_id FROM tasks WHERE id=? AND company_id=? FOR UPDATE");
  $st->execute([$tid, $cid]);
  $task = $st->fetch();
  if (!$task) {
    $pdo->rollBack();
    header('Location: ' . $redirect . '&error=' . urlencode('Задача не найдена в компании')); exit;
  }
  $oldAssignee = (int)($task['assigned_to_user_id'] ?? 0);

  // Обновим исполнителя
  $u = $pdo->prepare("UPDATE tasks SET assigned_to_user_id=?, updated_at=NOW() WHERE id=? AND company_id=?");
  $u->execute([$newAssignee, $tid, $cid]);

  // Лог (опционально)
  $log = $pdo->prepare("INSERT INTO task_logs (task_id, action, old_value, new_value, performed_by) VALUES (?,?,?,?,?)");
  $log->execute([$tid, 'assign', (string)$oldAssignee, (string)$newAssignee, $currentUserId]);

  $pdo->commit();
  header('Location: ' . $redirect . '&success=' . urlencode('Исполнитель назначен'));
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  header('Location: ' . $redirect . '&error=' . urlencode($e->getMessage()));
}
