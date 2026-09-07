<?php
/**
 * includes/today.php
 *
 * The single "what day is it" for the whole demo. Three separate files
 * (driver-today.php, bookings.php, schedule.php) each hardcoded their own
 * copy of the seed's demo date — a drift risk if it ever needs to change.
 * Everything now reads from here instead.
 *
 * TODO once the demo period is behind a live clock: return date('Y-m-d')
 * / date('j M Y') instead of the fixed strings.
 */

function today_iso(): string
{
    return '2026-05-10';
}

function today_label(): string
{
    return '10 May 2026';
}