<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/today.php';

function get_todays_trips(): array
{
    $driverId = $_SESSION['user_id'] ?? null;
    if ($driverId === null) {
        return [];   
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

    if (!empty($trips)) {
        $trips[0]['status'] = 'next';
    }

    return $trips;
}

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