<?php
/**
 * includes/auth.php
 *
 * One place for "which page is this role's home", so index.php and
 * login_process.php can never disagree about where a role lands.
 */

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