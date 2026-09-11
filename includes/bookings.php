<?php
/**
 * includes/bookings.php — backed by MySQL (mysqli).
 *
 * The logged-in passenger's bookings, in the shape my-bookings.php expects:
 * reference, route_id (for find_route), trip_id, date, time, seats, and a
 * derived 'upcoming'/'completed' status. The trip's date and time come from
 * the joined trips row, so the card no longer needs a separate find_slot().
 *
 * Hardcoded until auth + a live clock exist:
 *   - passenger:  $_SESSION['user_id'] later; for now the demo passenger
 *                 Jane Doe (users.id = 1), matching the passenger pages.
 *   - "today":    date('Y-m-d') in production; for now the seed's demo date
 *                 2026-05-10 — trips before it are Completed, on/after Upcoming.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/today.php';

function get_bookings(): array
{

    $userId    = $_SESSION['user_id'] ?? null;
    $todayDate = today_iso();

    if ($userId === null) {
        return [];   
    }

    $stmt = db()->prepare("
        SELECT
            b.reference,
            b.trip_id,
            b.seats,
            t.route_id,
            DATE_FORMAT(t.trip_date,   '%e %b %Y')   AS date,
            DATE_FORMAT(t.depart_time, '%h : %i %p') AS time,
            CASE WHEN t.trip_date < ? THEN 'completed' ELSE 'upcoming' END AS status
        FROM bookings b
        JOIN trips t ON t.id = b.trip_id
        WHERE b.user_id = ?
        ORDER BY t.trip_date, t.depart_time
    ");
    $stmt->bind_param('si', $todayDate, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            'reference' => $row['reference'],
            'route_id'  => (int) $row['route_id'],
            'trip_id'   => (int) $row['trip_id'],
            'date'      => $row['date'],
            'time'      => $row['time'],
            'seats'     => (int) $row['seats'],
            'status'    => $row['status'],
        ];
    }

    return $bookings;
}

function validate_booking_request(int $routeId, int $tripId, string $dropoffKey, int $seats): ?array
{
    require_once __DIR__ . '/routes.php';
    require_once __DIR__ . '/schedule.php';

    $route    = find_route($routeId);
    $slot     = find_slot($tripId);
    $dropoffs = get_dropoffs($routeId);
    $dropoff  = $dropoffs[$dropoffKey] ?? null;

    if ($route === null || $slot === null || $dropoff === null
        || $seats < 1 || $seats > 4 || $seats > $slot['available']
        || $slot['route_id'] !== $routeId) {   // the trip must actually belong to the posted route
        return null;
    }

    return ['route' => $route, 'slot' => $slot, 'dropoff' => $dropoff];
}

function generate_booking_reference(): string
{
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $candidate = strtoupper(substr(bin2hex(random_bytes(6)), 0, 9));
        if (find_booking($candidate) === null) {
            return $candidate;
        }
    }
    throw new RuntimeException('Could not generate a unique booking reference');
}

function find_booking(string $reference): ?array
{
    $stmt = db()->prepare("
        SELECT
            b.id AS booking_id,
            b.reference,
            b.user_id,
            u.name AS passenger,
            b.trip_id,
            t.route_id,
            DATE_FORMAT(t.trip_date,   '%e %b %Y')   AS date,
            DATE_FORMAT(t.depart_time, '%h : %i %p') AS boarding,
            s.name AS dropoff,
            b.seats,
            b.total,
            v.plate,
            DATE_FORMAT(b.created_at, '%e %b %Y  %h:%i %p') AS booked_at,
            b.board_status
        FROM bookings b
        JOIN trips t ON t.id = b.trip_id
        JOIN vans  v ON v.id = t.van_id
        JOIN stops s ON s.id = b.dropoff_stop_id
        JOIN users u ON u.id = b.user_id
        WHERE b.reference = ?
    ");
    $stmt->bind_param('s', $reference);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row === null) {
        return null;
    }

    return [
        'booking_id' => (int) $row['booking_id'],
        'reference'  => $row['reference'],
        'user_id'    => (int) $row['user_id'],
        'passenger'  => $row['passenger'],
        'trip_id'    => (int) $row['trip_id'],
        'route_id'   => (int) $row['route_id'],
        'date'       => $row['date'],
        'boarding'   => $row['boarding'],
        'dropoff'    => $row['dropoff'],
        'seats'      => (int) $row['seats'],
        'total'      => (int) $row['total'],
        'plate'      => $row['plate'],
        'booked_at'  => $row['booked_at'],
        'board_status' => $row['board_status'],
    ];
}