<?php
  require_once '../includes/driver-today.php';
  require_once '../includes/auth.php';
  require_once '../includes/db.php';

  $user = require_role('driver');

  // same phone-formatting helper as the passenger profile — Thai mobile
  // numbers are stored as plain digits, displayed grouped 3-3-4
  function format_phone_display(string $phone): string
  {
      if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $phone, $m)) {
          return "{$m[1]} {$m[2]} {$m[3]}";
      }
      return $phone;
  }

  $stmt = db()->prepare('SELECT name, phone FROM users WHERE id = ?');
  $stmt->bind_param('i', $user['id']);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();

  $profile = [
    'name'  => $row['name'],
    'phone' => format_phone_display($row['phone']),
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