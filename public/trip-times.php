<?php
  // --- mutable slot data (later comes from the trips + bookings query) ---
  // Column 1 = first four rows, Column 2 = last four rows.
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>AU Van - Time Selection</title>
</head>
<body>
  <?php include '../includes/navbar.php'; ?>
  <main class="page">

    <div class="trip-info-wrapper">

        <div class="trip-card-wrapper">

            <div class="trip-card">
                <label for="origin">FROM </label>
                <input type="text" id="origin" name="origin" value="Assumption U." class="trip-input" readonly>
            </div>

            <div class="journeyline">
                <?php include '../includes/journeyline.php'; ?>
            </div>

            <div class="trip-card">
                <label for="destination">TO </label>
                <input type="text" id="destination" name="destination" value="Bangna" class="trip-input" readonly>
            </div>
        </div>

        <div class="trip-info-line">
            <div class="trip-card-input">
                <label for="dropoff" class="trip-title">DROP-OFF</label>
                <select name="dropoff" id="dropoff" class="trip-dropoff">
                    <option value="bangna">Bangna</option>
                    <option value="mega-bangna">Mega Bangna</option>
                    <option value="market-village">Market Village</option>
                    <option value="paradise-park">Paradise Park</option>
                </select>
            </div>

            <div class="trip-card-input">
                <label class="trip-title">NO. OF PASSENGER</label>
                <select name="numPassenger" id="numPassenger" class="trip-dropoff">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
        </div>

        <label class="trip-title">DEPARTURE TIME</label>
        <div class="depart-time-list">
            <?php foreach ($slots as $slot):
                $available = max(0, min($slot['available'], $slot['capacity']));   // clamp into range
                $booked    = $slot['capacity'] - $available;
                $percent   = $slot['capacity'] > 0 ? round($booked / $slot['capacity'] * 100) : 100;
                $isFull    = $available <= 0;
                $level     = $percent >= 75 ? 'is-low' : ($percent >= 40 ? 'is-med' : 'is-high');
            ?>
                <label class="depart-time-card <?= $isFull ? 'is-full' : '' ?>">
                    <input type="radio" name="trip_id" value="<?= (int) $slot['id'] ?>"
                           class="depart-radio" <?= $isFull ? 'disabled' : '' ?>>
                    <h3><?= htmlspecialchars($slot['time']) ?></h3>

                    <div class="depart-availability">
                        <?php if ($isFull): ?>
                            <span class="seats-label is-full">FULL</span>
                        <?php else: ?>
                            <span class="seats-label"><?= (int) $available ?> available</span>
                        <?php endif; ?>
                        <div class="seats-bar">
                            <div class="seats-bar-fill <?= $level ?>" style="width: <?= $percent ?>%;"></div>
                        </div>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="btn-box">
        <button class="btn-secondary" id="back">Back</button>
        <button class="btn-primary" id="next">Next</button>
    </div>

  </main>
</body>
</html>