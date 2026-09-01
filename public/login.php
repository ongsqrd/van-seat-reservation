<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Login</title>
</head>
<body>
    <div class="header-centered">
        <h2>Welcome Back!</h2>
    </div>

    <div class="login-container">
        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary btn-centered">Login</button>
            <br>
            <div class="navlink-centered">
                <label for="no-account">Don't have an account? <a href="register.php">Register here</a></label>
            </div>
        </form>

        <p class="form-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </section>
  </main>
</body>
</html>