<?php
  /* ------------------------------------------------------------
     Shared document head + navbar. A page sets these before it
     includes this file:

       $page_title  window/tab title (defaults to "AU VAN")
       $user_role   'guest' | 'passenger' | 'driver' | 'admin'
       $user_name   full name, for the avatar initials

     then emits its <main>, and finally includes footer.php.

     The stylesheet href is relative to the page URL (all pages
     live in /public), so it resolves the same for every page.
     ------------------------------------------------------------ */
  $page_title = $page_title ?? 'AU VAN';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php include __DIR__ . '/navbar.php'; ?>