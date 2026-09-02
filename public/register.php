<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU VAN - New Account</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
        $user_role = 'guest';
        include '../includes/navbar.php';
    ?>

    <main class="auth">
        <div class="container container-narrow">

            <h2 class="auth-title">Create new Account</h2>

            <form class="auth-form" action="register_process.php" method="POST">

                <div class="field">
                    <label class="field-label" for="fullname">NAME</label>
                    <input class="input" type="text" id="fullname" name="fullname"
                           autocomplete="name" required>
                </div>

                <div class="field">
                    <label class="field-label" for="phone">PHONE</label>
                    <input class="input" type="tel" id="phone" name="phone"
                           autocomplete="tel" required>
                </div>

                <div class="field">
                    <label class="field-label" for="password">PASSWORD</label>
                    <input class="input" type="password" id="password" name="password"
                           autocomplete="new-password" required>
                </div>

                <div class="field">
                    <label class="field-label" for="confirmPassword">CONFIRM PASSWORD</label>
                    <input class="input" type="password" id="confirmPassword" name="confirmPassword"
                           autocomplete="new-password" required>
                </div>

                <div class="field-check">
                    <input type="checkbox" id="terms" name="terms" value="1" required>
                    <label for="terms">I have agreed to the terms and conditions</label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign Up</button>

            </form>

            <p class="auth-alt">
                Already have an account? <a href="login.php">Login here</a>
            </p>

        </div>
    </main>
</body>

</html>