<?php
  require_once '../includes/routes.php';
  require_once '../includes/vans.php';
  require_once '../includes/admin-today.php';
  require_once '../includes/auth.php';

  $user = require_role('admin');

  $date  = today_label();
  $routes = get_routes();
  $vans   = get_vans();

  /* ------------------------------------------------------------------
     Two forms POST here: the create-trip form and the assign-driver
     form (which now carries a hidden trip_id — the original placeholder
     markup never did, so there was nothing to tell the server which
     trip was being assigned). $_POST['action'] only appears on the
     assign form, so its presence is what tells the two apart.
     ------------------------------------------------------------------ */
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      if (isset($_POST['action'])) {
          // --- assign or remove a driver on an existing trip ---
          $tripId = (int) ($_POST['trip_id'] ?? 0);
          $trips  = get_admin_trips();
          $trip   = null;
          foreach ($trips as $t) {
              if ($t['id'] === $tripId) { $trip = $t; break; }
          }
          if ($trip === null) {
              header('Location: admin-trips.php', true, 303);
              exit;
          }

          if ($_POST['action'] === 'remove') {
              $stmt = db()->prepare('UPDATE trips SET driver_id = NULL WHERE id = ?');
              $stmt->bind_param('i', $tripId);
              $stmt->execute();
          } else {
              $driverId = (int) ($_POST['driver'] ?? 0);

              // never trust the client — the UI already prevents selecting
              // an unavailable driver, but re-check server-side too
              $available = get_available_drivers(today_iso(), $trip['raw_time'], $tripId);
              $isFree    = false;
              foreach ($available as $d) {
                  if ($d['id'] === $driverId && $d['available']) { $isFree = true; break; }
              }

              if ($isFree) {
                  $stmt = db()->prepare('UPDATE trips SET driver_id = ? WHERE id = ?');
                  $stmt->bind_param('ii', $driverId, $tripId);
                  $stmt->execute();
              }
              // if not free (stale popup, race condition), just fall through
              // to the redirect below without writing anything
          }

          header('Location: admin-trips.php', true, 303);
          exit;
      }

      // --- create a new trip ---
      $newRouteId = (int) ($_POST['route'] ?? 0);
      $newVanId   = (int) ($_POST['van']   ?? 0);
      $newDate    = $_POST['date'] ?? '';
      $newTime    = $_POST['time'] ?? '';
      $newDriverId = (int) ($_POST['driver'] ?? 0);   // 0 = Unassigned

      $createError = match (true) {
          !isset($routes[$newRouteId])                      => 'route',
          !isset($vans[$newVanId])                           => 'van',
          !preg_match('/^\d{4}-\d{2}-\d{2}$/', $newDate)     => 'date',
          !preg_match('/^\d{2}:\d{2}$/', $newTime)           => 'time',
          default                                             => null,
      };

      if ($createError === null) {
          $driverParam = $newDriverId > 0 ? $newDriverId : null;
          $stmt = db()->prepare(
              'INSERT INTO trips (route_id, van_id, driver_id, trip_date, depart_time) VALUES (?, ?, ?, ?, ?)'
          );
          $stmt->bind_param('iiiss', $newRouteId, $newVanId, $driverParam, $newDate, $newTime);
          $stmt->execute();

          header('Location: admin-trips.php', true, 303);
          exit;
      }

      header('Location: admin-trips.php?new=1&error=' . $createError, true, 303);
      exit;
  }

  $createErrorText = [
      'route' => 'Choose a valid route.',
      'van'   => 'Choose a valid van.',
      'date'  => 'Enter a valid date.',
      'time'  => 'Enter a valid time.',
  ][$_GET['error'] ?? ''] ?? null;

  $trips = get_admin_trips();

  // server-side state, same idiom as profile ?edit / manage ?tab
  $creating = isset($_GET['new']);

  $assignId   = isset($_GET['assign']) ? (int) $_GET['assign'] : 0;
  $assignTrip = null;
  foreach ($trips as $t) {
      if ($t['id'] === $assignId) {
          $assignTrip = $t;
          break;
      }
  }
  // drivers offered in the assign popup — conflict-checked against this
  // trip's actual date + time, excluding the trip itself
  $drivers = $assignTrip !== null
      ? get_available_drivers(today_iso(), $assignTrip['raw_time'], $assignTrip['id'])
      : [];

  // all drivers, for the create-trip form's optional driver picker (no
  // conflict-filtering there — the date/time are still free text at that
  // point, so "available for this slot" isn't yet a meaningful question)
  $allDrivers = [];
  $result = db()->query("SELECT id, name FROM users WHERE role = 'driver' ORDER BY name");
  while ($row = $result->fetch_assoc()) {
      $allDrivers[] = ['id' => (int) $row['id'], 'name' => $row['name']];
  }

  $page_title = 'AU VAN - Trips';
  $user_role  = 'admin';
  $user_name  = $user['name'];
  include '../includes/header.php';
