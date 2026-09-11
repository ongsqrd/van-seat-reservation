<?php

require_once '../includes/routes.php';
require_once '../includes/schedule.php';
require_once '../includes/bookings.php';
require_once '../includes/auth.php';

$user = require_role('passenger');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $routeId    = isset($_POST['route_id'])     ? (int) $_POST['route_id']     : 0;
    $tripId     = isset($_POST['trip_id'])      ? (int) $_POST['trip_id']      : 0;
    $seats      = isset($_POST['numPassenger']) ? (int) $_POST['numPassenger'] : 0;
    $dropoffKey = $_POST['dropoff'] ?? '';
    $method     = ($_POST['method'] ?? 'promptpay') === 'card' ? 'card' : 'promptpay';

    $valid = validate_booking_request($routeId, $tripId, $dropoffKey, $seats);
    if ($valid === null) {
        header('Location: trips.php', true, 303);
        exit;
    }

    $total     = $valid['route']['fare'] * $seats;
    $reference = generate_booking_reference();
    $dropoffStopId = (int) $dropoffKey;   

    $stmt = db()->prepare("
        INSERT INTO bookings
            (reference, trip_id, user_id, dropoff_stop_id, seats, payment_method, total)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('siiiisi', $reference, $tripId, $user['id'], $dropoffStopId, $seats, $method, $total);
    $stmt->execute();

    header('Location: booking-success.php?ref=' . urlencode($reference), true, 303);
    exit;
}

$reference = $_GET['ref'] ?? null;
$booking   = $reference !== null ? find_booking($reference) : null;

if ($booking === null || $booking['user_id'] !== $user['id']) {
    header('Location: trips.php', true, 303);
    exit;
}

$page_title = 'AU VAN - Confirmed';
$user_role  = 'passenger';
$user_name  = $user['name'];
include '../includes/header.php';
?>

  <main class="success-page">
    <div class="container container-narrow">

      <div class="success">

        <span class="success-check" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
               focusable="false">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </span>

        <h2>Booking Confirmed</h2>
        <p class="success-message">Your trip has been successfully booked</p>

        <p class="success-id">Reference Number: <strong><?= htmlspecialchars($booking['reference']) ?></strong></p>

        <div class="success-actions">
          <a class="btn btn-primary btn-block" href="ticket.php?booking=<?= urlencode($booking['reference']) ?>">View Ticket</a>
          <a class="btn btn-secondary btn-block" href="index.php">Back to Home</a>
        </div>

      </div>

    </div>
  </main>

<?php include '../includes/footer.php'; ?>