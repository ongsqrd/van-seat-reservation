<?php

require_once '../includes/db.php';
require_once '../includes/auth.php';

session_start();

$phone    = trim($_POST['phone'] ?? '');
$password = (string) ($_POST['password'] ?? '');

function login_fail(string $phone)
{
    header('Location: login.php?error=invalid&phone=' . urlencode($phone));
    exit;
}

if ($phone === '' || $password === '') {
    login_fail($phone);
}

$stmt = db()->prepare('SELECT id, name, role, password_hash FROM users WHERE phone = ?');
$stmt->bind_param('s', $phone);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user === null || !password_verify($password, $user['password_hash'])) {
    login_fail($phone);
}

session_regenerate_id(true);

$_SESSION['user_id']   = (int) $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];

header('Location: ' . role_home($user['role']));
exit;