<?php
  require_once '../includes/routes.php';
  require_once '../includes/schedule.php';

  /* ------------------------------------------------------------------
     Placeholder booking.
     No bookings table exists yet, so a ticket's data is faked here to
     match the Canva mockup — the same posture as the hardcoded $date in
     booking-confirm. When the DB lands this becomes a lookup by id
     (ticket.php?booking=... -> SELECT ...), and route + slot come from
     the stored trip, exactly as trip-times reads them today.
     ------------------------------------------------------------------ */
  $booking = [
    'route_id'   => 1,                // -> find_route(): FROM / TO + fare
    'trip_id'    => 1,                // -> find_slot():  boarding time
    'date'       => '10 May 2026',
    'dropoff'    => 'Bangna Junction',
    'booking_id' => '2601134001',
    'reference'  => 'F134WD24A',
    'passenger'  => 'Jane Doe',
    'plate'      => 'กข 1234',
    'seats'      => 3,
    'booked_at'  => '10 May 2026  08:12 AM',
  ];

  $route = find_route($booking['route_id']);
  $slot  = find_slot($booking['trip_id']);

  // fare lives on the route, so the total can't drift from seats x fare
  $fare     = $route['fare'] ?? 40;
  $total    = $fare * (int) $booking['seats'];
  $boarding = $slot['time'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AU VAN - Ticket</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
    $user_role = 'passenger';
    $user_name = 'Jane Doe';
    include '../includes/navbar.php';
  ?>

  <main class="page">
    <div class="container">

      <div class="ticket">

        <div class="ticket-grid">

          <!-- LEFT: booking details -->
          <section class="card ticket-details">
            <h2 class="card-title">Booking Details</h2>

            <div class="ticket-route">
              <div class="ticket-endpoint">
                <span class="ticket-endpoint-label">FROM</span>
                <span class="ticket-endpoint-value"><?= htmlspecialchars($route['from']) ?></span>
              </div>
              <div class="ticket-endpoint">
                <span class="ticket-endpoint-label">TO</span>
                <span class="ticket-endpoint-value"><?= htmlspecialchars($route['to']) ?></span>
              </div>
            </div>

            <dl class="ticket-list">
              <div class="ticket-row">
                <dt class="ticket-label">Date</dt>
                <dd class="ticket-value"><?= htmlspecialchars($booking['date']) ?></dd>
              </div>
              <div class="ticket-row">
                <dt class="ticket-label">Drop-off</dt>
                <dd class="ticket-value"><?= htmlspecialchars($booking['dropoff']) ?></dd>
              </div>
              <div class="ticket-row">
                <dt class="ticket-label">Boarding</dt>
                <dd class="ticket-value"><?= htmlspecialchars($boarding) ?></dd>
              </div>
              <div class="ticket-row">
                <dt class="ticket-label">Passenger Name</dt>
                <dd class="ticket-value"><?= htmlspecialchars($booking['passenger']) ?></dd>
              </div>
              <div class="ticket-row">
                <dt class="ticket-label">Total Price</dt>
                <dd class="ticket-value">฿<?= number_format($total) ?></dd>
              </div>
              <div class="ticket-row">
                <dt class="ticket-label">Plate Number</dt>
                <dd class="ticket-value"><?= htmlspecialchars($booking['plate']) ?></dd>
              </div>
              <div class="ticket-row">
                <dt class="ticket-label">Seats Booked</dt>
                <dd class="ticket-value"><?= (int) $booking['seats'] ?></dd>
              </div>
              <div class="ticket-row">
                <dt class="ticket-label">Booked At</dt>
                <dd class="ticket-value"><?= htmlspecialchars($booking['booked_at']) ?></dd>
              </div>
            </dl>
          </section>

          <!-- RIGHT: QR ticket -->
          <section class="card ticket-qr">
            <h2 class="card-title">Ticket</h2>

            <div class="ticket-qr-image">
              <?php include '../includes/qrcode-mockup.php'; ?>
            </div>

            <p class="ticket-qr-caption">Scan your QR code when boarding the van</p>

            <dl class="ticket-qr-refs">
              <div class="ticket-row">
                <dt class="ticket-label">Booking ID</dt>
                <dd class="ticket-value"><?= htmlspecialchars($booking['booking_id']) ?></dd>
              </div>
              <div class="ticket-row">
                <dt class="ticket-label">Reference Number</dt>
                <dd class="ticket-value"><?= htmlspecialchars($booking['reference']) ?></dd>
              </div>
            </dl>
          </section>

        </div>

        <div class="btn-row">
          <a href="index.php" class="btn btn-secondary">Back to Home</a>
        </div>

      </div>

    </div>
  </main>
</body>

</html>