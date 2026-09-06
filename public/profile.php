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

  $page_title = 'AU VAN - Passenger';
  $user_role  = 'passenger';
  $user_name  = $profile['name'];
  include '../includes/header.php';
  include '../includes/profile-shell.php';
  include '../includes/footer.php';
?>