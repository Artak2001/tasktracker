<?php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

$pdo = get_pdo();

$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
    header('Location: ' . BASE_URL . '/index.php?error=Введите+email+и+пароль'); exit;
}

$st = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
$st->execute([$email]);
$user = $st->fetch();

if (!$user || !password_verify($pass, $user['password_hash'])) {
    header('Location: ' . BASE_URL . '/index.php?error=Неверный+email+или+пароль'); exit;
}

$token = jwt_sign(['uid' => (int)$user['id']], JWT_SECRET, 60*60*24*7);

setcookie('tt_token', $token, [
    'expires'  => time() + 60*60*24*7,
    'path'     => BASE_URL,
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);

header('Location: ' . BASE_URL . '/index.php');
exit;
