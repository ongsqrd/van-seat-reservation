<?php
  require_once '../includes/driver-today.php';
  require_once '../includes/auth.php';

  $user = require_role('driver');

  handle_profile_update($user['id'], 'driver-profile.php');

  $stmt = db()->prepare('SELECT name, phone FROM users WHERE id = ?');
  $stmt->bind_param('i', $user['id']);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();

  $profile = [
    'name'  => $row['name'],
    'phone' => format_phone_display($row['phone']),
    'role'  => 'Driver',
  ];

  $trip_count = count(get_todays_trips());
  $summary = [
    'label' => "Today's Trips",
    'value' => $trip_count . ' ' . ($trip_count === 1 ? 'trip' : 'trips') . ' assigned',
    'href'  => 'driver-dashboard.php',
  ];

  $self    = 'driver-profile.php';
  $editing = isset($_GET['edit']);

  $profileErrorText = [
      'name'     => 'Enter a name.',
      'phone'    => 'Enter a valid 10-digit phone number.',
      'taken'    => 'That phone number belongs to another account.',
      'mismatch' => "Passwords don't match.",
      'weak'     => 'Password must be at least 8 characters.',
  ][$_GET['error'] ?? ''] ?? null;

  $page_title = 'AU VAN - Driver';
  $user_role  = 'driver';
  $user_name  = $profile['name'];
  include '../includes/header.php';
  include '../includes/profile-shell.php';
  include '../includes/footer.php';
?>