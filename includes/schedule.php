<?php
/**
 * Shared departure slot data — backed by MySQL (mysqli).
 *
 * A "slot" is a scheduled trip a passenger can pick: its time, the van's
 * capacity, and live availability (capacity minus seats already booked).
 *
 *   get_slots($routeId) — the day's trips on a route, keyed by trip id, in
 *     the shape trip-times.php's departure grid expects.
 *   find_slot($tripId)  — one trip's slot, used by booking-confirm.php for
 *     the time and the seats-<=-available check.
 *
 * "Today" is the seed's demo date 2026-05-10 until a date picker / live
 * clock exists (date('Y-m-d') in production).
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/today.php';

/**
 * @return array<int, array{time: string, capacity: int, available: int}>
 */
function get_slots(?int $routeId = null): array
{
    $today = today_iso();

    $base = "
        SELECT
            t.id,
            DATE_FORMAT(t.depart_time, '%h : %i %p') AS time,
            v.seats AS capacity,
            v.seats - COALESCE(
                (SELECT SUM(b.seats) FROM bookings b WHERE b.trip_id = t.id), 0
            ) AS available
        FROM trips t
        JOIN vans v ON v.id = t.van_id
        WHERE t.trip_date = ?
    ";

    if ($routeId !== null) {
        $stmt = db()->prepare($base . " AND t.route_id = ? ORDER BY t.depart_time");
        $stmt->bind_param('si', $today, $routeId);
    } else {
        $stmt = db()->prepare($base . " ORDER BY t.depart_time");
        $stmt->bind_param('s', $today);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $slots = [];
    while ($row = $result->fetch_assoc()) {
        $slots[(int) $row['id']] = [
            'time'      => $row['time'],
            'capacity'  => (int) $row['capacity'],
            'available' => (int) $row['available'],
        ];
    }
    return $slots;
}

/**
 * One slot by trip id, or null if it does not exist.
 */
function find_slot(int $id): ?array
{
    $stmt = db()->prepare("
        SELECT
            t.route_id,
            DATE_FORMAT(t.trip_date,   '%e %b %Y')   AS date,
            DATE_FORMAT(t.depart_time, '%h : %i %p') AS time,
            v.seats AS capacity,
            v.seats - COALESCE(
                (SELECT SUM(b.seats) FROM bookings b WHERE b.trip_id = t.id), 0
            ) AS available
        FROM trips t
        JOIN vans v ON v.id = t.van_id
        WHERE t.id = ?
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row === null) {
        return null;
    }
    return [
        'route_id'  => (int) $row['route_id'],   // added so callers can confirm a trip really belongs to the route it's posted with
        'date'      => $row['date'],
        'time'      => $row['time'],
        'capacity'  => (int) $row['capacity'],
        'available' => (int) $row['available'],
    ];
}