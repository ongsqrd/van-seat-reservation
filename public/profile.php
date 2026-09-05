<?php
  require_once '../includes/bookings.php';

  /* ------------------------------------------------------------------
     Placeholder account (passenger). No users table / session yet, so
     the account is faked here. When auth lands it comes from the
     session / users table. Body is rendered by includes/profile-shell.php.
     ------------------------------------------------------------------ */
  $profile = [
    'name'  => 'Jane Doe',
    'phone' => '091 334 5776',
    'role'  => 'Passenger',
  ];

  // role-specific summary card -> booking history, counting upcoming trips
  $upcoming_count = 0;
  foreach (get_bookings() as $b) {
      if ($b['status'] !== 'completed') {
          $upcoming_count++;
      }
  }
  $summary = [
    'label' => 'Booking History',
    'value' => $upcoming_count . ' upcoming ' . ($upcoming_count === 1 ? 'trip' : 'trips'),
    'href'  => 'my-bookings.php',
  ];

  $self    = 'profile.php';
  $editing = isset($_GET['edit']);
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
    include '../includes/profile-shell.php';
  ?>
</body>

</html>