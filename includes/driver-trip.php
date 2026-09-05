<?php
/**
 * includes/driver-trips.php
 *
 * Placeholder for the logged-in driver's assigned trips today. Stands in
 * the way routes.php / bookings.php do until the trips table exists.
 *
 * Each trip references a route by id, so From/To resolve through
 * find_route() and can't drift. 'boarded' is how many of 'capacity'
 * passengers have checked in. 'status' flags the imminent trip ('next')
 * versus the rest ('upcoming'). When the DB lands this becomes a query
 * for trips assigned to the current driver on the current date.
 */

function get_todays_trips(): array
{
    return [
        [
            'id'       => 1,
            'route_id' => 3,          // -> find_route(): From / To
            'time'     => '12 : 00 PM',
            'capacity' => 12,
            'boarded'  => 8,
            'status'   => 'next',
        ],
        [
            'id'       => 2,
            'route_id' => 2,
            'time'     => '01 : 30 PM',
            'capacity' => 12,
            'boarded'  => 4,
            'status'   => 'upcoming',
        ],
    ];
}