<?php
  $page_title = $page_title ?? 'AU VAN';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php include __DIR__ . '/navbar.php'; ?>