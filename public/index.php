<?php
  /* ------------------------------------------------------------
     index.php — entry router.

     Sends a signed-in user to their role's home and guests to
     login. Auth isn't wired yet, so with no session this always
     lands on login.php; once login_process populates the session
     ($_SESSION['user_role']), the switch routes each role home.

     No markup here — it's a redirect, so no header/footer.
     ------------------------------------------------------------ */
  session_start();

  switch ($_SESSION['user_role'] ?? 'guest') {
      case 'passenger':
          header('Location: trips.php');
          break;
      case 'driver':
          header('Location: driver-dashboard.php');
          break;
      case 'admin':
          header('Location: admin-dashboard.php');
          break;
      default:
          header('Location: login.php');
  }
  exit;