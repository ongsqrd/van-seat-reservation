# Pages — Van Seat Reservation System

Shared filename reference. **Everyone builds against these names** so we don't end
up with `booking.php`, `book.php`, and `bookingPage.php` for the same screen.

## Naming rules
- all lowercase, hyphens for multi-word (`my-bookings.php`, not `myBookings.php`)
- `.php` extension throughout, even while static (avoids renaming and broken links later)
- noun-based, matching what the page *is*

---

## Public (logged out)

| File | Page | Owner |
|---|---|---|
| `index.php` | Router: trips if logged in, else login | |
| `landing.php` | Public landing (AU VAN BOOKING intro) | |
| `login.php` | Login | |
| `register.php` | Create account | |
| `logout.php` | Logout — clears session, redirects (no UI) | |

## Passenger

| File | Page | Owner |
|---|---|---|
| `trips.php` | Route selection (passenger home) | |
| `trip-times.php` | Time + seats selection for a chosen route | |
| `booking-confirm.php` | Booking summary + payment | |
| `booking-success.php` | "Booking confirmed" screen | |
| `ticket.php` | Booking details / e-ticket | |
| `my-bookings.php` | Trip history | |
| `profile.php` | Passenger profile | |

## Driver

| File | Page | Owner |
|---|---|---|
| `driver-dashboard.php` | Today's trips | |
| `driver-trip.php` | Trip details + manifest | |
| `driver-checkin.php` | Check-in (mock scan + manual entry) | |
| `driver-profile.php` | Driver profile | |

## Admin

| File | Page | Owner |
|---|---|---|
| `admin-dashboard.php` | Admin hub | |
| `admin-trips.php` | Trip list + create + assign-driver popup | |
| `admin-manage.php` | Manage data (vans / routes / stops / users tabs) | |
| `admin-profile.php` | Admin profile | |

## Shared includes (fragments, not pages)

| File | Purpose |
|---|---|
| `includes/navbar.php` | Role-aware navigation |
| `includes/header.php` | `<head>`, stylesheet link, opening tags |
| `includes/footer.php` | Closing tags |

---

## Design label -> filename mapping

The Canva mockups use different labels. Map them to filenames here:

| Canva label | Filename |
|---|---|
| landing-page | `landing.php` |
| login-page | `login.php` |
| registration-page | `register.php` |
| route-selection | `trips.php` |
| time-selection | `trip-times.php` |
| confirm-booking | `booking-confirm.php` |
| booking confirmed | `booking-success.php` |
| ticket-page | `ticket.php` |
| booking history | `my-bookings.php` |
| profile: passenger | `profile.php` |
| driver-dashboard | `driver-dashboard.php` |
| driver-trip-details | `driver-trip.php` |
| driver-trip-checkin | `driver-checkin.php` |
| profile: driver | `driver-profile.php` |
| admin-dashboard | `admin-dashboard.php` |
| admin-trips | `admin-trips.php` |
| admin-manage: * | `admin-manage.php` |
| profile: admin | `admin-profile.php` |

---
