<?php
/**
 * includes/driver-today.php — backed by MySQL (mysqli).
 *
 *   get_todays_trips()        — the logged-in driver's trips today, in the
 *                                shape driver-dashboard.php expects.
 *   get_trip_manifest($tripId) — that trip's passengers, in the shape
 *                                driver-trip.php / driver-checkin.php expect.
 *
 * The driver comes from the session (require_role('driver') has already
 * run on every page that calls these, so $_SESSION['user_id'] is real).
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/today.php';

function get_todays_trips(): array
{
    $driverId = $_SESSION['user_id'] ?? null;
    if ($driverId === null) {
        return [];   // defensive — should never happen behind require_role()
    }
    $todayDate = today_iso();

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

/**
 * The passenger manifest for one trip: who's booked, how many seats,
 * where they're getting off, and whether they've boarded. The caller is
 * responsible for confirming the trip belongs to the current driver
 * (get_todays_trips() already does this — see driver-trip.php /
 * driver-checkin.php, which only reach this after that check passes).
 *
 * @return array<int, array{booking_id: int, reference: string, passenger: string, seats: int, dropoff: string, status: string}>
 */
function get_trip_manifest(int $tripId): array
{
    $stmt = db()->prepare("
        SELECT
            b.id AS booking_id,
            b.reference,
            u.name AS passenger,
            b.seats,
            s.name AS dropoff,
            b.board_status
        FROM bookings b
        JOIN users u ON u.id = b.user_id
        JOIN stops s ON s.id = b.dropoff_stop_id
        WHERE b.trip_id = ?
        ORDER BY b.created_at
    ");
    $stmt->bind_param('i', $tripId);
    $stmt->execute();
    $result = $stmt->get_result();

    $manifest = [];
    while ($row = $result->fetch_assoc()) {
        $manifest[] = [
            'booking_id' => (int) $row['booking_id'],
            'reference'  => $row['reference'],
            'passenger'  => $row['passenger'],
            'seats'      => (int) $row['seats'],
            'dropoff'    => $row['dropoff'],
            'status'     => $row['board_status'],
        ];
    }
    return $manifest;
}