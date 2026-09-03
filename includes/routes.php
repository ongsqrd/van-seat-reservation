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
 * @return array<int, array{name: string, from: string, to: string, fare: int}>
 */
function get_routes(): array
{
    return [
        1 => ['name' => 'Assumption University', 'from' => 'Bangna',        'to' => 'Assumption U.', 'fare' => 40],
        2 => ['name' => 'Hua Mak Campus',        'from' => 'Assumption U.', 'to' => 'Hua Mak',       'fare' => 40],
        3 => ['name' => 'Bangna',                'from' => 'Assumption U.', 'to' => 'Bangna',        'fare' => 40],
        4 => ['name' => 'Assumption University', 'from' => 'Hua Mak',       'to' => 'Assumption U.', 'fare' => 40],
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

/**
 * Drop-off points, keyed by the value used in the select.
 *
 * Flat for now. The stops table has these per route, so this becomes
 * get_dropoffs(int $routeId) once the data is in.
 *
 * @return array<string, string>
 */
function get_dropoffs(): array
{
    return [
        'bangna'         => 'Bangna',
        'mega-bangna'    => 'Mega Bangna',
        'market-village' => 'Market Village',
        'paradise-park'  => 'Paradise Park',
    ];
}