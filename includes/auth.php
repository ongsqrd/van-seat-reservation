<?php

require_once __DIR__ . '/db.php';

function role_home(string $role): string
{
    return match ($role) {
        'passenger' => 'trips.php',
        'driver'    => 'driver-dashboard.php',
        'admin'     => 'admin-dashboard.php',
        default     => 'login.php',
    };
}

function current_user(): array
{
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    return [
        'id'   => (int) $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role'],
    ];
}

function require_role(string ...$roles): array
{
    $user = current_user();

    if (!in_array($user['role'], $roles, true)) {
        header('Location: ' . role_home($user['role']));
        exit;
    }

    return $user;
}

function format_phone_display(string $phone): string
{
    if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $phone, $m)) {
        return "{$m[1]} {$m[2]} {$m[3]}";
    }
    return $phone;  
}

function handle_profile_update(int $userId, string $selfPage): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    $name     = trim($_POST['name'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $confirm  = (string) ($_POST['confirm'] ?? '');

    $changingPassword = $password !== '' || $confirm !== '';

    $phoneOwnerStmt = db()->prepare('SELECT id FROM users WHERE phone = ? AND id != ?');
    $phoneOwnerStmt->bind_param('si', $phone, $userId);
    $phoneOwnerStmt->execute();
    $phoneTaken = $phoneOwnerStmt->get_result()->fetch_assoc() !== null;

    $error = match (true) {
        $name === ''                                  => 'name',
        !preg_match('/^\d{10}$/', $phone)             => 'phone',
        $phoneTaken                                    => 'taken',
        $changingPassword && $password !== $confirm    => 'mismatch',
        $changingPassword && strlen($password) < 8     => 'weak',
        default                                          => null,
    };

    if ($error !== null) {
        header('Location: ' . $selfPage . '?edit=1&error=' . $error, true, 303);
        exit;
    }

    if ($changingPassword) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = db()->prepare('UPDATE users SET name = ?, phone = ?, password_hash = ? WHERE id = ?');
        $stmt->bind_param('sssi', $name, $phone, $hash, $userId);
    } else {
        $stmt = db()->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?');
        $stmt->bind_param('ssi', $name, $phone, $userId);
    }
    $stmt->execute();

    $_SESSION['user_name'] = $name;

    header('Location: ' . $selfPage, true, 303);
    exit;
}