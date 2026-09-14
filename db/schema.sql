-- ============================================================
--  AU Van Seat Reservation — schema
--  CE4221 · Assumption University
--
--  Run once:  mysql -u root -h 127.0.0.1 -P 3306
--	mysql> source db/schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS au_van
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE au_van;

SET sql_safe_updates = 0;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS trips;
DROP TABLE IF EXISTS route_stops;
DROP TABLE IF EXISTS routes;
DROP TABLE IF EXISTS vans;
DROP TABLE IF EXISTS stops;
DROP TABLE IF EXISTS users;

-- ------------------------------------------------------------
-- users — passengers, drivers, admins
-- ------------------------------------------------------------
CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100) NOT NULL,
    phone         VARCHAR(20)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,           
    role          ENUM('passenger','driver','admin') NOT NULL DEFAULT 'passenger',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_phone (phone)              
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
-- routes 
-- From X to Y are derived from route_stops.seq
-- ------------------------------------------------------------
CREATE TABLE routes (
    id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,                    
    fare INT UNSIGNED NOT NULL,                    
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- route_stops 
-- seq = 1 is the origin, the highest seq is the terminus
-- ------------------------------------------------------------
CREATE TABLE route_stops (
    id       INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    route_id INT UNSIGNED     NOT NULL,
    stop_id  INT UNSIGNED     NOT NULL,
    seq      TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_route_seq  (route_id, seq),      
    UNIQUE KEY uq_route_stop (route_id, stop_id),  
    CONSTRAINT fk_rs_route FOREIGN KEY (route_id) REFERENCES routes (id) ON DELETE CASCADE,
    CONSTRAINT fk_rs_stop  FOREIGN KEY (stop_id)  REFERENCES stops  (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- vans
-- ------------------------------------------------------------
CREATE TABLE vans (
    id    INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    plate VARCHAR(20)      NOT NULL,
    seats TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- trips 
-- ------------------------------------------------------------
CREATE TABLE trips (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    route_id    INT UNSIGNED NOT NULL,
    van_id      INT UNSIGNED NOT NULL,
    driver_id   INT UNSIGNED NULL,                 
    trip_date   DATE         NOT NULL,
    depart_time TIME         NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_trips_when   (trip_date, depart_time), 
    KEY idx_trips_driver (driver_id),
    CONSTRAINT fk_trips_route  FOREIGN KEY (route_id)  REFERENCES routes (id) ON DELETE RESTRICT,
    CONSTRAINT fk_trips_van    FOREIGN KEY (van_id)    REFERENCES vans   (id) ON DELETE RESTRICT,
    CONSTRAINT fk_trips_driver FOREIGN KEY (driver_id) REFERENCES users  (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- bookings 
-- ------------------------------------------------------------
CREATE TABLE bookings (
    id              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    reference       VARCHAR(10)      NOT NULL,     
    trip_id         INT UNSIGNED     NOT NULL,
    user_id         INT UNSIGNED     NOT NULL,     
    dropoff_stop_id INT UNSIGNED     NOT NULL,
    seats           TINYINT UNSIGNED NOT NULL,
    payment_method  ENUM('promptpay','card') NOT NULL,
    total           INT UNSIGNED     NOT NULL,    
    board_status    ENUM('waiting','boarded') NOT NULL DEFAULT 'waiting',
    created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_bookings_reference (reference),
    KEY idx_bookings_trip (trip_id),             
    KEY idx_bookings_user (user_id),              
    CONSTRAINT chk_bookings_seats CHECK (seats BETWEEN 1 AND 4),
    CONSTRAINT fk_bookings_trip    FOREIGN KEY (trip_id)         REFERENCES trips (id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_user    FOREIGN KEY (user_id)         REFERENCES users (id) ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_dropoff FOREIGN KEY (dropoff_stop_id) REFERENCES stops (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET sql_safe_updates = 1;
