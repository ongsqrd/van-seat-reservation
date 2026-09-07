<?php
  /*
   * booking-success.php
   *
   * The no-JS fallback target of the booking-confirm form. With JS on,
   * booking-confirm.js intercepts the submit and shows the confirmed
   * dialog instead, so this page is only reached when JS is off or the
   * showModal feature check fails.
   *
   * When the DB lands this also becomes the POST-redirect-GET target:
   * booking-confirm inserts the booking, then redirects here with the
   * real id — which fixes the reload-resubmits-the-booking problem.
   * For now the id is a placeholder, matching the mockup.
   */
  require_once '../includes/auth.php';
  $user = require_role('passenger');

  $booking_id = 'QWSE-00012';

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

        <p class="success-id">Booking ID: <strong><?= htmlspecialchars($booking_id) ?></strong></p>

        <div class="success-actions">
          <a class="btn btn-primary btn-block" href="ticket.php?booking=<?= urlencode($booking_id) ?>">View Ticket</a>
          <a class="btn btn-secondary btn-block" href="index.php">Back to Home</a>
        </div>

      </div>

    </div>
  </main>

<?php include '../includes/footer.php'; ?>