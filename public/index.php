<?php
  require_once '../includes/auth.php';
  session_start();

  header('Location: ' . role_home($_SESSION['user_role'] ?? 'guest'));
  exit;