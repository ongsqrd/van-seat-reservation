<?php
/**
 * Shared departure slot data.
 *
 * trip-times.php lists these; booking-confirm.php looks one up by id.
 * Same shape as routes.php so both read the same way.
 *
 * In the PHP phase get_slots() becomes a query joining trips and bookings
 * to work out live availability. Nothing that calls it changes.
 */

/**
 * All departure slots, keyed by trip id.
 *
 * @return array<int, array{time: string, capacity: int, available: int}>
 */
function get_slots(): array
{
    return [
        1 => ['time' => '08 : 00 AM', 'capacity' => 14, 'available' => 3 ],
        2 => ['time' => '09 : 00 AM', 'capacity' => 14, 'available' => 10],
        3 => ['time' => '10 : 00 AM', 'capacity' => 14, 'available' => 5 ],
        4 => ['time' => '11 : 00 AM', 'capacity' => 14, 'available' => 7 ],
        5 => ['time' => '12 : 00 PM', 'capacity' => 14, 'available' => 12],
        6 => ['time' => '01 : 30 PM', 'capacity' => 14, 'available' => 12],
        7 => ['time' => '03 : 00 PM', 'capacity' => 14, 'available' => 5 ],
        8 => ['time' => '04 : 30 PM', 'capacity' => 14, 'available' => 0 ],
    ];
}

/**
 * One slot by id, or null if the id does not exist.
 */
function find_slot(int $id): ?array
{
    $slots = get_slots();

    return $slots[$id] ?? null;
}