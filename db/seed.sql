-- ============================================================
--  AU Van Seat Reservation — seed data
--  CE4221 · Assumption University
--
--  Run AFTER schema.sql:  mysql -u root -h 127.0.0.1 -P 3306 au_van < db/seed.sql
--
--  Team roles for the demo: Panupong = passenger, Patchara = driver,
--  Chanyapat = admin. Patchara drives trips 3 and 10 (trip 3 is the
--  Jane + Watson manifest used in the check-in demo).
--
--  Fully consistent: every FK resolves, every trip's driver is a driver,
--  every booking's passenger is a passenger, no driver is booked on a
--  trip they drive, drop-offs are on-route, totals = fare x seats.
--
--  DEV LOGIN: every account's password is  vanpass123
-- ============================================================

USE au_van;

DELETE FROM bookings;
DELETE FROM trips;
DELETE FROM route_stops;
DELETE FROM vans;
DELETE FROM routes;
DELETE FROM stops;
DELETE FROM users;

-- ------------------------------------------------------------
-- users  (8 passengers, 6 drivers, 1 admin)
--   project members: #3 Patchara (driver), #14 Panupong (passenger),
--                     #15 Chanyapat (admin)
-- ------------------------------------------------------------
INSERT INTO users (id, name, phone, password_hash, role) VALUES
  ( 1, 'Jane Doe',             '0913345776', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'passenger'),
  ( 2, 'John Watson',          '0901182245', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'passenger'),
  ( 3, 'Patchara Chainiyom',   '0924457781', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'driver'),
  ( 4, 'Somchai Prasert',      '0812206635', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'passenger'),
  ( 5, 'Ananya Kittikul',      '0839071120', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'passenger'),
  ( 6, 'Kevin Tan',            '0956624013', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'passenger'),
  ( 7, 'Mei Lin',              '0843389902', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'passenger'),
  ( 8, 'Arthur Chen',          '0875517788', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'passenger'),
  ( 9, 'John Doe',             '0911234567', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'driver'),
  (10, 'Sherlock Holmes',      '0982347593', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'driver'),
  (11, 'Molly Hooper',         '0865520198', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'driver'),
  (12, 'James Moriaty',        '0876403321', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'driver'),
  (13, 'Bobby Brown',          '0897715064', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'driver'),
  (14, 'Panupong Jaimethumde', '0930064471', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'passenger'),
  (15, 'Chanyapat Saeng-Xuto', '0917739007', '$2b$10$XrJHXqVrDD8eQqoFX2E6v.sBTN9c1iRP5NezG7jtojf7Ja5XpGbuO', 'admin');

-- ------------------------------------------------------------
-- stops
-- ------------------------------------------------------------
INSERT INTO stops (id, name) VALUES
  (1, 'Assumption U.'),
  (2, 'Bangna'),
  (3, 'Hua Mak'),
  (4, 'Bangna Junction'),
  (5, 'Mega Bangna'),
  (6, 'Market Village');

-- ------------------------------------------------------------
-- routes  (name is the display headline)
-- ------------------------------------------------------------
INSERT INTO routes (id, name, fare) VALUES
  (1, 'Assumption University', 40),   -- Bangna -> Assumption U.
  (2, 'Hua Mak Campus',        40),   -- Assumption U. -> Hua Mak
  (3, 'Bangna',                40),   -- Assumption U. -> Bangna  (the only multi-stop route)
  (4, 'Assumption University', 40);   -- Hua Mak -> Assumption U.

-- ------------------------------------------------------------
-- route_stops  (seq 1 = origin, highest seq = terminus)
--   Only route 3 has intermediate drop-offs.
-- ------------------------------------------------------------
INSERT INTO route_stops (route_id, stop_id, seq) VALUES
  (1, 2, 1), (1, 1, 2),                                    -- Route 1: Bangna -> Assumption U.
  (2, 1, 1), (2, 3, 2),                                    -- Route 2: Assumption U. -> Hua Mak
  (3, 1, 1), (3, 6, 2), (3, 5, 3), (3, 4, 4), (3, 2, 5),   -- Route 3: AU -> Market Village -> Mega Bangna -> Bangna Junction -> Bangna
  (4, 3, 1), (4, 1, 2);                                    -- Route 4: Hua Mak -> Assumption U.

