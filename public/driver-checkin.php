<?php
  require_once '../includes/routes.php';
  require_once '../includes/driver-today.php';
  require_once '../includes/auth.php';

  $user = require_role('driver');

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
  $date  = today_label();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      require_once '../includes/bookings.php';

      $reference = trim($_POST['reference'] ?? '');
      $booking   = $reference !== '' ? find_booking($reference) : null;

      $checkinError = match (true) {
          $reference === ''                          => 'empty',
          $booking === null                           => 'notfound',
          $booking['trip_id'] !== $tripId              => 'wrongtrip',
          $booking['board_status'] === 'boarded'       => 'already',
          default                                      => null,
      };

      if ($checkinError === null) {
          $stmt = db()->prepare("UPDATE bookings SET board_status = 'boarded' WHERE id = ?");
          $stmt->bind_param('i', $booking['booking_id']);
          $stmt->execute();

          header('Location: driver-checkin.php?trip=' . $tripId . '&checked_in=' . urlencode($reference), true, 303);
          exit;
      }

      header('Location: driver-checkin.php?trip=' . $tripId
             . '&error=' . $checkinError . '&ref=' . urlencode($reference), true, 303);
      exit;
  }

  $checkinErrorText = [
      'empty'     => 'Enter a booking reference.',
      'notfound'  => 'No booking found with that reference.',
      'wrongtrip' => "That booking isn't on this trip.",
      'already'   => 'That booking has already been checked in.',
  ][$_GET['error'] ?? ''] ?? null;
  $refValue   = $_GET['ref'] ?? '';
  $justBoarded = $_GET['checked_in'] ?? null;

  $manifest = get_trip_manifest($tripId);
  $capacity = $trip['capacity'];
  $boarded  = array_sum(array_map(
      fn($m) => $m['status'] === 'boarded' ? $m['seats'] : 0,
      $manifest
  ));
  $percent  = $capacity > 0 ? round($boarded / $capacity * 100) : 0;

  $recently_boarded = array_values(array_filter($manifest, fn($m) => $m['status'] === 'boarded'));

  $page_title = 'AU VAN - Check-in';
  $user_role  = 'driver';
  $user_name  = $user['name'];
  include '../includes/header.php';
?>

  <main class="page">
    <div class="container">

      <div class="checkin">

        <a class="checkin-back" href="driver-trip.php?trip=<?= (int) $tripId ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               aria-hidden="true" focusable="false">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Trip Details
        </a>

        <div class="checkin-head">
          <h2>Check in &middot; <?= htmlspecialchars($from) ?> &rarr; <?= htmlspecialchars($to) ?></h2>
          <p class="checkin-sub"><?= htmlspecialchars($date) ?> &middot; <?= htmlspecialchars($trip['time']) ?></p>
        </div>

        <?php if ($justBoarded !== null): ?>
          <p class="auth-error checkin-success">Checked in: <?= htmlspecialchars($justBoarded) ?></p>
        <?php elseif ($checkinErrorText !== null): ?>
          <p class="auth-error"><?= htmlspecialchars($checkinErrorText) ?></p>
        <?php endif; ?>

        <div class="checkin-grid">

          <div class="checkin-main">

            <section class="card checkin-scan">
              <span class="checkin-scan-frame" aria-hidden="true">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     focusable="false">
                  <path d="M6 16 V8 a2 2 0 0 1 2-2 h8"></path>
                  <path d="M42 16 V8 a2 2 0 0 0 -2-2 h-8"></path>
                  <path d="M6 32 v8 a2 2 0 0 0 2 2 h8"></path>
                  <path d="M42 32 v8 a2 2 0 0 0 -2 2 h-8"></path>
                  <line x1="6" y1="24" x2="42" y2="24"></line>
                </svg>
              </span>
              <p class="checkin-scan-caption">Scan to Check in</p>
            </section>

            <form class="card checkin-manual" method="POST" action="driver-checkin.php?trip=<?= (int) $tripId ?>">
              <h2 class="card-title">Enter Booking Reference Manually</h2>
              <div class="field">
                <label class="field-label" for="reference">Booking ref</label>
                <input class="input" type="text" id="reference" name="reference"
                       value="<?= htmlspecialchars($refValue) ?>"
                       placeholder="e.g. B31FRE3D" autocomplete="off">
              </div>
              <button class="btn btn-primary btn-block" type="submit">Check in</button>
            </form>

          </div>

          <aside class="checkin-side">

            <section class="card checkin-progress">
              <span class="checkin-progress-value"><?= sprintf('%02d', $boarded) ?> / <?= (int) $capacity ?></span>
              <span class="checkin-progress-label">boarded</span>
              <span class="checkin-bar">
                <span class="checkin-bar-fill" style="width: <?= (int) $percent ?>%;"></span>
              </span>
            </section>

            <section class="card checkin-recent">
              <h2 class="card-title">Recently Boarded</h2>
              <?php if ($recently_boarded): ?>
                <ul class="checkin-recent-list">
                  <?php foreach ($recently_boarded as $r): ?>
                    <li class="checkin-recent-item">
                      <span class="checkin-recent-info">
                        <span class="checkin-recent-name"><?= htmlspecialchars($r['passenger']) ?></span>
                        <span class="checkin-recent-meta"><?= (int) $r['seats'] ?> seats &middot; <?= htmlspecialchars($r['reference']) ?></span>
                      </span>
                      <span class="manifest-status is-boarded">Boarded</span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p class="bookings-empty">No one boarded yet.</p>
              <?php endif; ?>
            </section>

          </aside>

        </div>

      </div>

    </div>
  </main>

<?php include '../includes/footer.php'; ?>