<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU VAN - Landing Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include '../includes/navbar.php';?>
    <main class="landing-content">
        <div class="landing-card-wrapper">
            <div class="landing-card">
                <h1 class="landing-title">AU VAN<br>BOOKING</h1>

                <p>
                    Welcome to our AU Van booking service.
                    Where would you like to go today? If you are
                    new here, click Join Now to sign up and
                    make an account. 
                </p>

                <div class="button-container">
                    <button type="button" class="btn-primary">Join now</button>
                    <button type="button" class="btn-primary btn-secondary">Log in</button>
                </div>
            </div>

            <div class="landing-card">
                <div class="laptop-mockup laptop-mockup-align">
                    <?php include('../includes/laptop-mockup.php');?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>