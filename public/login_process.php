<?php
/**
 * public/login_process.php — handles the login.php POST.
 *
 * Verifies phone + password against users.password_hash, starts a
 * session on success, and redirects to the role's home (role_home(),
 * shared with index.php). On failure it redirects back to login.php
 * with an error flag and the phone the person typed, so they don't
 * have to retype it (never sends the password back).
 */

require_once '../includes/db.php';
require_once '../includes/auth.php';

session_start();

$phone    = trim($_POST['phone'] ?? '');
$password = (string) ($_POST['password'] ?? '');

function login_fail(string $phone): never
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

// regenerate the session id on privilege change — standard session-fixation guard
session_regenerate_id(true);

$_SESSION['user_id']   = (int) $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];

header('Location: ' . role_home($user['role']));
exit;