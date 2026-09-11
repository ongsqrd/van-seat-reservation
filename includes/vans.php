<?php

require_once __DIR__ . '/db.php';

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