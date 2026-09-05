<?php
  /*
   * logout.php — clears the session and redirects. No UI.
   *
   * Auth isn't wired up yet, but this is the real teardown, so it's
   * correct the moment sessions land. With no active session it simply
   * redirects, which is harmless.
   */

  session_start();

  // drop every session variable
  $_SESSION = [];

  // expire the session cookie as well (belt and braces — this is the
  // step people forget, which leaves a resumable session behind)
  if (ini_get('session.use_cookies')) {
      $p = session_get_cookie_params();
      setcookie(
          session_name(),
          '',
          time() - 42000,
          $p['path'],
          $p['domain'],
          $p['secure'],
          $p['httponly']
      );
  }

  // tear down the session store
  session_destroy();

  // back to the login screen
  header('Location: login.php');
  exit;