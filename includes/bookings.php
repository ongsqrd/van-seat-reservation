<?php
/**
 * includes/bookings.php
 *
 * Placeholder booking history. No bookings table exists yet, so this
 * stands in the way routes.php / schedule.php do — the page consumes
 * get_bookings() and never hardcodes the list itself.
 *
 * Each booking points at a route and a slot by id, so From/To and the
 * boarding time resolve through find_route() / find_slot() and can't
 * drift from the trip. When the DB lands, this becomes a query scoped
 * to the logged-in passenger.
 */

function get_bookings(): array
{
    return [
        [
            'reference' => 'F134WD24A',
            'route_id'  => 1,
            'trip_id'   => 1,
            'date'      => '10 May 2026',
            'seats'     => 3,
            'status'    => 'upcoming',
        ],
        [
            'reference' => 'HQE34EF2',
            'route_id'  => 2,
            'trip_id'   => 2,
            'date'      => '11 May 2026',
            'seats'     => 2,
            'status'    => 'upcoming',
        ],
        [
            'reference' => 'A72KD91C',
            'route_id'  => 1,
            'trip_id'   => 3,
            'date'      => '2 May 2026',
            'seats'     => 1,
            'status'    => 'completed',
        ],
        [
            'reference' => 'B65QP04E',
            'route_id'  => 2,
            'trip_id'   => 4,
            'date'      => '28 Apr 2026',
            'seats'     => 4,
            'status'    => 'completed',
        ],
    ];
}