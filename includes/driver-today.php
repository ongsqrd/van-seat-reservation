<?php
/**
 * includes/driver-today.php — backed by MySQL (mysqli).
 *
 * The logged-in driver's trips for today, in the exact shape
 * driver-dashboard.php expects: id, route_id, a formatted time, capacity
 * (the van's seats), boarded (seats checked in), and a 'next'/'upcoming'
 * flag. The earliest trip is flagged 'next' for the dashboard highlight.
 *
 * Two values are hardcoded until auth + a live clock exist:
 *   - driver:  $_SESSION['user_id'] later; for now the demo driver John Doe
 *              (users.id = 9), matching the name shown on the driver pages.
 *   - "today":  date('Y-m-d') in production; for now the seed's demo date
 *              2026-05-10, so the sample trips actually appear.
 */

require_once __DIR__ . '/db.php';

function get_todays_trips(): array
{
    $driverId  = 3;              // TODO: $_SESSION['user_id']
    $todayDate = '2026-05-10';   // TODO: date('Y-m-d')

    $stmt = db()->prepare("
        SELECT
            t.id,
            t.route_id,
            DATE_FORMAT(t.depart_time, '%h : %i %p') AS time,
            v.seats AS capacity,
            (SELECT COALESCE(SUM(b.seats), 0)
               FROM bookings b
              WHERE b.trip_id = t.id AND b.board_status = 'boarded') AS boarded
        FROM trips t
        JOIN vans v ON v.id = t.van_id
        WHERE t.driver_id = ? AND t.trip_date = ?
        ORDER BY t.depart_time
    ");
    $stmt->bind_param('is', $driverId, $todayDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $trips = [];
    while ($row = $result->fetch_assoc()) {
        $trips[] = [
            'id'       => (int) $row['id'],
            'route_id' => (int) $row['route_id'],
            'time'     => $row['time'],
            'capacity' => (int) $row['capacity'],
            'boarded'  => (int) $row['boarded'],
            'status'   => 'upcoming',
        ];
    }

    // the earliest trip today is the "next" one the dashboard highlights
    if (!empty($trips)) {
        $trips[0]['status'] = 'next';
    }

    return $trips;
}