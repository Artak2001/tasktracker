<?php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/?page=company&error=Метод+не+поддерживается');
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

// достаём email пользователя
$st = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$st->execute([$currentUserId]);
$user = $st->fetch();

if (!$user) {
    header('Location: ' . BASE_URL . '/?error=Пользователь+не+найден');
    exit;
}

$email = $user['email'];
$inviteId = (int)($_POST['invite_id'] ?? 0);

try {
    // проверяем приглашение
    $st = $pdo->prepare("
        SELECT * FROM invitations
        WHERE id = ? AND email = ? AND status = 'PENDING'
        LIMIT 1
    ");
    $st->execute([$inviteId, $email]);
    $invite = $st->fetch();

    if (!$invite) {
        header('Location: ' . BASE_URL . '/?page=company&error=Приглашение+не+найдено+или+устарело');
        exit;
    }

    $companyId = (int)$invite['company_id'];

    // добавляем пользователя в company_users
    $st = $pdo->prepare("
        INSERT IGNORE INTO company_users (company_id, user_id, role, created_at)
        VALUES (?, ?, 'DEV', NOW())
    ");
    $st->execute([$companyId, $currentUserId]);

    // обновляем приглашение
    $st = $pdo->prepare("UPDATE invitations SET status = 'ACCEPTED' WHERE id = ?");
    $st->execute([$inviteId]);

    header('Location: ' . BASE_URL . '/?page=company&success=Вы+приняли+приглашение');
    exit;

} catch (Throwable $e) {
    header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Ошибка: '.$e->getMessage()));
    exit;
}
