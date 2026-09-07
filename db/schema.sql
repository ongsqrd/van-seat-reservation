-- ============================================================
--  AU Van Seat Reservation — schema
--  CE4221 · Assumption University
--
--  Build per docs/DATABASE_DESIGN.md. Tables are created in
--  foreign-key order (a table's referenced tables come first).
--  Engine InnoDB + utf8mb4 throughout (Thai names, plate e.g. กข 1234).
--
--  Run once:  mysql -u root -h 127.0.0.1 -P 3306 < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS au_van
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE au_van;

-- Drop in reverse FK order so re-running is clean during development.
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS trips;
DROP TABLE IF EXISTS route_stops;
DROP TABLE IF EXISTS routes;
DROP TABLE IF EXISTS vans;
DROP TABLE IF EXISTS stops;
DROP TABLE IF EXISTS users;

-- ------------------------------------------------------------
-- users — passengers, drivers, admins in one table (role-split)
-- ------------------------------------------------------------
CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100) NOT NULL,
    phone         VARCHAR(20)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,           -- password_hash() / bcrypt
    role          ENUM('passenger','driver','admin') NOT NULL DEFAULT 'passenger',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_phone (phone)              -- login identifier
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- stops — every boarding / drop-off point
-- ------------------------------------------------------------
CREATE TABLE stops (
    id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- routes — a fare + an ordered chain of stops (see route_stops).
-- Endpoints (From X to Y) are derived from route_stops.seq, not stored.
-- ------------------------------------------------------------
CREATE TABLE routes (
    id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,                    -- display headline (e.g. "Hua Mak Campus")
    fare INT UNSIGNED NOT NULL,                    -- whole baht per seat
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- route_stops — the ordered stops on a route.
-- seq = 1 is the origin; the highest seq is the terminus;
-- drop-off choices are the stops after the origin.
-- ------------------------------------------------------------
CREATE TABLE route_stops (
    id       INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    route_id INT UNSIGNED     NOT NULL,
    stop_id  INT UNSIGNED     NOT NULL,
    seq      TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_route_seq  (route_id, seq),      -- one stop per position
    UNIQUE KEY uq_route_stop (route_id, stop_id),  -- a stop appears once per route
    CONSTRAINT fk_rs_route FOREIGN KEY (route_id) REFERENCES routes (id) ON DELETE CASCADE,
    CONSTRAINT fk_rs_stop  FOREIGN KEY (stop_id)  REFERENCES stops  (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- vans — the fleet; seats is the trip capacity
-- ------------------------------------------------------------
CREATE TABLE vans (
    id    INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    plate VARCHAR(20)      NOT NULL,
    seats TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- trips — one scheduled van run.
-- capacity is NOT stored; it is vans.seats for van_id.
-- driver_id NULL is the "Unassigned" state.
-- ------------------------------------------------------------
CREATE TABLE trips (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    route_id    INT UNSIGNED NOT NULL,
    van_id      INT UNSIGNED NOT NULL,
    driver_id   INT UNSIGNED NULL,                 -- NULL = needs a driver
    trip_date   DATE         NOT NULL,
    depart_time TIME         NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_trips_when   (trip_date, depart_time), -- "today's trips", ordered
    KEY idx_trips_driver (driver_id),
    CONSTRAINT fk_trips_route  FOREIGN KEY (route_id)  REFERENCES routes (id) ON DELETE RESTRICT,
    CONSTRAINT fk_trips_van    FOREIGN KEY (van_id)    REFERENCES vans   (id) ON DELETE RESTRICT,
    CONSTRAINT fk_trips_driver FOREIGN KEY (driver_id) REFERENCES users  (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- bookings — a passenger's 1-4 seats on a trip.
-- Two identifiers: id ("Booking ID") + reference ("Reference No.").
-- The manifest is this table filtered by trip_id.
-- ------------------------------------------------------------
CREATE TABLE bookings (
    id              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    reference       VARCHAR(10)      NOT NULL,     -- e.g. F134WD24A (varies 8-9 chars)
    trip_id         INT UNSIGNED     NOT NULL,
    user_id         INT UNSIGNED     NOT NULL,     -- the passenger
    dropoff_stop_id INT UNSIGNED     NOT NULL,
    seats           TINYINT UNSIGNED NOT NULL,
    payment_method  ENUM('promptpay','card') NOT NULL,
    total           INT UNSIGNED     NOT NULL,     -- snapshot of fare x seats
    board_status    ENUM('waiting','boarded') NOT NULL DEFAULT 'waiting',
    created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_bookings_reference (reference),
    KEY idx_bookings_trip (trip_id),               -- manifest / seats sold
    KEY idx_bookings_user (user_id),               -- "my bookings"
    CONSTRAINT chk_bookings_seats CHECK (seats BETWEEN 1 AND 4),
    CONSTRAINT fk_bookings_trip    FOREIGN KEY (trip_id)         REFERENCES trips (id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_user    FOREIGN KEY (user_id)         REFERENCES users (id) ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_dropoff FOREIGN KEY (dropoff_stop_id) REFERENCES stops (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
