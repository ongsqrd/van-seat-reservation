<?php
/**
 * includes/auth.php
 *
 * One place for "which page is this role's home", so index.php and
 * login_process.php can never disagree about where a role lands.
 * Also holds the shared account helpers used by all three profile
 * pages: phone formatting and the profile-update handler.
 */

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

/**
 * The logged-in user, or redirect to login.php if there isn't one.
 * Starts the session, so call this before touching $_SESSION elsewhere.
 *
 * @return array{id: int, name: string, role: string}
 */
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

/**
 * Require the logged-in user to hold one of the given roles. A guest is
 * sent to login.php (via current_user()); a logged-in user with the wrong
 * role is sent to *their* home rather than shown an error — the page just
 * isn't for them, the same way a passenger doesn't see an "access denied"
 * for a page that's simply not part of their flow.
 *
 * @return array{id: int, name: string, role: string}
 */
function require_role(string ...$roles): array
{
    $user = current_user();

    if (!in_array($user['role'], $roles, true)) {
        header('Location: ' . role_home($user['role']));
        exit;
    }

    return $user;
}

/**
 * Thai mobile numbers are stored as plain digits (0913345776);
 * display them grouped 3-3-4 the way the UI always has. Shared by all
 * three profile pages so there's one copy, not three.
 */
function format_phone_display(string $phone): string
{
    if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $phone, $m)) {
        return "{$m[1]} {$m[2]} {$m[3]}";
    }
    return $phone;   // unexpected format — show as stored rather than mangle it
}

/**
 * Handles the profile-shell.php edit form's POST, shared by all three
 * roles' profile pages. Must be called BEFORE header.php is included —
 * like every other write path in this app, it may redirect, and PHP
 * can't send a Location header after any output has already gone out.
 *
 * Password change is optional: leaving both password fields blank keeps
 * the existing password and only updates name/phone. Filling either one
 * requires both to match and be at least 8 characters.
 *
 * A no-op on a GET request, so pages can call this unconditionally.
 */
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

    // keep the session/navbar name in sync immediately, no re-login needed
    $_SESSION['user_name'] = $name;

    header('Location: ' . $selfPage, true, 303);
    exit;
}