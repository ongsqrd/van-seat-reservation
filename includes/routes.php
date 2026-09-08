<?php
/**
 * Shared route data — backed by MySQL (mysqli).
 *
 * Routes are stored normalised: routes(id, name, fare) plus an ordered
 * route_stops chain. get_routes() rebuilds the flat shape the pages expect
 * (from = first stop, to = last stop), so nothing that calls these changes.
 */

require_once __DIR__ . '/db.php';

/**
 * All routes, keyed by route id. Queried once, then cached for the request.
 *
 * @return array<int, array{name: string, from: string, to: string, fare: int}>
 */
function get_routes(): array
{
    static $routes = null;
    if ($routes !== null) {
        return $routes;
    }

    // origin = the seq-1 stop; terminus = the highest-seq stop
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

/**
 * One route by id, or null if it does not exist.
 */
function find_route(int $id): ?array
{
    return get_routes()[$id] ?? null;
}

/**
 * The subtitle line, e.g. "From Bangna to Assumption U."
 * Pure formatting — no database access.
 */
function route_detail(array $route): string
{
    return 'From ' . $route['from'] . ' to ' . $route['to'];
}

/**
 * Drop-off points, keyed by stop id so the value maps straight onto
 * bookings.dropoff_stop_id.
 *
 * With a route id: that route's stops after the origin, in travel order —
 * the correct per-route drop-offs. Without one: every stop, ordered by
 * name — a safe fallback so a caller not yet passing the id still works.
 *
 * @return array<int, string>  stop id => stop name
 */
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

/**
 * The full ordered stop chain for a route (including the origin, seq 1)
 * — what the admin Stops tab shows. Unlike get_dropoffs(), which starts
 * after the origin (a passenger's actual choices), this is the complete
 * chain for managing a route's stops.
 *
 * @return array<int, array{seq: int, stop_id: int, name: string}>
 */
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

/**
 * The id of a stop matching this name exactly, or a newly-created one if
 * no such stop exists yet. Used by the admin Stops tab's "Enter New Stop"
 * field, which lets the admin type a name without first checking whether
 * that stop already exists elsewhere in the system.
 */
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