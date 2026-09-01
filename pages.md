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

| Canva label | Filename | Title|
|---|---| ---|
| landing-page | `landing.php` | AU VAN |
| login-page | `login.php` | AU VAN - Login |
| registration-page | `register.php` | AU VAN - Register |
| route-selection | `trips.php` | AU VAN - Route |
| time-selection | `trip-times.php` | AU VAN - Time |
| confirm-booking | `booking-confirm.php` | AU VAN - Booking |
| booking confirmed | `booking-success.php` | AU VAN - Confirmed |
| ticket-page | `ticket.php` | AU VAN - Ticket |
| booking history | `my-bookings.php` | AU VAN - History |
| profile: passenger | `profile.php` | AU VAN - Passenger |
| driver-dashboard | `driver-dashboard.php` | AU VAN - Dashboard |
| driver-trip-details | `driver-trip.php` |  AU VAN - Details |
| driver-trip-checkin | `driver-checkin.php` | AU VAN - Check-in |
| profile: driver | `driver-profile.php` | AU VAN - Driver |
| admin-dashboard | `admin-dashboard.php` | AU VAN - Dashboard |
| admin-trips | `admin-trips.php` | AU VAN - Trips |
| admin-manage: * | `admin-manage.php` | AU VAN - Manage |
| profile: admin | `admin-profile.php` | AU VAN - Admin |

---
