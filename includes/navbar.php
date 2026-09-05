<?php
  /* ------------------------------------------------------------
     Navigation bar — included on every page.

     TEMPORARY: each page sets these two variables before the
     include. In the PHP phase they come from $_SESSION instead.

       $user_role  'guest' | 'passenger' | 'driver' | 'admin'
       $user_name  full name, used for the avatar initials

     A guest sees the logo only. Login and register already link
     to each other inside the form, so the bar stays clean.
     ------------------------------------------------------------ */

  $user_role = $user_role ?? 'guest';
  $user_name = $user_name ?? '';

  // "Jane Doe" -> "JD"
  $initials = '';
  foreach (preg_split('/\s+/', trim($user_name), -1, PREG_SPLIT_NO_EMPTY) as $part) {
      $initials .= mb_strtoupper(mb_substr($part, 0, 1));
  }
  $initials = mb_substr($initials, 0, 2);

  // the avatar links to the profile for this role
  $profile_pages = [
      'passenger' => 'profile.php',
      'driver'    => 'driver-profile.php',
      'admin'     => 'admin-profile.php',
  ];
  $profile_page = $profile_pages[$user_role] ?? 'profile.php';
?>
<nav class="navbar" aria-label="Main">
  <div class="navbar-inner">

    <a href="index.php" class="logo">AU VAN</a>

    <?php if ($user_role !== 'guest'): ?>
      <a href="<?= htmlspecialchars($profile_page) ?>" class="avatar"
         aria-label="<?= htmlspecialchars($user_name) ?> — profile">
        <?= htmlspecialchars($initials) ?>
      </a>
    <?php endif; ?>

  </div>
</nav>