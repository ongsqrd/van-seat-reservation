<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU Van — Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
        $user_role = 'guest';
        include '../includes/navbar.php';
    ?>

    <main class="auth">
        <div class="container container-narrow">

            <h2 class="auth-title">Welcome Back!</h2>

            <form class="auth-form" action="login_process.php" method="POST">

                <div class="field">
                    <label class="field-label" for="phone">PHONE</label>
                    <input class="input" type="tel" id="phone" name="phone"
                           autocomplete="tel" required>
                </div>

                <div class="field">
                    <label class="field-label" for="password">PASSWORD</label>
                    <input class="input" type="password" id="password" name="password"
                           autocomplete="current-password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>

            </form>

            <p class="auth-alt">
                Don't have an account? <a href="register.php">Register here</a>
            </p>

        </div>
    </main>
</body>

</html>