# AU Van Seat Reservation System

A web-based van seat reservation system for Assumption University, built for
**CE4221  Network Application and Technology**.

Students reserve seats on university van trips in advance, drivers check passengers
in, and the transport office assigns drivers to trips.

---

## Team

| Student ID | Name | Role |
|---|---|---|
|6711021| _Chanyapat Saeng-Xuto_ |  |
|6711038| _Panupong Jaimethumde_ |  |
|6711164| _Patchara Chainiyom_ |  |

---

## Tech stack

- **HTML, CSS, JavaScript** — front end
- **PHP** — server-side logic (introduced later in the course)
- **MySQL** — database
- **XAMPP** (Apache + PHP + MySQL) — local environment

---

## Project status

UI-first build. We are constructing the complete front end (all pages, static,
with placeholder data) before the database, per course requirement. PHP and MySQL
integration come in a later phase.

---

## Running locally

1. Install [XAMPP](https://www.apachefriends.org/).
2. Clone this repo into the XAMPP `htdocs` folder:
   ```
   htdocs/ce4221-van-reservation/
   ```
3. Start **Apache** in the XAMPP control panel.
4. Open the app in a browser:
   ```
   http://localhost/ce4221-van-reservation/public/
   ```

(Database setup instructions will be added when the schema is built.)

---

## Project structure

```
ce4221-van-reservation/
├── README.md
├── .gitignore
├── /public          # all pages (see docs/PAGES.md)
│   ├── /css          # style.css — shared design system
│   ├── /js
│   └── /assets       # logo, images
├── /includes         # navbar.php, header.php, footer.php (shared)
└── /docs             # PAGES.md, DATABASE_DESIGN.md, mockups
```

---

## How we work (Git)

- `main` always stays working
- Work on your own branch: `yourname/page-name` (e.g. `ongii/login-page`).
- Merge into `main` via **pull request**
- Pull `main` before starting new work each session.

---

## Documentation

- **`docs/PAGES.md`** — the filename for every page. Build against these names.
- **`docs/DATABASE_DESIGN.md`** — tables, relationships, and data types (for later).

---

## Scope

**In scope:** registration/login, browse trips by route and time, book up to 4
seats, view bookings, e-ticket with QR, driver check-in (mock scan), admin trip
management and driver assignment.

**Out of scope (deliberately):** real payment processing, seat selection,
cancellation, email/SMS, GPS tracking. Payment and QR scanning are simulated by
design.