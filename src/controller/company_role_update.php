<?php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/?page=company&error=Метод+не+поддерживается');
    exit;
}

$pdo = get_pdo();
$token   = $_COOKIE['tt_token'] ?? '';
$payload = $token ? jwt_verify($token, JWT_SECRET) : null;
$currentUserId = $payload['uid'] ?? null;
if (!$currentUserId) {
    header('Location: ' . BASE_URL . '/?error=Нужна+авторизация');
    exit;
}

$cid = (int)($_POST['company_id'] ?? 0);
$uid = (int)($_POST['user_id'] ?? 0);
$role = $_POST['role'] ?? 'DEV';

if (!$cid || !$uid) {
    header('Location: ' . BASE_URL . '/?page=company&error=Нет+данных');
    exit;
}

try {
    $st = $pdo->prepare("UPDATE company_users SET role=:r WHERE company_id=:cid AND user_id=:uid");
    $st->execute([':r'=>$role, ':cid'=>$cid, ':uid'=>$uid]);
    header('Location: ' . BASE_URL . '/?page=company&success=Роль+обновлена');
} catch (Throwable $e) {
    header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode($e->getMessage()));
}
