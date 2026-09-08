<?php
/**
 * includes/admin-today.php
 *
 * Admin's view of today's schedule — every trip (not scoped to one
 * driver, unlike driver-today.php), and driver availability for the
 * assign popup. Named admin-today, not admin-trips, so it can never
 * collide with the admin-trips.php page (see pages.md's naming rule).
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/today.php';

/**
 * Every trip scheduled for a given date (today by default), with its
 * assigned driver if any.
 *
 * @return array<int, array{id: int, route_id: int, driver_id: ?int, driver: ?string, time: string, raw_time: string}>
 */
function get_admin_trips(?string $date = null): array
{
    $date ??= today_iso();

    $stmt = db()->prepare("
        SELECT
            t.id,
            t.route_id,
            t.driver_id,
            u.name AS driver_name,
            DATE_FORMAT(t.depart_time, '%h : %i %p') AS time,
            t.depart_time AS raw_time
        FROM trips t
        LEFT JOIN users u ON u.id = t.driver_id
        WHERE t.trip_date = ?
        ORDER BY t.depart_time
    ");
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $trips = [];
    while ($row = $result->fetch_assoc()) {
        $trips[] = [
            'id'        => (int) $row['id'],
            'route_id'  => (int) $row['route_id'],
            'driver_id' => $row['driver_id'] !== null ? (int) $row['driver_id'] : null,
            'driver'    => $row['driver_name'],
            'time'      => $row['time'],
            'raw_time'  => $row['raw_time'],
        ];
    }
    return $trips;
}

/**
 * Every driver, flagged available/unavailable for a specific trip slot:
 * a driver is unavailable if they're already assigned to a DIFFERENT
 * trip at that exact date + time (a genuine scheduling conflict, not
 * just "has any trip today"). $excludeTripId lets re-opening the assign
 * popup on a trip not mark that trip's own current driver as conflicting
 * with themselves.
 *
 * @return array<int, array{id: int, name: string, available: bool, status: string}>
 */
function get_available_drivers(string $date, string $time, int $excludeTripId = 0): array
{
    $stmt = db()->prepare("
        SELECT
            u.id,
            u.name,
            (SELECT COUNT(*) FROM trips t2
              WHERE t2.driver_id = u.id
                AND t2.trip_date = ?
                AND t2.depart_time = ?
                AND t2.id != ?) AS conflict_count
        FROM users u
        WHERE u.role = 'driver'
        ORDER BY u.name
    ");
    $stmt->bind_param('ssi', $date, $time, $excludeTripId);
    $stmt->execute();
    $result = $stmt->get_result();

    $drivers = [];
    while ($row = $result->fetch_assoc()) {
        $available = ((int) $row['conflict_count']) === 0;
        $drivers[] = [
            'id'        => (int) $row['id'],
            'name'      => $row['name'],
            'available' => $available,
            'status'    => $available ? 'Free' : 'Assigned',
        ];
    }
    return $drivers;
}