<?php
  require_once '../includes/routes.php';
  require_once '../includes/schedule.php';
  require_once '../includes/bookings.php';
  require_once '../includes/auth.php';

  $user = require_role('passenger');

  // --- what trip-times.php posted ---
  $routeId    = isset($_POST['route_id'])     ? (int) $_POST['route_id']     : 0;
  $tripId     = isset($_POST['trip_id'])      ? (int) $_POST['trip_id']      : 0;
  $seats      = isset($_POST['numPassenger']) ? (int) $_POST['numPassenger'] : 0;
  $dropoffKey = $_POST['dropoff'] ?? '';

  // shared with booking-success.php's commit step, so review and commit
  // can never validate a booking request differently
  $valid = validate_booking_request($routeId, $tripId, $dropoffKey, $seats);

  // nothing to confirm without a valid trip — send them back to the start
  if ($valid === null) {
      header('Location: trips.php');
      exit;
  }
  ['route' => $route, 'slot' => $slot, 'dropoff' => $dropoff] = $valid;

  $fare  = $route['fare'];
  $total = $fare * $seats;

  // this is a REVIEW step only — nothing is booked yet. Submitting the
  // form below is a normal browser POST to booking-success.php, which
  // validates again, inserts, and redirects (POST-redirect-GET) to the
  // real confirmation screen — no JS involved.
  $date = $slot['date'];

  $passengerName = $user['name'];

  $page_title = 'AU VAN - Booking';
  $user_role  = 'passenger';
  $user_name  = $passengerName;
  include '../includes/header.php';
?>

  <main class="page">
    <div class="container container-wide">

      <form class="confirm-form" action="booking-success.php" method="POST">

        <input type="hidden" name="route_id" value="<?= (int) $routeId ?>">
        <input type="hidden" name="trip_id" value="<?= (int) $tripId ?>">
        <input type="hidden" name="numPassenger" value="<?= (int) $seats ?>">
        <input type="hidden" name="dropoff" value="<?= htmlspecialchars($dropoffKey) ?>">

        <div class="confirm-grid">

          <!-- ---------- booking summary ---------- -->
          <section class="card">
            <h3 class="card-title">Booking Summary</h3>

            <div class="summary-route">
              <div>
                <span class="summary-label">FROM</span>
                <span class="summary-endpoint"><?= htmlspecialchars($route['from']) ?></span>
              </div>
              <div>
                <span class="summary-label">TO</span>
                <span class="summary-endpoint"><?= htmlspecialchars($route['to']) ?></span>
              </div>
            </div>

            <div class="summary-list">
              <div class="summary-row">
                <span class="summary-label">Date</span>
                <span class="summary-value"><?= htmlspecialchars($date) ?></span>
              </div>
              <div class="summary-row">
                <span class="summary-label">Time</span>
                <span class="summary-value"><?= htmlspecialchars($slot['time']) ?></span>
              </div>
              <div class="summary-row">
                <span class="summary-label">Drop-off</span>
                <span class="summary-value"><?= htmlspecialchars($dropoff) ?></span>
              </div>
              <div class="summary-row">
                <span class="summary-label">Passenger Name</span>
                <span class="summary-value"><?= htmlspecialchars($passengerName) ?></span>
              </div>
              <div class="summary-row">
                <span class="summary-label">Seats</span>
                <span class="summary-value"><?= (int) $seats ?></span>
              </div>
            </div>
          </section>

          <!-- ---------- payment ---------- -->
          <section class="card payment">
            <h3 class="card-title">Payment</h3>

            <div class="pay-total">
              <span class="pay-rate">&#3647;<?= (int) $fare ?> x <?= (int) $seats ?> seats</span>
              <span class="pay-amount">&#3647;<?= (int) $total ?></span>
            </div>

            <p class="pay-note">
              Please confirm your payment in 15 minutes to secure your booking
            </p>

            <span class="field-label">PAYMENT METHOD</span>
            <div class="pay-methods">
              <label class="pay-method">
                <input type="radio" name="method" value="promptpay" checked>
                <span>PromptPay</span>
              </label>
              <label class="pay-method">
                <input type="radio" name="method" value="card">
                <span>Card</span>
              </label>
            </div>

          </section>

          <!-- ---------- chosen method's details ---------- -->
          <section class="card pay-detail">

            <!-- promptpay -->
            <div class="pay-panel pay-panel-promptpay">
              <div class="summary-row">
                <span class="summary-label">Account Name</span>
                <span class="summary-value">Jeremy Johnson</span>
              </div>
              <div class="summary-row">
                <span class="summary-label">Account No.</span>
                <span class="summary-value">12-3454-1231-32</span>
              </div>

              <div class="qr-box">
                <span class="qr-note">Scan to Pay through PromptPay</span>
              </div>

              <div class="pay-actions">
                <button type="button" class="btn btn-secondary btn-sm btn-block">Save to Device</button>
                <button type="button" class="btn btn-secondary btn-sm btn-block">Upload Payment Slip</button>
              </div>
            </div>

            <!-- card -->
            <div class="pay-panel pay-panel-card">
              <span class="pay-panel-title">Add your card details</span>

              <div class="field">
                <label class="field-label" for="cardName">Card Holder's Name</label>
                <input class="input" type="text" id="cardName" name="cardName" autocomplete="off">
              </div>

              <div class="field">
                <label class="field-label" for="cardNumber">Card Number</label>
                <input class="input" type="text" id="cardNumber" name="cardNumber"
                       inputmode="numeric" autocomplete="off">
              </div>

              <div class="pay-card-line">
                <div class="field">
                  <label class="field-label" for="cardCvv">CVV/ CVC</label>
                  <input class="input" type="text" id="cardCvv" name="cardCvv"
                         inputmode="numeric" autocomplete="off">
                </div>
                <div class="field">
                  <label class="field-label" for="cardExpiry">Expiry</label>
                  <input class="input" type="text" id="cardExpiry" name="cardExpiry"
                         placeholder="MM/YY" autocomplete="off">
                </div>
              </div>
            </div>
          </section>

        </div>

        <div class="btn-row">
          <a href="trips.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Confirm Booking</button>
        </div>

      </form>

    </div>
  </main>

<?php include '../includes/footer.php'; ?>