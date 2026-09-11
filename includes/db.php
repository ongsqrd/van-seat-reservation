<?php

const DB_HOST = '127.0.0.1';  
const DB_PORT = 3306;
const DB_NAME = 'au_van';
const DB_USER = 'root';
const DB_PASS = '';

function db(): mysqli
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;                 
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    $conn->set_charset('utf8mb4');    

    return $conn;
}