-- ------------------------------------------------------------
-- vans  (10; mostly 15-seaters, two 12-seaters)
-- ------------------------------------------------------------
INSERT INTO vans (id, plate, seats) VALUES
  ( 1, 'กข 1234', 15),
  ( 2, 'พส 6767', 15),
  ( 3, 'งจ 4521', 15),
  ( 4, 'ทล 8890', 12),
  ( 5, 'บม 3345', 15),
  ( 6, 'กก 7712', 15),
  ( 7, 'ฉช 1096', 12),
  ( 8, 'ผด 5540', 15),
  ( 9, 'หน 2278', 15),
  (10, 'รอ 9903', 15);

-- ------------------------------------------------------------
-- trips  (15)
--   #1-#10 are 2026-05-10 ("today"); #4, #6, #9 have no driver.
--   Patchara (#3) drives trips 3 and 10.  #11-#13 past, #14-#15 future.
-- ------------------------------------------------------------
INSERT INTO trips (id, route_id, van_id, driver_id, trip_date, depart_time) VALUES
  ( 1, 1,  1, 10,   '2026-05-10', '08:00:00'),
  ( 2, 4,  2,  9,   '2026-05-10', '09:00:00'),
  ( 3, 3,  1,  3,   '2026-05-10', '12:00:00'),   -- Patchara
  ( 4, 2,  2, NULL, '2026-05-10', '13:30:00'),   -- needs a driver
  ( 5, 1,  3, 11,   '2026-05-10', '15:00:00'),
  ( 6, 2,  4, NULL, '2026-05-10', '16:30:00'),   -- needs a driver
  ( 7, 3,  5, 12,   '2026-05-10', '07:30:00'),
  ( 8, 4,  6, 13,   '2026-05-10', '10:30:00'),
  ( 9, 1,  7, NULL, '2026-05-10', '17:00:00'),   -- needs a driver
  (10, 2,  8,  3,   '2026-05-10', '11:00:00'),   -- Patchara 
  (11, 2,  1,  9,   '2026-05-02', '11:00:00'),   -- past
  (12, 3,  2, 10,   '2026-04-28', '09:00:00'),   -- past
  (13, 1,  3, 11,   '2026-05-05', '08:00:00'),   -- past
  (14, 4,  9, 12,   '2026-05-11', '09:00:00'),   -- future
  (15, 3, 10, 13,   '2026-05-12', '12:00:00');   -- future

-- ------------------------------------------------------------
-- bookings  (15)
--   trip #3: Jane (3 seats, boarded) + Watson (1, waiting).
--   Panupong (#14) holds the two bookings.
-- ------------------------------------------------------------
INSERT INTO bookings
  (reference, trip_id, user_id, dropoff_stop_id, seats, payment_method, total, board_status) VALUES
  ('F134WD24A',  3,  1, 4, 3, 'promptpay', 120, 'boarded'),  -- Jane     -> Bangna Junction
  ('JW77XK2Q',   3,  2, 5, 1, 'card',       40, 'waiting'),  -- Watson   -> Mega Bangna
  ('HQE34EF2',  10,  1, 3, 2, 'card',        80, 'waiting'), -- Jane     -> Hua Mak (upcoming)
  ('A72KD91C',  11,  1, 3, 1, 'promptpay',   40, 'boarded'), -- Jane     -> Hua Mak (completed)
  ('B65QP04E',  12,  1, 2, 4, 'card',       160, 'boarded'), -- Jane     -> Bangna (completed)
  ('GT55RB2K',   1, 14, 1, 2, 'promptpay',   80, 'boarded'), -- Panupong -> Assumption U.
  ('LM09WQ7C',   2,  4, 1, 1, 'card',        40, 'boarded'), -- Somchai  -> Assumption U.
  ('PN34KD8X',   5,  5, 1, 3, 'promptpay',  120, 'waiting'), -- Ananya   -> Assumption U.
  ('QR71ZC5V',   7,  6, 6, 2, 'card',        80, 'waiting'), -- Kevin    -> Market Village
  ('SD82YH3B',   7,  7, 5, 1, 'promptpay',   40, 'boarded'), -- Mei      -> Mega Bangna
  ('TF19MJ6N',   8,  8, 1, 4, 'card',       160, 'waiting'), -- Arthur   -> Assumption U.
  ('UH53PL0D',  10, 14, 3, 1, 'promptpay',   40, 'waiting'), -- Panupong -> Hua Mak
  ('VJ64QK9M',  13,  4, 1, 2, 'card',        80, 'boarded'), -- Somchai  -> Assumption U. (completed)
  ('WK25RN1P',  14,  5, 1, 1, 'promptpay',   40, 'waiting'), -- Ananya   -> Assumption U. (upcoming)
  ('XL36SP7Q',  15,  6, 4, 3, 'card',       120, 'waiting'); -- Kevin    -> Bangna Junction (upcoming)