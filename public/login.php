<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Login</title>
</head>
<body>
    <main>
        <section class="login-section">
        <div class="header-centered">
            <h2>Welcome Back!</h2>
        </div>

        <div class="login-container">
            <form action="login_process.php" method="POST">
                <div class="form-group">
                    <label for="phone">PHONE</label>
                    <input type="tel" id="phone" name="phone" value="" class="input-field" required>
                </div>

                <div class="form-group">
                    <label for="password">PASSWORD</label>
                    <input type="password" id="password" name="password" value="" class="input-field" required>
                </div>

                <button type="submit" class="btn-primary btn-centered">Login</button>

                <div class="navlink-centered">
                    <p class="no-account">Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </form>
        </div>
        </section>
  </main>
</body>
</html>