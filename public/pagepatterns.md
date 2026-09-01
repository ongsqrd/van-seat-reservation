# Page Layout Patterns

Five reusable HTML skeletons. Every one of the ~20 pages is a variation of one of
these. Copy the relevant block into a real `.php` file and style it against
`style.css`. **These are structure only — build your own content and styling from
here.**

All pages share the same shell:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>[Page name]</title>
</head>
<body>
  <?php include '../includes/navbar.php';?>
  <main class="page">
    <!-- pattern content goes here -->
  </main>
</body>
</html>
```

The patterns below show only what goes **inside `<main>`**.

---

## 1. Form pattern
**Used by:** login, register, booking-confirm (the payment form side)

```html
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
```

---

## 2. List pattern
**Used by:** trips, my-bookings, driver-dashboard, admin-trips

A stack of repeated cards. In the static build, hardcode a few; later a PHP loop
generates them.

```html
<section class="list">
  <h2 class="list-title">My bookings</h2>

  <a class="list-card" href="ticket.php?ref=E43RF5YK78">
    <div class="list-card-main">
      <p class="list-card-title">ABAC &rarr; Bangna</p>
      <p class="list-card-sub">10 May 2026 · 12:00 PM · 3 seats</p>
    </div>
    <span class="badge badge-green">Confirmed</span>
  </a>

  <a class="list-card" href="ticket.php?ref=HQE34EF2XX">
    <div class="list-card-main">
      <p class="list-card-title">ABAC &rarr; Hua Mak</p>
      <p class="list-card-sub">11 May 2026 · 11:00 AM · 2 seats</p>
    </div>
    <span class="badge badge-grey">Completed</span>
  </a>
</section>
```

---

## 3. Detail pattern
**Used by:** ticket, booking-success, driver-trip (header part)

One record shown in full. A header, a body of label/value pairs, actions.

```html
<section class="detail-card">
  <header class="detail-header">
    <p class="detail-eyebrow">E-ticket · 3 seats</p>
    <h2>ABAC &rarr; Bangna</h2>
    <p class="detail-ref">Ref: E43RF5YK78</p>
  </header>

  <div class="detail-qr">
    <!-- QR image goes here -->
  </div>

  <dl class="detail-grid">
    <div><dt>Date</dt><dd>10 May 2026</dd></div>
    <div><dt>Time</dt><dd>12:00 PM</dd></div>
    <div><dt>Drop-off</dt><dd>Bangna Junction</dd></div>
    <div><dt>Seats</dt><dd>3</dd></div>
    <div><dt>Passenger</dt><dd>Jane Doe</dd></div>
    <div><dt>Total fare</dt><dd>&#3647;120</dd></div>
  </dl>

  <footer class="detail-actions">
    <button class="btn-primary">Save QR</button>
  </footer>
</section>
```

---

## 4. Table pattern
**Used by:** admin-manage (all tabs), admin-trips list, driver-trip manifest

Rows and columns of data. Use a real `<table>` — it's the correct element and the
most accessible.

```html
<section class="table-section">
  <div class="table-toolbar">
    <h2>Vans</h2>
    <button class="btn-primary">+ Add van</button>
  </div>

  <table class="data-table">
    <thead>
      <tr>
        <th>Plate</th>
        <th>Model</th>
        <th>Seats</th>
        <th>Edit</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>&#3609;&#3586; 1234</td>
        <td>Toyota Commuter</td>
        <td>15</td>
        <td><a href="#">Edit</a></td>
      </tr>
      <tr>
        <td>&#3585;&#3585; 5678</td>
        <td>Toyota Commuter</td>
        <td>15</td>
        <td><a href="#">Edit</a></td>
      </tr>
    </tbody>
  </table>
</section>
```

---

## 5. Dashboard pattern
**Used by:** admin-dashboard, driver-dashboard (top part)

An alert banner, a row of stat tiles, and navigation cards.

```html
<section class="dashboard">
  <h2 class="dashboard-title">Admin dashboard</h2>

  <div class="alert alert-warning">
    <p>2 trips need a driver</p>
    <a href="admin-trips.php" class="btn-small">View</a>
  </div>

  <div class="stat-grid">
    <div class="stat"><p class="stat-label">Trips today</p><p class="stat-value">6</p></div>
    <div class="stat"><p class="stat-label">Seats booked</p><p class="stat-value">58</p></div>
    <div class="stat"><p class="stat-label">Active routes</p><p class="stat-value">4</p></div>
  </div>

  <div class="nav-grid">
    <a class="nav-card" href="admin-trips.php">
      <p class="nav-card-title">Trips</p>
      <p class="nav-card-sub">Create, edit, assign drivers</p>
    </a>
    <a class="nav-card" href="admin-manage.php">
      <p class="nav-card-title">Manage data</p>
      <p class="nav-card-sub">Vans, routes, stops, users</p>
    </a>
  </div>
</section>
```

---

## How to use this

- Each of your 20 pages maps to one pattern (see "Used by" lines).
- Copy the block, rename classes if you like, build the content out.
- Style each pattern's classes **once** in `style.css` — then every page using that
  pattern is already mostly styled.
- The Thai characters above use HTML entities (`&#3609;` etc.) so they render even
  if the file encoding is off; in real files you can type Thai directly if the file
  is UTF-8.

## Class naming note
Keep class names consistent across the team — `list-card`, `data-table`, `stat`,
`nav-card`. If everyone invents their own, the shared CSS won't apply. This file is
the reference for those names.