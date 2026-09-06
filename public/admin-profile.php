<?php
  /* ------------------------------------------------------------------
     Placeholder account (admin). Same shell as the passenger/driver
     profiles; only the data, role, summary card, and self-link differ.
     ------------------------------------------------------------------ */
  $profile = [
    'name'  => 'Chanyapat Saeng-Xuto',
    'phone' => '091 743 9776',
    'role'  => 'Admin',
  ];

  // role-specific summary card -> the admin hub
  $summary = [
    'label' => 'Admin Dashboard',
    'value' => 'Manage trips & data',
    'href'  => 'admin-dashboard.php',
  ];

  $self    = 'admin-profile.php';
  $editing = isset($_GET['edit']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AU VAN - Admin</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
    $user_role = 'admin';
    $user_name = $profile['name'];
    include '../includes/navbar.php';
    include '../includes/profile-shell.php';
  ?>
</body>

</html>