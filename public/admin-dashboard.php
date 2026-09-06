<?php
  /* ------------------------------------------------------------------
     Placeholder admin figures. These become COUNT queries once the DB
     lands (trips today, seats booked, drivers on duty, and trips still
     missing a driver). Hardcoded for now like the rest of the UI phase.
     ------------------------------------------------------------------ */
  $date = '10 May 2026';

  $stats = [
      ['label' => 'Trips Today',  'value' => 6],
      ['label' => 'Seats Booked', 'value' => 58],
      ['label' => 'Drivers',      'value' => 4],
  ];

  $unassigned = 2;                 // trips with no driver assigned yet
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AU VAN - Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
    $user_role = 'admin';
    $user_name = 'Chanyapat Saeng-Xuto';
    include '../includes/navbar.php';
  ?>

  <main class="page">
    <div class="container">

      <div class="admin-dash">

        <div class="admin-dash-head">
          <h2>Admin Dashboard</h2>
          <p class="admin-dash-date"><?= htmlspecialchars($date) ?></p>
        </div>

        <?php if ($unassigned > 0): ?>
          <a class="admin-alert" href="admin-trips.php">
            <span class="admin-alert-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   focusable="false">
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
            </span>
            <span class="admin-alert-body">
              <span class="admin-alert-title"><?= (int) $unassigned ?> <?= $unassigned === 1 ? 'trip needs' : 'trips need' ?> a driver</span>
              <span class="admin-alert-text">Trips can't run until a driver is assigned</span>
            </span>
            <span class="admin-alert-go" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   focusable="false">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </span>
          </a>
        <?php endif; ?>

        <div class="admin-stats">
          <?php foreach ($stats as $s): ?>
            <div class="card admin-stat">
              <span class="admin-stat-value"><?= (int) $s['value'] ?></span>
              <span class="admin-stat-label"><?= htmlspecialchars($s['label']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="admin-actions">

          <a class="card admin-action" href="admin-trips.php">
            <span class="admin-action-body">
              <span class="admin-action-title">Manage Trips</span>
              <span class="admin-action-text">Create, edit, and assign drivers</span>
            </span>
            <span class="admin-action-go" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   focusable="false">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </span>
          </a>

          <a class="card admin-action" href="admin-manage.php">
            <span class="admin-action-body">
              <span class="admin-action-title">Manage Data</span>
              <span class="admin-action-text">Vans, routes, stops, users</span>
            </span>
            <span class="admin-action-go" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   focusable="false">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </span>
          </a>

        </div>

      </div>

    </div>
  </main>
</body>

</html>