<?php
  require_once '../includes/driver-today.php';

  /* ------------------------------------------------------------------
     Placeholder account (driver). Same shell as the passenger profile;
     only the data, role, summary card, and self-link differ.
     ------------------------------------------------------------------ */
  $profile = [
    'name'  => 'John Doe',
    'phone' => '091 123 4567',
    'role'  => 'Driver',
  ];

  // role-specific summary card -> today's trips (driver home)
  $trip_count = count(get_todays_trips());
  $summary = [
    'label' => "Today's Trips",
    'value' => $trip_count . ' ' . ($trip_count === 1 ? 'trip' : 'trips') . ' assigned',
    'href'  => 'driver-dashboard.php',
  ];

  $self    = 'driver-profile.php';
  $editing = isset($_GET['edit']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AU VAN - Driver</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
    $user_role = 'driver';
    $user_name = $profile['name'];
    include '../includes/navbar.php';
    include '../includes/profile-shell.php';
  ?>
</body>

</html>