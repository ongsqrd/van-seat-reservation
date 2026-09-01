<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU VAN - Route Selection</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <main class="route-container">
        <div class="header-route">
            <h2 class="header-centered">Choose your Route</h2>
            <p>Where would you like to go today?</p>
        </div>

        <div class="route-options">
            <div class="route-card">
                <div class="van-icon">
                    <?php include '../includes/van-icon.php'; ?>
                </div>
                <div class="route-info">
                    <h3>Assumption University</h3>
                    <p>From Bangna to Assumption U.</p>
                </div>
            </div>

            <div class="route-card">
                <div class="van-icon">
                    <?php include '../includes/van-icon.php'; ?>
                </div>
                <div class="route-info">
                    <h3>Hua Mak Campus</h3>
                    <p>From Assumption U. to Hua Mak.</p>
                </div>
            </div>

            <div class="route-card">
                <div class="van-icon">
                    <?php include '../includes/van-icon.php'; ?>
                </div>
                <div class="route-info">
                    <h3>Bangna</h3>
                    <p>From Assumption U. to Bangna</p>
                </div>
            </div>

            <div class="route-card">
                <div class="van-icon">
                    <?php include '../includes/van-icon.php'; ?>
                </div>
                <div class="route-info">
                    <h3>Assumption University</h3>
                    <p>From Hua Mak to Assumption U.</p>
                </div>
            </div>
        </div>
        
    </main>
</body>
</html>