<?php

require_once __DIR__ . '/db.php';

function get_routes(): array
{
    static $routes = null;
    if ($routes !== null) {
        return $routes;
    }

    $sql = "
        SELECT
            r.id,
            r.name,
            r.fare,
            (SELECT s.name FROM route_stops rs JOIN stops s ON s.id = rs.stop_id
               WHERE rs.route_id = r.id ORDER BY rs.seq ASC  LIMIT 1) AS `from`,
            (SELECT s.name FROM route_stops rs JOIN stops s ON s.id = rs.stop_id
               WHERE rs.route_id = r.id ORDER BY rs.seq DESC LIMIT 1) AS `to`
        FROM routes r
        ORDER BY r.id
    ";

    $routes = [];
    $result = db()->query($sql);
    while ($row = $result->fetch_assoc()) {
        $routes[(int) $row['id']] = [
            'name' => $row['name'],
            'from' => $row['from'],
            'to'   => $row['to'],
            'fare' => (int) $row['fare'],
        ];
    }

    return $routes;
}

function find_route(int $id): ?array
{
    return get_routes()[$id] ?? null;
}

function route_detail(array $route): string
{
    return 'From ' . $route['from'] . ' to ' . $route['to'];
}

function get_dropoffs(?int $routeId = null): array
{
    $out = [];

    if ($routeId === null) {
        $result = db()->query("SELECT id, name FROM stops ORDER BY name");
        while ($row = $result->fetch_assoc()) {
            $out[(int) $row['id']] = $row['name'];
        }
        return $out;
    }

    $stmt = db()->prepare("
        SELECT s.id, s.name
        FROM route_stops rs
        JOIN stops s ON s.id = rs.stop_id
        WHERE rs.route_id = ? AND rs.seq > 1
        ORDER BY rs.seq
    ");
    $stmt->bind_param('i', $routeId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $out[(int) $row['id']] = $row['name'];
    }
    return $out;
}

function get_route_stop_chain(int $routeId): array
{
    $stmt = db()->prepare("
        SELECT rs.seq, s.id AS stop_id, s.name
        FROM route_stops rs
        JOIN stops s ON s.id = rs.stop_id
        WHERE rs.route_id = ?
        ORDER BY rs.seq
    ");
    $stmt->bind_param('i', $routeId);
    $stmt->execute();
    $result = $stmt->get_result();

    $chain = [];
    while ($row = $result->fetch_assoc()) {
        $chain[] = [
            'seq'     => (int) $row['seq'],
            'stop_id' => (int) $row['stop_id'],
            'name'    => $row['name'],
        ];
    }
    return $chain;
}

function find_or_create_stop(string $name): int
{
    $stmt = db()->prepare('SELECT id FROM stops WHERE name = ?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row !== null) {
        return (int) $row['id'];
    }

    $insert = db()->prepare('INSERT INTO stops (name) VALUES (?)');
    $insert->bind_param('s', $name);
    $insert->execute();
    return (int) db()->insert_id;
}