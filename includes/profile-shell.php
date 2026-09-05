<?php
/**
 * includes/profile-shell.php
 *
 * Shared profile body for every role. The including page sets:
 *
 *   $profile  ['name','phone','role']
 *   $self     this page's filename, for the edit links + form action
 *             ('profile.php' | 'driver-profile.php' | 'admin-profile.php')
 *   $summary  ['label','value','href'] — the role-specific card linking to
 *             this role's home (passenger -> my-bookings, driver ->
 *             driver-dashboard, admin -> admin-dashboard). Optional.
 *   $editing  bool — whether to show the edit form (isset($_GET['edit'])).
 *
 * View vs edit is server-side ?edit state, so it stays keyboard-accessible
 * with no JS. Styling is section 7j of style.css.
 */

if (!function_exists('profile_initials')) {
    // "Jane Doe" -> "JD", multi-byte safe (same approach as the navbar)
    function profile_initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) {
            return '';
        }
        $first = mb_substr($parts[0], 0, 1);
        $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
        return mb_strtoupper($first . $last);
    }
}

$self    = $self    ?? 'profile.php';
$summary = $summary ?? null;
$editing = $editing ?? isset($_GET['edit']);
?>
<main class="page">
  <div class="container container-mid">

    <div class="profile">

      <div class="profile-head">
        <span class="profile-avatar"><?= htmlspecialchars(profile_initials($profile['name'])) ?></span>
        <span class="profile-id">
          <span class="profile-name"><?= htmlspecialchars($profile['name']) ?></span>
          <a class="profile-logout" href="logout.php">log out</a>
        </span>
      </div>

      <?php if (!$editing): ?>

        <!-- VIEW -->
        <section class="card">
          <h2 class="card-title">Account Details</h2>
          <dl class="profile-list">
            <div class="profile-row">
              <dt class="profile-label">Full Name</dt>
              <dd class="profile-value"><?= htmlspecialchars($profile['name']) ?></dd>
            </div>
            <div class="profile-row">
              <dt class="profile-label">Phone</dt>
              <dd class="profile-value"><?= htmlspecialchars($profile['phone']) ?></dd>
            </div>
            <div class="profile-row">
              <dt class="profile-label">Role</dt>
              <dd class="profile-value"><?= htmlspecialchars($profile['role']) ?></dd>
            </div>
          </dl>
        </section>

        <section class="card">
          <h2 class="card-title">Security</h2>
          <dl class="profile-list">
            <div class="profile-row">
              <dt class="profile-label">Password</dt>
              <dd class="profile-value">••••••••</dd>
            </div>
          </dl>
          <a class="btn btn-secondary btn-block profile-security-action" href="<?= htmlspecialchars($self) ?>?edit=1">Change Password</a>
        </section>

        <?php if ($summary !== null): ?>
          <a class="card profile-summary" href="<?= htmlspecialchars($summary['href']) ?>">
            <span class="profile-summary-text">
              <span class="profile-summary-label"><?= htmlspecialchars($summary['label']) ?></span>
              <span class="profile-summary-value"><?= htmlspecialchars($summary['value']) ?></span>
            </span>
            <span class="profile-summary-go" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   focusable="false">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </span>
          </a>
        <?php endif; ?>

        <a class="btn btn-primary btn-block" href="<?= htmlspecialchars($self) ?>?edit=1">Edit Profile</a>

      <?php else: ?>

        <!-- EDIT -->
        <form class="profile-form" method="POST" action="<?= htmlspecialchars($self) ?>">
          <section class="card">
            <h2 class="card-title">Edit Profile</h2>
            <div class="stack">
              <div class="field">
                <label class="field-label" for="name">Name</label>
                <input class="input" type="text" id="name" name="name"
                       value="<?= htmlspecialchars($profile['name']) ?>" autocomplete="name">
              </div>
              <div class="field">
                <label class="field-label" for="phone">Phone Number</label>
                <input class="input" type="tel" id="phone" name="phone"
                       value="<?= htmlspecialchars($profile['phone']) ?>" autocomplete="tel">
              </div>
              <div class="field">
                <label class="field-label" for="password">Password</label>
                <input class="input" type="password" id="password" name="password"
                       autocomplete="new-password">
              </div>
              <div class="field">
                <label class="field-label" for="confirm">Confirm Password</label>
                <input class="input" type="password" id="confirm" name="confirm"
                       autocomplete="new-password">
              </div>
            </div>
          </section>

          <div class="btn-row">
            <a href="<?= htmlspecialchars($self) ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>

      <?php endif; ?>

    </div>

  </div>
</main>