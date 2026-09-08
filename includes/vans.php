<?php
/**
 * includes/vans.php
 *
 * The fleet. Shared by admin-trips.php's create-trip dropdown and (later)
 * admin-manage.php's Vans tab, so both read the same source.
 */

require_once __DIR__ . '/db.php';

/**
 * @return array<int, array{plate: string, seats: int}>
 */
function get_vans(): array
{
    $vans = [];
    $result = db()->query('SELECT id, plate, seats FROM vans ORDER BY plate');
    while ($row = $result->fetch_assoc()) {
        $vans[(int) $row['id']] = [
            'plate' => $row['plate'],
            'seats' => (int) $row['seats'],
        ];
    }
    return $vans;
}

function find_van(int $id): ?array
{
    return get_vans()[$id] ?? null;
}