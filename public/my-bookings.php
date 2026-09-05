<?php
  require_once '../includes/routes.php';
  require_once '../includes/schedule.php';
  require_once '../includes/bookings.php';

  // split by status so each tab renders its own list
  $upcoming  = [];
  $completed = [];
  foreach (get_bookings() as $b) {
      if ($b['status'] === 'completed') {
          $completed[] = $b;
      } else {
          $upcoming[] = $b;
      }
  }

  // one card, rendered the same way in both tabs.
  // route + time come from the includes so they can't drift from the trip.
  function render_booking_card(array $b): void
  {
      $route = find_route($b['route_id']);
      $slot  = find_slot($b['trip_id']);

      $from  = $route['from'] ?? '';
      $to    = $route['to']   ?? '';
      $time  = $slot['time']  ?? '';
      $seats = (int) $b['seats'];
  ?>
    <li class="booking-card">
      <div class="booking-info">

        <div class="booking-route">
          <span class="booking-endpoint">
            <span class="booking-label">From</span>
            <span class="booking-place"><?= htmlspecialchars($from) ?></span>
          </span>

          <span class="booking-sep" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 focusable="false">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </span>

          <span class="booking-endpoint">
            <span class="booking-label">To</span>
            <span class="booking-place"><?= htmlspecialchars($to) ?></span>
          </span>
        </div>

        <p class="booking-meta">
          <span class="booking-meta-label">Date</span>
          <?= htmlspecialchars($b['date']) ?> &middot; <?= htmlspecialchars($time) ?> &middot; <?= $seats ?> <?= $seats === 1 ? 'seat' : 'seats' ?>
        </p>

        <p class="booking-ref">Booking Ref: <strong><?= htmlspecialchars($b['reference']) ?></strong></p>

      </div>

      <a class="btn btn-primary btn-sm" href="ticket.php?booking=<?= urlencode($b['reference']) ?>">View Ticket</a>
    </li>
  <?php
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AU VAN - History</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
    $user_role = 'passenger';
    $user_name = 'Jane Doe';
    include '../includes/navbar.php';
  ?>

  <main class="page">
    <div class="container container-mid">

      <div class="bookings">

        <div class="bookings-header">
          <h2>Booking History</h2>
        </div>

        <div class="bookings-tabs">
          <label class="bookings-tab">
            <input type="radio" name="booking-tab" value="upcoming" checked>
            Upcoming
          </label>
          <label class="bookings-tab">
            <input type="radio" name="booking-tab" value="completed">
            Completed
          </label>
        </div>

        <div class="bookings-panel bookings-panel-upcoming">
          <?php if ($upcoming): ?>
            <ul class="booking-list">
              <?php foreach ($upcoming as $b) render_booking_card($b); ?>
            </ul>
          <?php else: ?>
            <p class="bookings-empty">No upcoming trips.</p>
          <?php endif; ?>
        </div>

        <div class="bookings-panel bookings-panel-completed">
          <?php if ($completed): ?>
            <ul class="booking-list">
              <?php foreach ($completed as $b) render_booking_card($b); ?>
            </ul>
          <?php else: ?>
            <p class="bookings-empty">No completed trips.</p>
          <?php endif; ?>
        </div>

      </div>

    </div>
  </main>
</body>

</html>