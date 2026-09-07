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
     Placeholder check-in state. Real scanning is out of scope (mock),
     so the scanner below is decorative; manual entry is the working
     path. 'boarded' is seats checked in of the van's capacity. Recently
     Boarded is a short activity feed, not the full boarded list.
     ------------------------------------------------------------------ */
  $capacity = 15;
  $boarded  = 4;
  $percent  = $capacity > 0 ? round($boarded / $capacity * 100) : 0;

  $recently_boarded = [
      ['passenger' => 'Jane Doe', 'seats' => 3, 'reference' => 'F134WD24A'],
  ];

  $page_title = 'AU VAN - Check-in';
  $user_role  = 'driver';
  $user_name = 'Patchara Chainiyom';
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
            </section>

          </aside>

        </div>

      </div>

    </div>
  </main>

<?php include '../includes/footer.php'; ?>