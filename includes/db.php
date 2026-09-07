<?php
/**
 * includes/db.php
 *
 * One MySQL connection (mysqli) shared for the whole request. Any page or
 * data include that needs the database calls db() and gets the same handle.
 *
 * mysqli is set to THROW on error (mysqli_sql_exception) instead of quietly
 * returning false, so a bad connection or query fails loudly with a message
 * you can read — much easier to debug than a blank page.
 */

// --- credentials --------------------------------------------------------
// XAMPP's defaults are user 'root' with an EMPTY password, MySQL on
// localhost port 3306. If you set a MySQL root password, put it in DB_PASS.
// Keep real passwords out of version control.
const DB_HOST = '127.0.0.1';   // 127.0.0.1 forces a TCP connection; if it fails, try 'localhost'
const DB_PORT = 3306;
const DB_NAME = 'au_van';
const DB_USER = 'root';
const DB_PASS = '';

/**
 * Return the shared mysqli connection, opening it once per request.
 * Throws mysqli_sql_exception if the connection can't be made.
 */
function db(): mysqli
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;                 // already connected this request — reuse it
    }

    // make mysqli raise exceptions rather than warnings + false returns
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    $conn->set_charset('utf8mb4');    // match the schema so Thai text round-trips

    return $conn;
}