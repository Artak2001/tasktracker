<?php
// src/controller/company_invite.php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/?page=company&error=Метод+не+поддерживается');
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
$companyId = (int)($_POST['company_id'] ?? 0);
$email     = trim($_POST['email'] ?? '');

if ($companyId <= 0 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: ' . BASE_URL . '/?page=company&error=Некорректные+данные');
  exit;
}

try {
  // Проверим, что текущий пользователь — админ компании
  $st = $pdo->prepare("SELECT role FROM company_users WHERE company_id=? AND user_id=? LIMIT 1");
  $st->execute([$companyId, $currentUserId]);
  $row = $st->fetch();
  if (!$row || $row['role'] !== 'ADMIN') {
    header('Location: ' . BASE_URL . '/?page=company&error=Нет+прав+приглашать');
    exit;
  }

  // Генерация токена приглашения
  $token = bin2hex(random_bytes(16));

  // Запишем приглашение
  $st = $pdo->prepare("
    INSERT INTO invitations (company_id, email, token, invited_by, status, created_at)
    VALUES (:cid, :email, :token, :inviter, 'PENDING', NOW())
  ");
  $st->execute([
    ':cid'     => $companyId,
    ':email'   => $email,
    ':token'   => $token,
    ':inviter' => $currentUserId,
  ]);

  header('Location: ' . BASE_URL . '/?page=company&success=Приглашение+отправлено');
  exit;

} catch (Throwable $e) {
  header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Ошибка: '.$e->getMessage()));
  exit;
}
