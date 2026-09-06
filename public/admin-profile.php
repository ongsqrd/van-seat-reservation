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

  $page_title = 'AU VAN - Admin';
  $user_role  = 'admin';
  $user_name  = $profile['name'];
  include '../includes/header.php';
  include '../includes/profile-shell.php';
  include '../includes/footer.php';
?>