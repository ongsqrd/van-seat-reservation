<?php
  require_once '../includes/driver-today.php';

  /* ------------------------------------------------------------------
     Placeholder account (driver). Same shell as the passenger profile;
     only the data, role, summary card, and self-link differ.
     ------------------------------------------------------------------ */
  $profile = [
    'name'  => 'Patchara Chainiyom',
    'phone' => '0924457781',
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

  $page_title = 'AU VAN - Driver';
  $user_role  = 'driver';
  $user_name  = $profile['name'];
  include '../includes/header.php';
  include '../includes/profile-shell.php';
  include '../includes/footer.php';
?>