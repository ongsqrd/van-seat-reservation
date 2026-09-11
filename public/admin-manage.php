<?php
  require_once '../includes/routes.php';
  require_once '../includes/vans.php';
  require_once '../includes/auth.php';

  $user = require_role('admin');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      switch ($_POST['do'] ?? '') {

          case 'add_van':
              $plate = trim($_POST['plate'] ?? '');
              $seats = (int) ($_POST['seats'] ?? 0);

              $vanError = match (true) {
                  $plate === ''             => 'plate',
                  $seats < 1 || $seats > 50 => 'seats',
                  default                    => null,
              };

              if ($vanError === null) {
                  $stmt = db()->prepare('INSERT INTO vans (plate, seats) VALUES (?, ?)');
                  $stmt->bind_param('si', $plate, $seats);
                  $stmt->execute();
                  header('Location: admin-manage.php?tab=vans', true, 303);
                  exit;
              }
              header('Location: admin-manage.php?tab=vans&error=' . $vanError, true, 303);
              exit;

          case 'edit_van':
              $vanId = (int) ($_POST['id'] ?? 0);
              $plate = trim($_POST['plate'] ?? '');
              $seats = (int) ($_POST['seats'] ?? 0);

              $vanError = match (true) {
                  find_van($vanId) === null => 'notfound',
                  $plate === ''             => 'plate',
                  $seats < 1 || $seats > 50 => 'seats',
                  default                    => null,
              };

              if ($vanError === null) {
                  $stmt = db()->prepare('UPDATE vans SET plate = ?, seats = ? WHERE id = ?');
                  $stmt->bind_param('sii', $plate, $seats, $vanId);
                  $stmt->execute();
                  header('Location: admin-manage.php?tab=vans', true, 303);
                  exit;
              }
              header('Location: admin-manage.php?tab=vans&edit_van=' . $vanId . '&error=' . $vanError, true, 303);
              exit;

          case 'add_route':
              $name       = trim($_POST['name'] ?? '');
              $originId   = (int) ($_POST['origin_stop']   ?? 0);
              $terminusId = (int) ($_POST['terminus_stop'] ?? 0);
              $fare       = (int) ($_POST['fare'] ?? 0);
              $validStopIds = array_keys(get_dropoffs(null));

              $routeError = match (true) {
                  $name === ''  => 'name',
                  !in_array($originId, $validStopIds, true)
                    || !in_array($terminusId, $validStopIds, true)
                    || $originId === $terminusId
                                => 'stops',
                  $fare < 1     => 'fare',
                  default        => null,
              };

              if ($routeError === null) {
                  $stmt = db()->prepare('INSERT INTO routes (name, fare) VALUES (?, ?)');
                  $stmt->bind_param('si', $name, $fare);
                  $stmt->execute();
                  $newRouteId = (int) db()->insert_id;

                  $stmt2 = db()->prepare(
                      'INSERT INTO route_stops (route_id, stop_id, seq) VALUES (?, ?, 1), (?, ?, 2)'
                  );
                  $stmt2->bind_param('iiii', $newRouteId, $originId, $newRouteId, $terminusId);
                  $stmt2->execute();

                  header('Location: admin-manage.php?tab=routes', true, 303);
                  exit;
              }
              header('Location: admin-manage.php?tab=routes&error=' . $routeError, true, 303);
              exit;

          case 'edit_route':
              $routeId = (int) ($_POST['id'] ?? 0);
              $name    = trim($_POST['name'] ?? '');
              $fare    = (int) ($_POST['fare'] ?? 0);

              $routeError = match (true) {
                  find_route($routeId) === null => 'notfound',
                  $name === ''                    => 'name',
                  $fare < 1                       => 'fare',
                  default                          => null,
              };

              if ($routeError === null) {
                  $stmt = db()->prepare('UPDATE routes SET name = ?, fare = ? WHERE id = ?');
                  $stmt->bind_param('sii', $name, $fare, $routeId);
                  $stmt->execute();
                  header('Location: admin-manage.php?tab=routes', true, 303);
                  exit;
              }
              header('Location: admin-manage.php?tab=routes&edit_route=' . $routeId . '&error=' . $routeError, true, 303);
              exit;

          case 'add_stop':
              $stopRouteId = (int) ($_POST['route_id'] ?? 0);
              $stopName    = trim($_POST['stop'] ?? '');
              $validRoutes = get_routes();

              $stopError = match (true) {
                  !isset($validRoutes[$stopRouteId]) => 'route',
                  $stopName === ''                    => 'name',
                  default                              => null,
              };

              if ($stopError === null) {
                  $chain = get_route_stop_chain($stopRouteId);
                  $existingNames = array_column($chain, 'name');

                  if (in_array($stopName, $existingNames, true)) {
                      header('Location: admin-manage.php?tab=stops&route=' . $stopRouteId . '&error=duplicate', true, 303);
                      exit;
                  }

                  $stopId  = find_or_create_stop($stopName);
                  $nextSeq = count($chain) + 1;
                  $stmt = db()->prepare('INSERT INTO route_stops (route_id, stop_id, seq) VALUES (?, ?, ?)');
                  $stmt->bind_param('iii', $stopRouteId, $stopId, $nextSeq);
                  $stmt->execute();

                  header('Location: admin-manage.php?tab=stops&route=' . $stopRouteId, true, 303);
                  exit;
              }
              header('Location: admin-manage.php?tab=stops&route=' . $stopRouteId . '&error=' . $stopError, true, 303);
              exit;

          case 'edit_stop':
              $stopId       = (int) ($_POST['id'] ?? 0);
              $stopRouteId  = (int) ($_POST['route_id'] ?? 0);
              $newStopName  = trim($_POST['name'] ?? '');

              $dupStmt = db()->prepare('SELECT id FROM stops WHERE name = ? AND id != ?');
              $dupStmt->bind_param('si', $newStopName, $stopId);
              $dupStmt->execute();
              $dup = $dupStmt->get_result()->fetch_assoc();

              $stopError = match (true) {
                  $newStopName === '' => 'name',
                  $dup !== null        => 'duplicate',
                  default               => null,
              };

              if ($stopError === null) {
                  $stmt = db()->prepare('UPDATE stops SET name = ? WHERE id = ?');
                  $stmt->bind_param('si', $newStopName, $stopId);
                  $stmt->execute();
                  header('Location: admin-manage.php?tab=stops&route=' . $stopRouteId, true, 303);
                  exit;
              }
              header('Location: admin-manage.php?tab=stops&route=' . $stopRouteId . '&edit_stop=' . $stopId . '&error=' . $stopError, true, 303);
              exit;

          case 'edit_user':
              $editUserId  = (int) ($_POST['id'] ?? 0);
              $newName     = trim($_POST['name'] ?? '');
              $newPhone    = trim($_POST['phone'] ?? '');
              $newRole     = $_POST['role'] ?? '';
              $backQuery   = $_POST['back_q'] ?? '';
              $backRole    = $_POST['back_role'] ?? '';

              $phoneOwnerStmt = db()->prepare('SELECT id FROM users WHERE phone = ? AND id != ?');
              $phoneOwnerStmt->bind_param('si', $newPhone, $editUserId);
              $phoneOwnerStmt->execute();
              $phoneTaken = $phoneOwnerStmt->get_result()->fetch_assoc() !== null;

              $userError = match (true) {
                  $newName === ''                                   => 'name',
                  !preg_match('/^\d{10}$/', $newPhone)               => 'phone',
                  $phoneTaken                                        => 'taken',
                  !in_array($newRole, ['passenger', 'driver', 'admin'], true) => 'role',
                  default                                             => null,
              };

              $backParams = 'tab=users' . ($backQuery !== '' ? '&q=' . urlencode($backQuery) : '')
                                        . ($backRole  !== '' ? '&role=' . urlencode($backRole) : '');

              if ($userError === null) {
                  $stmt = db()->prepare('UPDATE users SET name = ?, phone = ?, role = ? WHERE id = ?');
                  $stmt->bind_param('sssi', $newName, $newPhone, $newRole, $editUserId);
                  $stmt->execute();
                  header('Location: admin-manage.php?' . $backParams, true, 303);
                  exit;
              }
              header('Location: admin-manage.php?' . $backParams . '&edit_user=' . $editUserId . '&error=' . $userError, true, 303);
              exit;

          default:
              header('Location: admin-manage.php', true, 303);
              exit;
      }
  }

  // which tab is open? server-side default so the dashboard can deep-link
  // (admin-manage.php?tab=users); :has() handles switching after load.
  $tabs = ['vans', 'routes', 'stops', 'users'];
  $tab  = in_array($_GET['tab'] ?? '', $tabs, true) ? $_GET['tab'] : 'vans';

  $errorMessages = [
      'vans'   => ['plate' => 'Enter a plate number.', 'seats' => 'Enter a valid seat count (1-50).', 'notfound' => 'That van no longer exists.'],
      'routes' => ['name' => 'Enter a route name.', 'stops' => 'Choose two different stops.', 'fare' => 'Enter a valid fare.', 'notfound' => 'That route no longer exists.'],
      'stops'  => ['route' => 'Choose a valid route.', 'name' => 'Enter a stop name.', 'duplicate' => 'A stop with that name already exists.'],
      'users'  => ['name' => 'Enter a name.', 'phone' => 'Enter a valid 10-digit phone number.', 'taken' => 'That phone number belongs to another account.', 'role' => 'Choose a valid role.'],
  ];
  $errorText = isset($_GET['error']) ? ($errorMessages[$tab][$_GET['error']] ?? null) : null;

  // which row (if any) is being edited, per tab — only one at a time
  $editVanId   = isset($_GET['edit_van'])   ? (int) $_GET['edit_van']   : null;
  $editRouteId = isset($_GET['edit_route']) ? (int) $_GET['edit_route'] : null;
  $editStopId  = isset($_GET['edit_stop'])  ? (int) $_GET['edit_stop']  : null;
  $editUserId  = isset($_GET['edit_user'])  ? (int) $_GET['edit_user']  : null;

  $vans   = get_vans();
  $routes = get_routes();
  $allStops = get_dropoffs(null);   // every stop, for the add-route origin/terminus pickers

  // --- Stops tab: which route's chain are we viewing? ---
  $stopsRouteId = isset($_GET['route']) ? (int) $_GET['route'] : (array_key_first($routes) ?? 0);
  if (!isset($routes[$stopsRouteId])) {
      $stopsRouteId = array_key_first($routes) ?? 0;
  }
  $stopsChain = $stopsRouteId ? get_route_stop_chain($stopsRouteId) : [];

  // --- Users tab: search + role filter, plain GET, server-side ---
  $userQuery = trim($_GET['q'] ?? '');
  $roleFilter = $_GET['role'] ?? '';

  $conditions = [];
  $params = [];
  if ($userQuery !== '') {
      $conditions[] = '(name LIKE ? OR phone LIKE ?)';
      $like = '%' . $userQuery . '%';
      $params[] = $like;
      $params[] = $like;
  }
  if (in_array($roleFilter, ['passenger', 'driver', 'admin'], true)) {
      $conditions[] = 'role = ?';
      $params[] = $roleFilter;
  }
  $userSql = 'SELECT id, name, phone, role FROM users';
  if ($conditions) {
      $userSql .= ' WHERE ' . implode(' AND ', $conditions);
  }
  $userSql .= ' ORDER BY name';
  $userStmt = db()->prepare($userSql);
  if ($params) {
      $userStmt->execute($params);
  } else {
      $userStmt->execute();
  }
  $users = $userStmt->get_result()->fetch_all(MYSQLI_ASSOC);

  $page_title = 'AU VAN - Manage';
  $user_role  = 'admin';
  $user_name  = $user['name'];
  include '../includes/header.php';
