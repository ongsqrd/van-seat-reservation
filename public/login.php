<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Login</title>
</head>
<body>
  <!-- navbar include goes here later -->
  <main class="page">
    <section class="form-card">
        <header class="form-header">
            <h2>Welcome Back!</h2>
        </header>

        <form action="[action].php" method="POST">
            <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-primary btn-centered">Login</button>
        </form>

        <p class="form-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </section>
  </main>
</body>
</html>