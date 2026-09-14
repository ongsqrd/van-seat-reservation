# AU Van Seat Reservation System

A web-based van seat reservation system for Assumption University, built for
**CE4221 Network Application and Technology**.

Students reserve seats on university van trips in advance, drivers check passengers
in against a real manifest, and the transport office schedules trips and assigns
drivers.

---

## Team

| Student ID | Name |
|---|---|
| 6711021 | _Chanyapat Saeng-Xuto_ |
| 6711038 | _Panupong Jaimethumde_ |
| 6711164 | _Patchara Chainiyom_ |

---

## Tech stack

- **HTML, CSS** — front end. No JavaScript: tabs, toggles, and panel switches
  (payment method, booking history, admin manage) are done with hidden radio
  inputs and the CSS `:has()` selector — the interface is fully server-rendered.
- **PHP 8** — server-side logic, no framework
- **MySQL** (MariaDB via XAMPP) — database, accessed through `mysqli` with
  prepared statements throughout
- **XAMPP** (Apache + PHP + MySQL) — local environment
- **Visual Studio Code** — editor

---

## Project status

**Functional.** The front end was built first with placeholder data, per the
course requirement; the database was designed afterward and every page now runs
against real MySQL queries. All core flows work end to end:

- Registration and login, with bcrypt-hashed passwords and role-based session gating
- Passenger: browse routes, live seat availability, book up to 4 seats, e-ticket,
  booking history
- Driver: today's assigned trips, real passenger manifest per trip, check-in by
  reference
- Admin: dashboard, trip creation, driver assignment (with real scheduling-conflict
  checks), and full data management (vans, routes, stops, users)

See `docs/SPECIFICATION.md` for the full requirements list and known limitations.

---

## Running locally

1. Install [XAMPP](https://www.apachefriends.org/).
2. Clone this repo into the XAMPP `htdocs` folder:
   ```
   htdocs/van-seat-reservation/
   ```
3. Start **Apache** and **MySQL** in the XAMPP control panel.
4. Create and seed the database. Connect with the MySQL monitor and run:
   ```
   mysql -u root -h 127.0.0.1 -P 3306
   ```
   then, from inside it:
   ```sql
   source db/schema.sql;
   source db/seed.sql;
   ```
   (Or pipe the files directly: `mysql -u root -h 127.0.0.1 -P 3306 < db/schema.sql`,
   then the same for `seed.sql`.)
5. Check `includes/db.php` — if your local MySQL root account has a password set
   (XAMPP's default is empty), update `DB_PASS` to match. This file is shared, so
   don't commit a personal password back to `main`.
6. Open the app in a browser:
   ```
   http://localhost/van-seat-reservation/public/
   ```

### Dev login

`seed.sql` creates 15 accounts (8 passengers, 6 drivers, 1 admin). Every account's
password is:

```
vanpass123
```

A few to log in as: `0913345776` (Jane Doe, passenger), `0924457781` (Patchara
Chainiyom, driver), `0917739007` (Chanyapat Saeng-Xuto, admin).

---

## Project structure

```
van-seat-reservation/
├── README.md
├── .gitignore
├── /public           # all pages (see docs/pages.md)
│   ├── /css            # style.css — shared design system
│   ├── /js
│   └── /assets         # logo, images, mockups
├── /includes         # shared layout, auth, and data-fetch functions
│   ├── header.php / footer.php / navbar.php / profile-shell.php
│   ├── db.php          # the one shared MySQL connection
│   ├── auth.php        # role_home(), require_role(), profile update
│   ├── today.php       # the shared "what day is it" for the demo
│   └── routes.php, schedule.php, bookings.php, vans.php,
│       driver-today.php, admin-today.php   # data-fetch functions per domain
├── /db               # schema.sql, seed.sql
└── /docs             # pages.md, design-tokens.md, DATABASE_DESIGN.md, SPECIFICATION.md
```

---

## How we work (Git)

- `main` always stays working
- Work on your own branch: `yourname/page-name` (e.g. `ongii/login-page`).
- Merge into `main` via **pull request**
- Pull `main` before starting new work each session.
- `includes/db.php` holds local DB credentials — if you change `DB_PASS` for your
  own machine, don't push that change to `main`.

---

## Documentation

- **`docs/pages.md`** — the filename for every page and shared include, plus the
  Canva-mockup-to-filename mapping.
- **`docs/design-tokens.md`** — the design system: colour, type scale, spacing,
  control sizing, layout widths.
- **`docs/DATABASE_DESIGN.md`** — schema, relationships, and the UI-to-table
  mapping.
- **`docs/SPECIFICATION.md`** — full functional and non-functional requirements,
  architecture, and known limitations.

---

## Scope

**In scope:** registration/login, browse trips by route and time, book up to 4
seats, view bookings, e-ticket with QR, driver check-in (mock scan + manual
reference), admin trip scheduling and driver assignment, and management of vans,
routes, stops, and users.

**Out of scope (deliberately):** real payment processing, seat *selection* (a
count, not a specific seat number), cancellation, email/SMS notifications, GPS
tracking. Payment method and QR scanning are simulated, not integrated.