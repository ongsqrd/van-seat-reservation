<?php
  require_once '../includes/routes.php';

  $routes = get_routes();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU VAN - Route Selection</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
        $user_role = 'passenger';
        $user_name = 'Jane Doe';
        include '../includes/navbar.php';
    ?>

    <main class="routes">
        <div class="container container-mid">

            <div class="routes-header">
                <h2>Choose your Route</h2>
                <p>Where would you like to go today?</p>
            </div>

            <ul class="route-list">
                <?php foreach ($routes as $id => $route): ?>
                    <li>
                        <a class="route-card"
                           href="trip-times.php?route=<?= (int) $id ?>">

                            <span class="route-icon">
                                <?php include '../includes/van-icon.php'; ?>
                            </span>

                            <span class="route-text">
                                <span class="route-name"><?= htmlspecialchars($route['name']) ?></span>
                                <span class="route-detail"><?= htmlspecialchars(route_detail($route)) ?></span>
                            </span>

                            <span class="route-go">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true" focusable="false">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>

                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </main>
</body>

</html>