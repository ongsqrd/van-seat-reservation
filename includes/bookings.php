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

function get_bookings(): array
{
    $userId    = 1;              // TODO: $_SESSION['user_id']
    $todayDate = '2026-05-10';   // TODO: date('Y-m-d')

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