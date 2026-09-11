<?php

require_once '../includes/db.php';
require_once '../includes/auth.php';

session_start();

$fullname = trim($_POST['fullname'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$password = (string) ($_POST['password'] ?? '');
$confirm  = (string) ($_POST['confirmPassword'] ?? '');
$agreed   = isset($_POST['terms']);

function register_fail(string $error, string $fullname, string $phone)
{
    $qs = http_build_query(['error' => $error, 'fullname' => $fullname, 'phone' => $phone]);
    header('Location: register.php?' . $qs);
    exit;
}

if ($fullname === '' || $phone === '' || $password === '' || $confirm === '') {
    register_fail('missing', $fullname, $phone);
}
if (!$agreed) {
    register_fail('terms', $fullname, $phone);
}
if ($password !== $confirm) {
    register_fail('mismatch', $fullname, $phone);
}
if (strlen($password) < 8) {
    register_fail('weak', $fullname, $phone);
}

$exists = db()->prepare('SELECT id FROM users WHERE phone = ?');
$exists->bind_param('s', $phone);
$exists->execute();
if ($exists->get_result()->fetch_assoc() !== null) {
    register_fail('taken', $fullname, $phone);
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$insert = db()->prepare(
    "INSERT INTO users (name, phone, password_hash, role) VALUES (?, ?, ?, 'passenger')"
);
$insert->bind_param('sss', $fullname, $phone, $hash);
$insert->execute();

session_regenerate_id(true);

$_SESSION['user_id']   = (int) db()->insert_id;
$_SESSION['user_name'] = $fullname;
$_SESSION['user_role'] = 'passenger';

header('Location: ' . role_home('passenger'));
exit;