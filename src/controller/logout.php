<?php
require __DIR__ . '/../../config/constants.php';

setcookie('tt_token', '', [
    'expires'  => time() - 3600,
    'path'     => BASE_URL,
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);

header('Location: ' . BASE_URL . '/index.php?success=Вы+вышли');
exit;
