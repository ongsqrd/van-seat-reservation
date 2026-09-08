<?php
  require_once '../includes/bookings.php';
  require_once '../includes/auth.php';

  $user = require_role('passenger');

  handle_profile_update($user['id'], 'profile.php');   // no-op unless this is a POST

  $stmt = db()->prepare('SELECT name, phone FROM users WHERE id = ?');
  $stmt->bind_param('i', $user['id']);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();

  $profile = [
    'name'  => $row['name'],
    'phone' => format_phone_display($row['phone']),
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

  $profileErrorText = [
      'name'     => 'Enter a name.',
      'phone'    => 'Enter a valid 10-digit phone number.',
      'taken'    => 'That phone number belongs to another account.',
      'mismatch' => "Passwords don't match.",
      'weak'     => 'Password must be at least 8 characters.',
  ][$_GET['error'] ?? ''] ?? null;

  $page_title = 'AU VAN - Passenger';
  $user_role  = 'passenger';
  $user_name  = $profile['name'];
  include '../includes/header.php';
  include '../includes/profile-shell.php';
  include '../includes/footer.php';
?>