?>

  <main class="page">
    <div class="container">

      <div class="manage">

        <h2>Manage Data</h2>

        <div class="manage-tabs">
          <label class="manage-tab">
            <input type="radio" name="manage-tab" value="vans" <?= $tab === 'vans' ? 'checked' : '' ?>>
            Vans
          </label>
          <label class="manage-tab">
            <input type="radio" name="manage-tab" value="routes" <?= $tab === 'routes' ? 'checked' : '' ?>>
            Routes
          </label>
          <label class="manage-tab">
            <input type="radio" name="manage-tab" value="stops" <?= $tab === 'stops' ? 'checked' : '' ?>>
            Stops
          </label>
          <label class="manage-tab">
            <input type="radio" name="manage-tab" value="users" <?= $tab === 'users' ? 'checked' : '' ?>>
            Users
          </label>
        </div>

        <!-- VANS -->
        <div class="manage-panel manage-panel-vans">
          <div class="manage-panel-head">
            <span class="manage-count"><?= count($vans) ?> vans</span>
          </div>

          <?php if ($tab === 'vans' && $errorText !== null): ?>
            <p class="auth-error"><?= htmlspecialchars($errorText) ?></p>
          <?php endif; ?>

          <table class="data-table">
            <thead>
              <tr><th>Plate Number</th><th>Seats</th><th class="data-edit-col">Edit</th></tr>
            </thead>
            <tbody>
              <?php foreach ($vans as $vid => $v): ?>
                <?php if ($editVanId === $vid): ?>
                  <tr>
                    <td><input class="input" type="text" name="plate" form="edit-van-<?= $vid ?>" value="<?= htmlspecialchars($v['plate']) ?>"></td>
                    <td><input class="input" type="number" name="seats" form="edit-van-<?= $vid ?>" value="<?= (int) $v['seats'] ?>" min="1" max="50"></td>
                    <td class="data-edit-col">
                      <button type="submit" form="edit-van-<?= $vid ?>" class="btn btn-primary btn-sm">Save</button>
                      <a href="admin-manage.php?tab=vans" class="btn btn-secondary btn-sm">Cancel</a>
                    </td>
                  </tr>
                  <form id="edit-van-<?= $vid ?>" method="POST" action="admin-manage.php">
                    <input type="hidden" name="do" value="edit_van">
                    <input type="hidden" name="id" value="<?= $vid ?>">
                  </form>
                <?php else: ?>
                  <tr>
                    <td class="data-strong"><?= htmlspecialchars($v['plate']) ?></td>
                    <td><?= (int) $v['seats'] ?></td>
                    <td class="data-edit-col">
                      <a class="data-edit" href="admin-manage.php?tab=vans&edit_van=<?= $vid ?>" aria-label="Edit <?= htmlspecialchars($v['plate']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" focusable="false">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                          <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path>
                        </svg>
                      </a>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>

          <form class="manage-add" method="POST" action="admin-manage.php">
            <input type="hidden" name="do" value="add_van">
            <input class="input" type="text" name="plate" placeholder="Enter Plate Number" autocomplete="off">
            <input class="input manage-add-num" type="number" name="seats" placeholder="Seats" min="1" autocomplete="off">
            <button class="btn btn-primary btn-sm" type="submit">+ Add Van</button>
          </form>
        </div>

        <!-- ROUTES -->
        <div class="manage-panel manage-panel-routes">
          <div class="manage-panel-head">
            <span class="manage-count"><?= count($routes) ?> routes</span>
          </div>

          <?php if ($tab === 'routes' && $errorText !== null): ?>
            <p class="auth-error"><?= htmlspecialchars($errorText) ?></p>
          <?php endif; ?>

          <table class="data-table">
            <thead>
              <tr><th>Name</th><th>Route</th><th>Fare</th><th class="data-edit-col">Edit</th></tr>
            </thead>
            <tbody>
              <?php foreach ($routes as $rid => $r): ?>
                <?php if ($editRouteId === $rid): ?>
                  <tr>
                    <td><input class="input" type="text" name="name" form="edit-route-<?= $rid ?>" value="<?= htmlspecialchars($r['name']) ?>"></td>
                    <td><?= htmlspecialchars($r['from']) ?> &rarr; <?= htmlspecialchars($r['to']) ?></td>
                    <td><input class="input" type="number" name="fare" form="edit-route-<?= $rid ?>" value="<?= (int) $r['fare'] ?>" min="1"></td>
                    <td class="data-edit-col">
                      <button type="submit" form="edit-route-<?= $rid ?>" class="btn btn-primary btn-sm">Save</button>
                      <a href="admin-manage.php?tab=routes" class="btn btn-secondary btn-sm">Cancel</a>
                    </td>
                  </tr>
                  <form id="edit-route-<?= $rid ?>" method="POST" action="admin-manage.php">
                    <input type="hidden" name="do" value="edit_route">
                    <input type="hidden" name="id" value="<?= $rid ?>">
                  </form>
                <?php else: ?>
                  <tr>
                    <td class="data-strong"><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['from']) ?> &rarr; <?= htmlspecialchars($r['to']) ?></td>
                    <td>฿<?= (int) $r['fare'] ?></td>
                    <td class="data-edit-col">
                      <a class="data-edit" href="admin-manage.php?tab=routes&edit_route=<?= $rid ?>" aria-label="Edit <?= htmlspecialchars($r['name']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" focusable="false">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                          <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path>
                        </svg>
                      </a>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>

          <form class="manage-add" method="POST" action="admin-manage.php">
            <input type="hidden" name="do" value="add_route">
            <input class="input" type="text" name="name" placeholder="Route Name" autocomplete="off">
            <select class="input" name="origin_stop">
              <?php foreach ($allStops as $sid => $sname): ?>
                <option value="<?= (int) $sid ?>"><?= htmlspecialchars($sname) ?></option>
              <?php endforeach; ?>
            </select>
            <select class="input" name="terminus_stop">
              <?php foreach ($allStops as $sid => $sname): ?>
                <option value="<?= (int) $sid ?>"><?= htmlspecialchars($sname) ?></option>
              <?php endforeach; ?>
            </select>
            <input class="input manage-add-num" type="number" name="fare" placeholder="Fare (฿)" min="1" autocomplete="off">
            <button class="btn btn-primary btn-sm" type="submit">+ Add Route</button>
          </form>
        </div>

        <!-- STOPS -->
        <div class="manage-panel manage-panel-stops">
          <div class="manage-panel-head">
            <form class="manage-stops-route" method="GET" action="admin-manage.php">
              <input type="hidden" name="tab" value="stops">
              <span class="field-label">Route</span>
              <select class="input" name="route">
                <?php foreach ($routes as $rid => $r): ?>
                  <option value="<?= (int) $rid ?>" <?= $rid === $stopsRouteId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['from']) ?> &rarr; <?= htmlspecialchars($r['to']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-secondary btn-sm">View</button>
            </form>
          </div>

          <?php if ($tab === 'stops' && $errorText !== null): ?>
            <p class="auth-error"><?= htmlspecialchars($errorText) ?></p>
          <?php endif; ?>

          <table class="data-table">
            <thead>
              <tr><th class="data-order-col">Order</th><th>Stop</th><th class="data-edit-col">Edit</th></tr>
            </thead>
            <tbody>
              <?php foreach ($stopsChain as $s): ?>
                <?php if ($editStopId === $s['stop_id']): ?>
                  <tr>
                    <td class="data-order-col"><?= (int) $s['seq'] ?></td>
                    <td><input class="input" type="text" name="name" form="edit-stop-<?= $s['stop_id'] ?>" value="<?= htmlspecialchars($s['name']) ?>"></td>
                    <td class="data-edit-col">
                      <button type="submit" form="edit-stop-<?= $s['stop_id'] ?>" class="btn btn-primary btn-sm">Save</button>
                      <a href="admin-manage.php?tab=stops&route=<?= $stopsRouteId ?>" class="btn btn-secondary btn-sm">Cancel</a>
                    </td>
                  </tr>
                  <form id="edit-stop-<?= $s['stop_id'] ?>" method="POST" action="admin-manage.php">
                    <input type="hidden" name="do" value="edit_stop">
                    <input type="hidden" name="id" value="<?= $s['stop_id'] ?>">
                    <input type="hidden" name="route_id" value="<?= $stopsRouteId ?>">
                  </form>
                <?php else: ?>
                  <tr>
                    <td class="data-order-col"><?= (int) $s['seq'] ?></td>
                    <td class="data-strong"><?= htmlspecialchars($s['name']) ?></td>
                    <td class="data-edit-col">
                      <a class="data-edit" href="admin-manage.php?tab=stops&route=<?= $stopsRouteId ?>&edit_stop=<?= $s['stop_id'] ?>" aria-label="Edit <?= htmlspecialchars($s['name']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" focusable="false">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                          <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path>
                        </svg>
                      </a>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>

          <form class="manage-add" method="POST" action="admin-manage.php">
            <input type="hidden" name="do" value="add_stop">
            <input type="hidden" name="route_id" value="<?= (int) $stopsRouteId ?>">
            <input class="input" type="text" name="stop" placeholder="Enter New Stop" autocomplete="off">
            <button class="btn btn-primary btn-sm" type="submit">+ Add Stop</button>
          </form>
        </div>

        <!-- USERS -->
        <div class="manage-panel manage-panel-users">
          <div class="manage-panel-head">
            <form class="manage-filters" method="GET" action="admin-manage.php">
              <input type="hidden" name="tab" value="users">
              <input class="input" type="search" name="q" placeholder="Search name or phone"
                     value="<?= htmlspecialchars($userQuery) ?>" autocomplete="off">
              <select class="input manage-role-filter" name="role">
                <option value="" <?= $roleFilter === '' ? 'selected' : '' ?>>All Roles</option>
                <option value="passenger" <?= $roleFilter === 'passenger' ? 'selected' : '' ?>>Passenger</option>
                <option value="driver" <?= $roleFilter === 'driver' ? 'selected' : '' ?>>Driver</option>
                <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
              </select>
              <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>
          </div>

          <?php if ($tab === 'users' && $errorText !== null): ?>
            <p class="auth-error"><?= htmlspecialchars($errorText) ?></p>
          <?php endif; ?>

          <table class="data-table">
            <thead>
              <tr><th>Name</th><th>Phone</th><th>Role</th><th class="data-edit-col">Edit</th></tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
                <?php if ($editUserId === (int) $u['id']): ?>
                  <tr>
                    <td><input class="input" type="text" name="name" form="edit-user-<?= $u['id'] ?>" value="<?= htmlspecialchars($u['name']) ?>"></td>
                    <td><input class="input" type="text" name="phone" form="edit-user-<?= $u['id'] ?>" value="<?= htmlspecialchars($u['phone']) ?>"></td>
                    <td>
                      <select class="input" name="role" form="edit-user-<?= $u['id'] ?>">
                        <option value="passenger" <?= $u['role'] === 'passenger' ? 'selected' : '' ?>>Passenger</option>
                        <option value="driver" <?= $u['role'] === 'driver' ? 'selected' : '' ?>>Driver</option>
                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                      </select>
                    </td>
                    <td class="data-edit-col">
                      <button type="submit" form="edit-user-<?= $u['id'] ?>" class="btn btn-primary btn-sm">Save</button>
                      <a href="admin-manage.php?tab=users" class="btn btn-secondary btn-sm">Cancel</a>
                    </td>
                  </tr>
                  <form id="edit-user-<?= $u['id'] ?>" method="POST" action="admin-manage.php">
                    <input type="hidden" name="do" value="edit_user">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="back_q" value="<?= htmlspecialchars($userQuery) ?>">
                    <input type="hidden" name="back_role" value="<?= htmlspecialchars($roleFilter) ?>">
                  </form>
                <?php else: ?>
                  <tr>
                    <td class="data-strong"><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['phone']) ?></td>
                    <td><span class="data-role"><?= htmlspecialchars(ucfirst($u['role'])) ?></span></td>
                    <td class="data-edit-col">
                      <a class="data-edit" href="admin-manage.php?tab=users&edit_user=<?= $u['id'] ?>&q=<?= urlencode($userQuery) ?>&role=<?= urlencode($roleFilter) ?>" aria-label="Edit <?= htmlspecialchars($u['name']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" focusable="false">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                          <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path>
                        </svg>
                      </a>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>

    </div>
  </main>

<?php include '../includes/footer.php'; ?>