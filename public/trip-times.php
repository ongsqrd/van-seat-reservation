<?php
  require_once '../includes/routes.php';

  // which route did we arrive from? bounce back if the id is missing or bogus
  $routeId = isset($_GET['route']) ? (int) $_GET['route'] : 0;
  $route   = find_route($routeId);

  if ($route === null) {
      header('Location: trips.php');
      exit;
  }

  // --- mutable slot data (later comes from the trips + bookings query) ---
  $slots = [
    ['id' => 1, 'time' => '08 : 00 AM', 'capacity' => 14, 'available' => 3 ],
    ['id' => 2, 'time' => '09 : 00 AM', 'capacity' => 14, 'available' => 10],
    ['id' => 3, 'time' => '10 : 00 AM', 'capacity' => 14, 'available' => 5 ],
    ['id' => 4, 'time' => '11 : 00 AM', 'capacity' => 14, 'available' => 7 ],
    ['id' => 5, 'time' => '12 : 00 PM', 'capacity' => 14, 'available' => 12],
    ['id' => 6, 'time' => '01 : 30 PM', 'capacity' => 14, 'available' => 12],
    ['id' => 7, 'time' => '03 : 00 PM', 'capacity' => 14, 'available' => 5 ],
    ['id' => 8, 'time' => '04 : 30 PM', 'capacity' => 14, 'available' => 0 ],
  ];

  // two columns, filled top to bottom — the row count follows the data
  $rows = (int) ceil(count($slots) / 2);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AU VAN - Time</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
    $user_role = 'passenger';
    $user_name = 'Jane Doe';
    include '../includes/navbar.php';
  ?>

  <main class="page">
    <div class="container">

      <form class="trip-form" action="booking-confirm.php" method="POST">

        <input type="hidden" name="route_id" value="<?= (int) $routeId ?>">

        <div class="trip-route">
          <div class="trip-endpoint">
            <span class="trip-endpoint-label">FROM</span>
            <span class="trip-endpoint-value"><?= htmlspecialchars($route['from']) ?></span>
          </div>

          <div class="journeyline">
            <?php include '../includes/journeyline.php'; ?>
          </div>

          <div class="trip-endpoint">
            <span class="trip-endpoint-label">TO</span>
            <span class="trip-endpoint-value"><?= htmlspecialchars($route['to']) ?></span>
          </div>
        </div>

        <div class="trip-options">
          <div class="field">
            <label class="field-label" for="dropoff">DROP-OFF</label>
            <select class="input" name="dropoff" id="dropoff">
              <option value="bangna">Bangna</option>
              <option value="mega-bangna">Mega Bangna</option>
              <option value="market-village">Market Village</option>
              <option value="paradise-park">Paradise Park</option>
            </select>
          </div>

          <div class="field">
            <label class="field-label" for="numPassenger">NO. OF PASSENGER</label>
            <select class="input" name="numPassenger" id="numPassenger">
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
            </select>
          </div>
        </div>

        <div class="field">
          <span class="field-label">DEPARTURE TIME</span>

          <div class="depart-list" style="--rows: <?= $rows ?>">
            <?php foreach ($slots as $slot):
              $available = max(0, min($slot['available'], $slot['capacity']));   // clamp into range
              $booked    = $slot['capacity'] - $available;
              $percent   = $slot['capacity'] > 0 ? round($booked / $slot['capacity'] * 100) : 100;
              $isFull    = $available <= 0;
              $level     = $percent >= 75 ? 'is-low' : ($percent >= 40 ? 'is-med' : 'is-high');
            ?>
              <label class="depart-card <?= $isFull ? 'is-full' : '' ?>">
                <input type="radio" name="trip_id" value="<?= (int) $slot['id'] ?>"
                       class="depart-radio"
                       <?= $isFull ? 'disabled' : 'required' ?>>

                <span class="depart-time"><?= htmlspecialchars($slot['time']) ?></span>

                <span class="depart-availability">
                  <?php if ($isFull): ?>
                    <span class="seats-label is-full">FULL</span>
                  <?php else: ?>
                    <span class="seats-label"><?= (int) $available ?> available</span>
                  <?php endif; ?>

                  <span class="seats-bar">
                    <span class="seats-bar-fill <?= $level ?>" style="width: <?= $percent ?>%;"></span>
                  </span>
                </span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="btn-row">
          <a href="trips.php" class="btn btn-secondary">Back</a>
          <button type="submit" class="btn btn-primary">Next</button>
        </div>

      </form>

    </div>
  </main>
</body>

</html>