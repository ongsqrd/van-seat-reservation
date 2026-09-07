<?php
  /* ------------------------------------------------------------
     index.php — entry router.

     Sends a signed-in user to their role's home and guests to
     login. role_home() (includes/auth.php) is the single source
     for the role -> page mapping; login_process.php uses the same
     function after a successful login, so the two can't disagree.

     No markup here — it's a redirect, so no header/footer.
     ------------------------------------------------------------ */
  require_once '../includes/auth.php';
  session_start();

  header('Location: ' . role_home($_SESSION['user_role'] ?? 'guest'));
  exit;