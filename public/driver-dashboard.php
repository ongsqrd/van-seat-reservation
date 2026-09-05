<?php
  require_once '../includes/routes.php';
  require_once '../includes/driver-today.php';

  $trips = get_todays_trips();
  $count = count($trips);
  $date  = '10 May 2026';          // hardcoded like the rest of the flow; no trips table yet

  function trip_status_label(string $status): string
  {
      return $status === 'next' ? 'Next Trip' : 'Upcoming';
  }
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
    $user_role = 'driver';
    $user_name = 'John Doe';
    include '../includes/navbar.php';
  ?>

  <main class="page">
    <div class="container container-mid">

      <div class="today">

        <div class="today-head">
          <h2>Today's Trips</h2>
          <p class="today-sub"><?= htmlspecialchars($date) ?> &middot; <?= (int) $count ?> <?= $count === 1 ? 'trip' : 'trips' ?> assigned</p>
        </div>

        <ul class="today-list">
          <?php foreach ($trips as $t):
            $route  = find_route($t['route_id']);
            $from   = $route['from'] ?? '';
            $to     = $route['to']   ?? '';
            $isNext = $t['status'] === 'next';
          ?>
            <li class="today-card <?= $isNext ? 'is-next' : '' ?>">
              <div class="today-info">

                <span class="today-status <?= $isNext ? 'is-next' : '' ?>"><?= trip_status_label($t['status']) ?></span>

                <div class="today-route">
                  <span class="today-place"><?= htmlspecialchars($from) ?></span>
                  <span class="today-arrow" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         focusable="false">
                      <line x1="5" y1="12" x2="19" y2="12"></line>
                      <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                  </span>
                  <span class="today-place"><?= htmlspecialchars($to) ?></span>
                </div>

                <p class="today-meta">
                  <span class="today-time"><?= htmlspecialchars($t['time']) ?></span>
                  <span class="today-sep" aria-hidden="true">&middot;</span>
                  <span class="today-seats"><?= (int) $t['boarded'] ?> / <?= (int) $t['capacity'] ?> seats boarded</span>
                </p>

              </div>

              <a class="btn btn-primary btn-sm" href="driver-trip.php?trip=<?= (int) $t['id'] ?>">View Details</a>
            </li>
          <?php endforeach; ?>
        </ul>

      </div>

    </div>
  </main>
</body>

</html>