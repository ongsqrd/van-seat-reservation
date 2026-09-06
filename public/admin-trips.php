<?php
  require_once '../includes/routes.php';

  $date = '10 May 2026';

  /* ------------------------------------------------------------------
     Placeholder scheduled trips. Coherent with the dashboard: 6 trips,
     2 without a driver. Each references a route by id (From/To resolve
     through find_route). driver = null means Unassigned.
     ------------------------------------------------------------------ */
  $trips = [
      ['id' => 1, 'time' => '08 : 00 AM', 'route_id' => 1, 'driver' => 'Sherlock H.'],
      ['id' => 2, 'time' => '10 : 00 AM', 'route_id' => 4, 'driver' => 'John Doe'],
      ['id' => 3, 'time' => '12 : 00 PM', 'route_id' => 3, 'driver' => 'Sherlock H.'],
      ['id' => 4, 'time' => '01 : 30 PM', 'route_id' => 2, 'driver' => null],
      ['id' => 5, 'time' => '03 : 00 PM', 'route_id' => 1, 'driver' => 'Molly E.'],
      ['id' => 6, 'time' => '04 : 30 PM', 'route_id' => 2, 'driver' => null],
  ];

  // drivers offered in the assign popup
  $drivers = [
      ['name' => 'James M.', 'status' => 'Free',     'available' => true],
      ['name' => 'Molly E.', 'status' => 'Free',     'available' => true],
      ['name' => 'John Doe', 'status' => 'Driving',  'available' => false],
      ['name' => 'Bob B.',   'status' => 'Assigned', 'available' => false],
  ];

  $routes = get_routes();
  $vans   = ['กข 1234', 'พส 6767'];

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

  $page_title = 'AU VAN - Trips';
  $user_role  = 'admin';
  $user_name  = 'Chanyapat Saeng-Xuto';
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
            <div class="trip-create-grid">
              <div class="field">
                <label class="field-label" for="route">Route</label>
                <select class="input" id="route" name="route">
                  <?php foreach ($routes as $r): ?>
                    <option><?= htmlspecialchars($r['from']) ?> &rarr; <?= htmlspecialchars($r['to']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field">
                <label class="field-label" for="van">Van</label>
                <select class="input" id="van" name="van">
                  <?php foreach ($vans as $plate): ?>
                    <option><?= htmlspecialchars($plate) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field">
                <label class="field-label" for="date">Date</label>
                <input class="input" type="date" id="date" name="date">
              </div>
              <div class="field">
                <label class="field-label" for="time">Time</label>
                <input class="input" type="time" id="time" name="time">
              </div>
              <div class="field trip-create-driver">
                <label class="field-label" for="driver">Driver <span class="field-hint">(optional)</span></label>
                <select class="input" id="driver" name="driver">
                  <option>Unassigned</option>
                  <?php foreach ($drivers as $d): if ($d['available']): ?>
                    <option><?= htmlspecialchars($d['name']) ?></option>
                  <?php endif; endforeach; ?>
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
                      <input type="radio" name="driver" value="<?= htmlspecialchars($d['name']) ?>"
                             <?= $assignTrip['driver'] === $d['name'] ? 'checked' : '' ?>>
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