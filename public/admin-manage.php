<?php
  require_once '../includes/routes.php';

  // routes + stops come from the existing include; vans + users are
  // placeholder arrays until those tables exist.
  $routes = get_routes();
  $stops  = get_dropoffs();

  $vans = [
      ['plate' => 'กข 1234', 'seats' => 15],
      ['plate' => 'พส 6767', 'seats' => 15],
  ];

  $users = [
      ['name' => 'Jane Doe',      'phone' => '092 738 4952', 'role' => 'Passenger'],
      ['name' => 'John Doe',      'phone' => '083 749 4750', 'role' => 'Driver'],
      ['name' => 'Chanyapat S.',  'phone' => '089 237 4987', 'role' => 'Admin'],
      ['name' => 'Sherlock H.',   'phone' => '098 234 7593', 'role' => 'Driver'],
  ];

  // which tab is open? server-side default so the dashboard can deep-link
  // (admin-manage.php?tab=users); :has() handles switching after load.
  $tabs = ['vans', 'routes', 'stops', 'users'];
  $tab  = in_array($_GET['tab'] ?? '', $tabs, true) ? $_GET['tab'] : 'vans';

  // small pencil, reused in every Edit cell
  function edit_button(string $label): string
  {
      return '<button type="button" class="data-edit" aria-label="Edit ' . htmlspecialchars($label) . '">'
           . '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" '
           . 'stroke-linecap="round" stroke-linejoin="round" focusable="false">'
           . '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>'
           . '<path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path>'
           . '</svg></button>';
  }

  $page_title = 'AU VAN - Manage';
  $user_role  = 'admin';
  $user_name  = 'Chanyapat Saeng-Xuto';
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

          <table class="data-table">
            <thead>
              <tr><th>Plate Number</th><th>Seats</th><th class="data-edit-col">Edit</th></tr>
            </thead>
            <tbody>
              <?php foreach ($vans as $v): ?>
                <tr>
                  <td class="data-strong"><?= htmlspecialchars($v['plate']) ?></td>
                  <td><?= (int) $v['seats'] ?></td>
                  <td class="data-edit-col"><?= edit_button($v['plate']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <form class="manage-add" method="POST" action="admin-manage.php?tab=vans">
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

          <table class="data-table">
            <thead>
              <tr><th>Route</th><th>Fare</th><th class="data-edit-col">Edit</th></tr>
            </thead>
            <tbody>
              <?php foreach ($routes as $r): ?>
                <tr>
                  <td class="data-strong"><?= htmlspecialchars($r['from']) ?> &rarr; <?= htmlspecialchars($r['to']) ?></td>
                  <td>฿<?= (int) $r['fare'] ?></td>
                  <td class="data-edit-col"><?= edit_button($r['from'] . ' to ' . $r['to']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <form class="manage-add" method="POST" action="admin-manage.php?tab=routes">
            <input class="input" type="text" name="origin" placeholder="Origin" autocomplete="off">
            <input class="input" type="text" name="terminus" placeholder="Terminus" autocomplete="off">
            <button class="btn btn-primary btn-sm" type="submit">+ Add Route</button>
          </form>
        </div>

        <!-- STOPS -->
        <div class="manage-panel manage-panel-stops">
          <div class="manage-panel-head">
            <label class="manage-stops-route">
              <span class="field-label">Route</span>
              <select class="input">
                <?php foreach ($routes as $r): ?>
                  <option><?= htmlspecialchars($r['from']) ?> &rarr; <?= htmlspecialchars($r['to']) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>

          <table class="data-table">
            <thead>
              <tr><th class="data-order-col">Order</th><th>Stop</th><th class="data-edit-col">Edit</th></tr>
            </thead>
            <tbody>
              <?php $order = 1; foreach ($stops as $label): ?>
                <tr>
                  <td class="data-order-col"><?= $order++ ?></td>
                  <td class="data-strong"><?= htmlspecialchars($label) ?></td>
                  <td class="data-edit-col"><?= edit_button($label) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <form class="manage-add" method="POST" action="admin-manage.php?tab=stops">
            <input class="input" type="text" name="stop" placeholder="Enter New Stop" autocomplete="off">
            <button class="btn btn-primary btn-sm" type="submit">+ Add Stop</button>
          </form>
        </div>

        <!-- USERS -->
        <div class="manage-panel manage-panel-users">
          <div class="manage-panel-head">
            <div class="manage-filters">
              <input class="input" type="search" placeholder="Search name or phone" autocomplete="off">
              <select class="input manage-role-filter">
                <option>All Roles</option>
                <option>Passenger</option>
                <option>Driver</option>
                <option>Admin</option>
              </select>
            </div>
          </div>

          <table class="data-table">
            <thead>
              <tr><th>Name</th><th>Phone</th><th>Role</th><th class="data-edit-col">Edit</th></tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td class="data-strong"><?= htmlspecialchars($u['name']) ?></td>
                  <td><?= htmlspecialchars($u['phone']) ?></td>
                  <td><span class="data-role"><?= htmlspecialchars($u['role']) ?></span></td>
                  <td class="data-edit-col"><?= edit_button($u['name']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>

    </div>
  </main>

<?php include '../includes/footer.php'; ?>