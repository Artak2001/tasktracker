<?php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';

$pdo = get_pdo();

$name  = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password']  ?? '';
$pass2 = $_POST['password2'] ?? '';

if ($name === '' || $email === '' || $pass === '' || $pass2 === '') {
    header('Location: ' . BASE_URL . '/index.php?error=Заполните+все+поля'); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . BASE_URL . '/index.php?error=Некорректный+email'); exit;
}
if (strlen($pass) < 8) {
    header('Location: ' . BASE_URL . '/index.php?error=Пароль+минимум+8+символов'); exit;
}
if ($pass !== $pass2) {
    header('Location: ' . BASE_URL . '/index.php?error=Пароли+не+совпадают'); exit;
}

$st = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$st->execute([$email]);
if ($st->fetch()) {
    header('Location: ' . BASE_URL . '/index.php?error=Email+уже+занят'); exit;
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
$st = $pdo->prepare('INSERT INTO users (name,email,password_hash) VALUES (?,?,?)');
$st->execute([$name,$email,$hash]);

header('Location: ' . BASE_URL . '/index.php?success=Регистрация+успешна,+теперь+войдите');
exit;
