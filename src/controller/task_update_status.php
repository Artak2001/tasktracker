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

$cid = (int)($_POST['company_id'] ?? 0);           // может быть 0 для "личных" вне компании, если такие есть
$tid = (int)($_POST['task_id'] ?? 0);
$newStatus = $_POST['status'] ?? '';
$redirect = $cid ? (BASE_URL . '/?page=tasks') : (BASE_URL . '/?page=my');

$allowedStatuses = ['new','in_progress','done'];
if (!$tid || !in_array($newStatus, $allowedStatuses, true)) {
  header('Location: ' . $redirect . '&error=' . urlencode('Некорректные данные')); exit;
}

try {
  $pdo->beginTransaction();

  // Подтянем задачу и залочим её
  $sql = "SELECT id, company_id, assigned_to_user_id, status FROM tasks WHERE id=? FOR UPDATE";
  $st = $pdo->prepare($sql);
  $st->execute([$tid]);
  $task = $st->fetch();
  if (!$task) {
    $pdo->rollBack();
    header('Location: ' . $redirect . '&error=' . urlencode('Задача не найдена')); exit;
  }

  $taskCompanyId = (int)($task['company_id'] ?? 0);
  $assigneeId    = (int)($task['assigned_to_user_id'] ?? 0);
  $oldStatus     = $task['status'];

  // Определяем права:
  $canChange = false;
  if ($taskCompanyId) {
    // Если задача «компании», то ADMIN/MANAGER компании могут менять статус любой задачи
    $r = $pdo->prepare("SELECT role FROM company_users WHERE company_id=? AND user_id=? LIMIT 1");
    $r->execute([$taskCompanyId, $currentUserId]);
    $role = $r->fetchColumn();

    if (in_array($role, ['ADMIN','MANAGER'], true)) {
      $canChange = true;
    }
  }
  // Исполнитель может менять статус своей задачи (и в компании, и личной)
  if ($assigneeId === $currentUserId) {
    $canChange = true;
  }

  if (!$canChange) {
    $pdo->rollBack();
    header('Location: ' . $redirect . '&error=' . urlencode('Нет прав менять статус')); exit;
  }

  // Обновим статус
  $u = $pdo->prepare("UPDATE tasks SET status=?, updated_at=NOW() WHERE id=?");
  $u->execute([$newStatus, $tid]);

  // Лог (опционально)
  $log = $pdo->prepare("INSERT INTO task_logs (task_id, action, old_value, new_value, performed_by) VALUES (?,?,?,?,?)");
  $log->execute([$tid, 'status', $oldStatus, $newStatus, $currentUserId]);

  $pdo->commit();
  header('Location: ' . $redirect . '&success=' . urlencode('Статус обновлён'));
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  header('Location: ' . $redirect . '&error=' . urlencode($e->getMessage()));
}
