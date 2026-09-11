<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/today.php';

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
        'route_id'  => (int) $row['route_id'],   
        'time'      => $row['time'],
        'capacity'  => (int) $row['capacity'],
        'available' => (int) $row['available'],
    ];
}