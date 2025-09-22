<?php
require __DIR__ . '/../../config/constants.php';
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Метод не поддерживается'));
    exit;
}

$pdo = get_pdo();

// 1) Авторизация
$token   = $_COOKIE['tt_token'] ?? '';
$payload = $token ? jwt_verify($token, JWT_SECRET) : null;
$currentUserId = isset($payload['uid']) ? (int)$payload['uid'] : 0;

if (!$currentUserId) {
    header('Location: ' . BASE_URL . '/?error=' . urlencode('Нужна авторизация'));
    exit;
}

// 2) Входные данные
$cid  = (int)($_POST['company_id'] ?? 0);
$uid  = (int)($_POST['user_id'] ?? 0);
$role = strtoupper(trim($_POST['role'] ?? 'DEV'));

$allowedRoles = ['ADMIN','MANAGER','DEV'];
if (!$cid || !$uid || !in_array($role, $allowedRoles, true)) {
    header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Некорректные данные'));
    exit;
}

try {
    // Важно: работать атомарно
    $pdo->beginTransaction();

    // 3) Проверка, что компания существует и получить owner_user_id
    $st = $pdo->prepare("SELECT owner_user_id FROM companies WHERE id = ?");
    $st->execute([$cid]);
    $company = $st->fetch();
    if (!$company) {
        $pdo->rollBack();
        header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Компания не найдена'));
        exit;
    }
    $ownerId = (int)$company['owner_user_id'];

    // 4) Проверить, что инициатор - участник компании и он ADMIN
    $st = $pdo->prepare("SELECT role FROM company_users WHERE company_id = ? AND user_id = ? LIMIT 1");
    $st->execute([$cid, $currentUserId]);
    $me = $st->fetch();
    if (!$me) {
        $pdo->rollBack();
        header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Вы не участник этой компании'));
        exit;
    }
    if ($me['role'] !== 'ADMIN') {
        $pdo->rollBack();
        header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Только ADMIN может менять роли'));
        exit;
    }

    // 5) Залочить состав участников компании, чтобы не было гонок
    //    (забираем всех админов и целевого участника под FOR UPDATE)
    $adminsStmt = $pdo->prepare("SELECT user_id FROM company_users WHERE company_id = ? AND role = 'ADMIN' FOR UPDATE");
    $adminsStmt->execute([$cid]);
    $adminIds = $adminsStmt->fetchAll(PDO::FETCH_COLUMN);

    $targetStmt = $pdo->prepare("SELECT role FROM company_users WHERE company_id = ? AND user_id = ? FOR UPDATE");
    $targetStmt->execute([$cid, $uid]);
    $target = $targetStmt->fetch();

    if (!$target) {
        $pdo->rollBack();
        header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Пользователь не состоит в компании'));
        exit;
    }

    $oldRole = $target['role'];

    // 6) Гварды на «последнего админа» и владельца компании
    $isTargetAdminBefore = ($oldRole === 'ADMIN');
    $isDemoteAdmin       = ($isTargetAdminBefore && $role !== 'ADMIN');

    if ($isDemoteAdmin) {
        // Нельзя понизить владельца компании
        if ($uid === $ownerId) {
            $pdo->rollBack();
            header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Владелец компании всегда должен быть ADMIN'));
            exit;
        }

        // Нельзя оставлять компанию без админов
        $otherAdmins = array_filter($adminIds, fn($aid) => (int)$aid !== $uid);
        if (count($otherAdmins) === 0) {
            $pdo->rollBack();
            header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode('Нельзя понизить последнего ADMIN'));
            exit;
        }
    }

    // 7) Если роль не меняется — просто успех
    if ($oldRole === $role) {
        $pdo->commit();
        header('Location: ' . BASE_URL . '/?page=company&success=' . urlencode('Роль не изменилась'));
        exit;
    }

    // 8) Обновление роли
    $upd = $pdo->prepare("UPDATE company_users SET role = :r WHERE company_id = :cid AND user_id = :uid");
    $upd->execute([':r' => $role, ':cid' => $cid, ':uid' => $uid]);

    $pdo->commit();
    header('Location: ' . BASE_URL . '/?page=company&success=' . urlencode('Роль обновлена'));
} catch (Throwable $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    header('Location: ' . BASE_URL . '/?page=company&error=' . urlencode($e->getMessage()));
}
