<?php
  require_once '../includes/routes.php';
  require_once '../includes/driver-today.php';

  // which trip? validate against today's trips; bounce back if the id is bogus
  $tripId = isset($_GET['trip']) ? (int) $_GET['trip'] : 0;
  $trip   = null;
  foreach (get_todays_trips() as $t) {
      if ($t['id'] === $tripId) {
          $trip = $t;
          break;
      }
  }
  if ($trip === null) {
      header('Location: driver-dashboard.php');
      exit;
  }

  $route = find_route($trip['route_id']);
  $from  = $route['from'] ?? '';
  $to    = $route['to']   ?? '';
  $date  = '10 May 2026';

  /* ------------------------------------------------------------------
     Placeholder manifest for this trip. Per-trip passenger lists arrive
     with the bookings table; for now the same list stands in whichever
     trip you open. 'boarded' / 'waiting' is the check-in status.
     ------------------------------------------------------------------ */
  $capacity = 15;                        // the assigned van's seats
  $manifest = [
      ['passenger' => 'Jane Doe',    'seats' => 3, 'dropoff' => 'Bangna Junction', 'status' => 'boarded'],
      ['passenger' => 'John Watson', 'seats' => 1, 'dropoff' => 'Mega Bangna',     'status' => 'waiting'],
  ];

  $bookings = count($manifest);
  $booked   = array_sum(array_column($manifest, 'seats'));

  function manifest_status_label(string $status): string
  {
      return $status === 'boarded' ? 'Boarded' : 'Waiting';
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AU VAN - Details</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
    $user_role = 'driver';
    $user_name = 'John Doe';
    include '../includes/navbar.php';
  ?>

  <main class="page">
    <div class="container">

      <div class="trip-detail">

        <a class="trip-detail-back" href="driver-dashboard.php">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               aria-hidden="true" focusable="false">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Today's Trips
        </a>

        <section class="card">

          <div class="trip-detail-head">
            <div class="trip-detail-route">
              <span class="trip-detail-place"><?= htmlspecialchars($from) ?></span>
              <span class="trip-detail-arrow" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     focusable="false">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </span>
              <span class="trip-detail-place"><?= htmlspecialchars($to) ?></span>
            </div>
            <span class="trip-detail-date"><?= htmlspecialchars($date) ?></span>
          </div>

          <div class="trip-detail-stats">
            <div class="trip-detail-stat">
              <span class="trip-detail-stat-label">Time</span>
              <span class="trip-detail-stat-value"><?= htmlspecialchars($trip['time']) ?></span>
            </div>
            <div class="trip-detail-stat">
              <span class="trip-detail-stat-label">Seats booked</span>
              <span class="trip-detail-stat-value"><?= sprintf('%02d', $booked) ?> / <?= (int) $capacity ?></span>
            </div>
          </div>

          <div class="trip-detail-manifest-head">
            <h2 class="card-title">Passengers</h2>
            <span class="trip-detail-count"><?= (int) $bookings ?> bookings &middot; <?= (int) $booked ?> seats</span>
          </div>

          <table class="manifest">
            <thead>
              <tr>
                <th>Passenger</th>
                <th>Seats</th>
                <th>Drop-off</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($manifest as $m): ?>
                <tr>
                  <td class="manifest-passenger"><?= htmlspecialchars($m['passenger']) ?></td>
                  <td><?= (int) $m['seats'] ?></td>
                  <td><?= htmlspecialchars($m['dropoff']) ?></td>
                  <td>
                    <span class="manifest-status is-<?= htmlspecialchars($m['status']) ?>">
                      <?= manifest_status_label($m['status']) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

        </section>

        <a class="btn btn-primary btn-block" href="driver-checkin.php?trip=<?= (int) $tripId ?>">Start Check-in</a>

      </div>

    </div>
  </main>
</body>

</html>