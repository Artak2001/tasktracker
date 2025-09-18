<?php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/?page=company_create&error=Метод+не+поддерживается');
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

$name = trim($_POST['company_name'] ?? '');
if ($name === '') {
    header('Location: ' . BASE_URL . '/?page=company_create&error=Введите+название');
    exit;
}

try {
    $pdo->beginTransaction();

    $st = $pdo->prepare("INSERT INTO companies (name, owner_user_id, created_at) VALUES (:n,:uid,NOW())");
    $st->execute([':n'=>$name, ':uid'=>$currentUserId]);
    $cid = $pdo->lastInsertId();

    $st = $pdo->prepare("INSERT INTO company_users (company_id,user_id,role,created_at) VALUES (:cid,:uid,'ADMIN',NOW())");
    $st->execute([':cid'=>$cid, ':uid'=>$currentUserId]);

    $pdo->commit();
    header('Location: ' . BASE_URL . '/?page=company&success=Компания+создана');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header('Location: ' . BASE_URL . '/?page=company_create&error=' . urlencode($e->getMessage()));
}
