<?php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/?page=tasks&error=Метод+не+поддерживается');
    exit;
}

$pdo = get_pdo();

// авторизация
$token   = $_COOKIE['tt_token'] ?? '';
$payload = $token ? jwt_verify($token, JWT_SECRET) : null;
$currentUserId = $payload['uid'] ?? null;

if (!$currentUserId) {
    header('Location: ' . BASE_URL . '/?error=Нужна+авторизация');
    exit;
}

// данные формы
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? 'new';

if ($title === '') {
    header('Location: ' . BASE_URL . '/?page=task_create&error=Заголовок+обязателен');
    exit;
}
if (!in_array($status, ['new','in_progress','done'], true)) {
    $status = 'new';
}

// определяем компанию пользователя
$st = $pdo->prepare("
  SELECT company_id FROM company_users
  WHERE user_id = ?
  LIMIT 1
");
$st->execute([(int)$currentUserId]);
$row = $st->fetch();
$companyId = $row ? (int)$row['company_id'] : null;

try {
    $sql = "
        INSERT INTO tasks
          (company_id, title, description, status, assigned_to_user_id, created_by_user_id, created_at)
        VALUES
          (:company_id, :title, :description, :status, :assignee, :creator, NOW())
    ";

    $st = $pdo->prepare($sql);
    $st->execute([
        ':company_id'  => $companyId, // ← вот тут разница!
        ':title'       => $title,
        ':description' => ($description !== '' ? $description : null),
        ':status'      => $status,
        ':assignee'    => (int)$currentUserId,
        ':creator'     => (int)$currentUserId,
    ]);

    header('Location: ' . BASE_URL . '/?page=tasks&success=Задача+создана');
    exit;

} catch (Throwable $e) {
    header('Location: ' . BASE_URL . '/?page=task_create&error=' . urlencode('Ошибка: '.$e->getMessage()));
    exit;
}
