<?php
  require_once '../includes/bookings.php';
  require_once '../includes/auth.php';
  require_once '../includes/db.php';

  $user = require_role('passenger');

  /**
   * Thai mobile numbers are stored as plain digits (0913345776);
   * display them grouped 3-3-4 the way the UI always has.
   */
  function format_phone_display(string $phone): string
  {
      if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $phone, $m)) {
          return "{$m[1]} {$m[2]} {$m[3]}";
      }
      return $phone;   // unexpected format — show as stored rather than mangle it
  }

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

  $page_title = 'AU VAN - Passenger';
  $user_role  = 'passenger';
  $user_name  = $profile['name'];
  include '../includes/header.php';
  include '../includes/profile-shell.php';
  include '../includes/footer.php';
?>