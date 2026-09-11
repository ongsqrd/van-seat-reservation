<?php
  require_once '../includes/routes.php';
  require_once '../includes/bookings.php';
  require_once '../includes/auth.php';

  $user = require_role('passenger');

  $reference = $_GET['booking'] ?? null;
  $found     = $reference !== null ? find_booking($reference) : null;

  if ($found === null || $found['user_id'] !== $user['id']) {
      header('Location: my-bookings.php');
      exit;
  }

  $route = find_route($found['route_id']);

  $booking = [
    'date'       => $found['date'],
    'dropoff'    => $found['dropoff'],
    'booking_id' => (string) $found['booking_id'],
    'reference'  => $found['reference'],
    'passenger'  => $found['passenger'],
    'plate'      => $found['plate'],
    'seats'      => $found['seats'],
    'booked_at'  => $found['booked_at'],
  ];

  $boarding = $found['boarding'];
  $total    = $found['total'];   

  $page_title = 'AU VAN - Ticket';
  $user_role  = 'passenger';
  $user_name  = $user['name'];
  include '../includes/header.php';
?>

  <main class="page">
    <div class="container">

      <div class="ticket">

        <div class="ticket-grid">

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

<?php include '../includes/footer.php'; ?>