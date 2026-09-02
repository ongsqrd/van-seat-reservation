<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>AU VAN - New Account</title>
</head>
<body>
    <main>
        <?php include '../includes/navbar.php';?>
        <section class="login-section">
        <div class="header-centered">
            <h2>Create new Account</h2>
        </div>

        <div class="login-container login-container-newAcc">
            <form action="register_process.php" method="POST">
                <div class="form-group">
                    <label for="fullname">NAME</label>
                    <input type="fullname" id="fullname" name="fullname" value="" class="input-field" required>
                </div>

                <div class="form-group">
                    <label for="phone">PHONE</label>
                    <input type="tel" id="phone" name="phone" value="" class="input-field" required>
                </div>

                <div class="form-group">
                    <label for="password">PASSWORD</label>
                    <input type="password" id="password" name="password" value="" class="input-field" required>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">CONFIRM PASSWORD</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" value="" class="input-field" required>
                </div>

                <button type="submit" class="btn-primary btn-centered">Sign Up</button>

                <div class="navlink-centered">
                    <p class="no-account">Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
        </section>
  </main>
</body>
</html>