?>

  <main class="page">
    <div class="container">

      <div class="trips-admin">

        <div class="trips-admin-head">
          <div>
            <h2>Trips</h2>
            <p class="trips-admin-count"><?= count($trips) ?> scheduled</p>
          </div>
          <a class="btn btn-primary btn-sm" href="admin-trips.php?new=1">+ New Trip</a>
        </div>

        <?php if ($creating): ?>
          <form class="card trip-create" method="POST" action="admin-trips.php">
            <h2 class="card-title">New Trip</h2>

            <?php if ($createErrorText !== null): ?>
              <p class="auth-error"><?= htmlspecialchars($createErrorText) ?></p>
            <?php endif; ?>

            <div class="trip-create-grid">
              <div class="field">
                <label class="field-label" for="route">Route</label>
                <select class="input" id="route" name="route">
                  <?php foreach ($routes as $rid => $r): ?>
                    <option value="<?= (int) $rid ?>"><?= htmlspecialchars($r['from']) ?> &rarr; <?= htmlspecialchars($r['to']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field">
                <label class="field-label" for="van">Van</label>
                <select class="input" id="van" name="van">
                  <?php foreach ($vans as $vid => $v): ?>
                    <option value="<?= (int) $vid ?>"><?= htmlspecialchars($v['plate']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field">
                <label class="field-label" for="date">Date</label>
                <input class="input" type="date" id="date" name="date" value="<?= htmlspecialchars(today_iso()) ?>">
              </div>
              <div class="field">
                <label class="field-label" for="time">Time</label>
                <input class="input" type="time" id="time" name="time">
              </div>
              <div class="field trip-create-driver">
                <label class="field-label" for="driver">Driver <span class="field-hint">(optional)</span></label>
                <select class="input" id="driver" name="driver">
                  <option value="0">Unassigned</option>
                  <?php foreach ($allDrivers as $d): ?>
                    <option value="<?= (int) $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="btn-row">
              <a href="admin-trips.php" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Create Trip</button>
            </div>
          </form>
        <?php endif; ?>

        <table class="data-table">
          <thead>
            <tr><th>Time</th><th>Route</th><th>Driver</th><th class="data-edit-col">Edit</th></tr>
          </thead>
          <tbody>
            <?php foreach ($trips as $t):
              $route = find_route($t['route_id']);
            ?>
              <tr>
                <td class="data-strong"><?= htmlspecialchars($t['time']) ?></td>
                <td><?= htmlspecialchars($route['from'] ?? '') ?> &rarr; <?= htmlspecialchars($route['to'] ?? '') ?></td>
                <td>
                  <?php if ($t['driver'] !== null): ?>
                    <?= htmlspecialchars($t['driver']) ?>
                  <?php else: ?>
                    <span class="trip-unassigned">Unassigned</span>
                  <?php endif; ?>
                </td>
                <td class="data-edit-col">
                  <a class="data-edit" href="admin-trips.php?assign=<?= (int) $t['id'] ?>" aria-label="Assign driver">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" focusable="false">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path>
                    </svg>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>

    </div>

    <?php if ($assignTrip !== null):
      $assignRoute = find_route($assignTrip['route_id']);
    ?>
      <div class="trip-overlay">
        <form class="card trip-overlay-card" method="POST" action="admin-trips.php">
          <input type="hidden" name="trip_id" value="<?= (int) $assignTrip['id'] ?>">

          <div class="trip-overlay-head">
            <h2 class="card-title">Assign Driver</h2>
            <p class="trip-overlay-sub">
              <?= htmlspecialchars($assignRoute['from'] ?? '') ?> &rarr; <?= htmlspecialchars($assignRoute['to'] ?? '') ?>
              &middot; <?= htmlspecialchars($assignTrip['time']) ?> &middot; <?= htmlspecialchars($date) ?>
            </p>
          </div>

          <div class="trip-drivers">
            <div class="trip-drivers-group">
              <span class="trip-drivers-label">Available</span>
              <ul class="trip-drivers-list">
                <?php foreach ($drivers as $d): if ($d['available']): ?>
                  <li>
                    <label class="trip-driver">
                      <input type="radio" name="driver" value="<?= (int) $d['id'] ?>"
                             <?= $assignTrip['driver_id'] === $d['id'] ? 'checked' : '' ?>>
                      <span class="trip-driver-name"><?= htmlspecialchars($d['name']) ?></span>
                      <span class="trip-driver-status is-free"><?= htmlspecialchars($d['status']) ?></span>
                    </label>
                  </li>
                <?php endif; endforeach; ?>
              </ul>
            </div>

            <div class="trip-drivers-group">
              <span class="trip-drivers-label">Unavailable</span>
              <ul class="trip-drivers-list">
                <?php foreach ($drivers as $d): if (!$d['available']): ?>
                  <li>
                    <div class="trip-driver is-disabled">
                      <span class="trip-driver-name"><?= htmlspecialchars($d['name']) ?></span>
                      <span class="trip-driver-status is-busy"><?= htmlspecialchars($d['status']) ?></span>
                    </div>
                  </li>
                <?php endif; endforeach; ?>
              </ul>
            </div>
          </div>

          <div class="trip-overlay-actions">
            <a href="admin-trips.php" class="btn btn-secondary">Cancel</a>
            <?php if ($assignTrip['driver'] !== null): ?>
              <button type="submit" name="action" value="remove" class="btn btn-secondary">Remove</button>
            <?php endif; ?>
            <button type="submit" name="action" value="assign" class="btn btn-primary">Assign</button>
          </div>
        </form>
      </div>
    <?php endif; ?>

  </main>

<?php include '../includes/footer.php'; ?>