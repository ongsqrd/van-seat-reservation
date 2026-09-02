<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU Van — Book a university van seat</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
        $user_role = 'guest';
        include '../includes/navbar.php';
    ?>

    <main class="container container-wide landing">

        <div class="landing-copy">
            <h1 class="landing-title">AU VAN<br>BOOKING</h1>

            <p class="landing-lead">
                Welcome to our AU Van booking service.
                Where would you like to go today? If you are
                new here, click Join Now to sign up and
                make an account.
            </p>

            <div class="landing-actions">
                <a href="register.php" class="btn btn-primary">Join now</a>
                <a href="login.php" class="btn btn-secondary">Log in</a>
            </div>
        </div>

        <div class="landing-visual">
            <?php include '../includes/laptop-mockup.php'; ?>
        </div>

    </main>
</body>

</html>