<?php
  require_once '../includes/bookings.php';

  /* ------------------------------------------------------------------
     Placeholder account.
     No users table / session yet, so the account is faked here — same
     posture as the $booking placeholder in ticket.php. When auth lands
     this comes from the session / users table.
     ------------------------------------------------------------------ */
  $profile = [
    'name'  => 'Jane Doe',
    'phone' => '091 334 5776',
    'role'  => 'Passenger',
  ];

  // summary card counts the passenger's upcoming trips (links to history)
  $upcoming_count = 0;
  foreach (get_bookings() as $b) {
      if ($b['status'] !== 'completed') {
          $upcoming_count++;
      }
  }

  // view vs edit is server-side state, like trip-times reading ?route
  $editing = isset($_GET['edit']);

  // initials for the avatar — multi-byte safe, same approach as the navbar
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AU VAN - Passenger</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
    $user_role = 'passenger';
    $user_name = $profile['name'];
    include '../includes/navbar.php';
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
            <a class="btn btn-secondary btn-block profile-security-action" href="profile.php?edit=1">Change Password</a>
          </section>

          <a class="card profile-summary" href="my-bookings.php">
            <span class="profile-summary-text">
              <span class="profile-summary-label">Booking History</span>
              <span class="profile-summary-value"><?= (int) $upcoming_count ?> upcoming <?= $upcoming_count === 1 ? 'trip' : 'trips' ?></span>
            </span>
            <span class="profile-summary-go" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   focusable="false">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </span>
          </a>

          <a class="btn btn-primary btn-block" href="profile.php?edit=1">Edit Profile</a>

        <?php else: ?>

          <!-- EDIT -->
          <form class="profile-form" method="POST" action="profile.php">
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
              <a href="profile.php" class="btn btn-secondary">Back</a>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>

        <?php endif; ?>

      </div>

    </div>
  </main>
</body>

</html>