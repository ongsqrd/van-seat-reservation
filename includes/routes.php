<?php
/**
 * Shared route data.
 *
 * trips.php lists these; trip-times.php looks one up by ?route=<id>.
 * Keeping them here means the two pages can never disagree.
 *
 * In the PHP phase get_routes() becomes a SELECT on the routes table
 * and find_route() a WHERE route_id = ?. Nothing that calls them changes.
 */

/**
 * All routes, keyed by route id.
 *
 * @return array<int, array{name: string, from: string, to: string}>
 */
function get_routes(): array
{
    return [
        1 => ['name' => 'Assumption University', 'from' => 'Bangna',        'to' => 'Assumption U.'],
        2 => ['name' => 'Hua Mak Campus',        'from' => 'Assumption U.', 'to' => 'Hua Mak'],
        3 => ['name' => 'Bangna',                'from' => 'Assumption U.', 'to' => 'Bangna'],
        4 => ['name' => 'Assumption University', 'from' => 'Hua Mak',       'to' => 'Assumption U.'],
    ];
}

/**
 * One route by id, or null if the id does not exist.
 */
function find_route(int $id): ?array
{
    $routes = get_routes();

    return $routes[$id] ?? null;
}

/**
 * The subtitle line, e.g. "From Bangna to Assumption U."
 */
function route_detail(array $route): string
{
    return 'From ' . $route['from'] . ' to ' . $route['to'];
}