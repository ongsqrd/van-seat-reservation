<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>AU Van - Time Selection</title>
</head>
<body>
  <?php include '../includes/navbar.php';?>
  <main class="page">
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

    <div>
        <h3 class="trip-title">DROP-OFF</h3>
        <h3 class="trip-title">NO. OF PASSENGER</h3>
        <h3 class="trip-title">DEPARTURE TIME</h3>
    </div>

    <div>
        <button class="btn-primary" id="back">Back</button>
        <button class="btn-primary" id="next">Next</button>
    </div>

  </main>  
</body>
</html>
