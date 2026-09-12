-- ============================================================
--  AU Van Seat Reservation — seed data
--  CE4221 · Assumption University
--
--  Run AFTER schema.sql:  mysql -u root -h 127.0.0.1 -P 3306 au_van < seed.sql
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
-- trips
--   Today (2026-05-10) schedule, per route:
--     Route 1  Bangna -> Assumption U.   4 departures  (08, 09, 10, 11)
--     Route 2  Assumption U. -> Hua Mak  8 departures  (08 .. 16:30)
--     Route 3  Assumption U. -> Bangna   4 departures  (12, 13:30, 15, 16:30)
--     Route 4  Hua Mak -> Assumption U.  8 departures  (08 .. 16:30)
--   4 today trips have no driver (the admin "needs a driver" alert).
--   Patchara (#3) drives trips 13 & 14.  #25-#26 past, #27-#28 future.
--   Driver + van assignments avoid same-time conflicts.
-- ------------------------------------------------------------
INSERT INTO trips (id, route_id, van_id, driver_id, trip_date, depart_time) VALUES
  -- Route 1  Bangna -> Assumption U.
  ( 1, 1,  1,  9,   '2026-05-10', '08:00:00'),
  ( 2, 1,  1,  9,   '2026-05-10', '09:00:00'),
  ( 3, 1,  1, 11,   '2026-05-10', '10:00:00'),
  ( 4, 1,  1, NULL, '2026-05-10', '11:00:00'),   -- needs a driver
  -- Route 2  Assumption U. -> Hua Mak
  ( 5, 2,  2, 10,   '2026-05-10', '08:00:00'),
  ( 6, 2,  2, 10,   '2026-05-10', '09:00:00'),
  ( 7, 2,  2, 12,   '2026-05-10', '10:00:00'),
  ( 8, 2,  2, 12,   '2026-05-10', '11:00:00'),
  ( 9, 2,  4,  9,   '2026-05-10', '12:00:00'),
  (10, 2,  4, 11,   '2026-05-10', '13:30:00'),
  (11, 2,  4, 13,   '2026-05-10', '15:00:00'),
  (12, 2,  4, 10,   '2026-05-10', '16:30:00'),
  -- Route 3  Assumption U. -> Bangna
  (13, 3,  1,  3,   '2026-05-10', '12:00:00'),   -- Patchara; manifest screen
  (14, 3,  1,  3,   '2026-05-10', '13:30:00'),   -- Patchara
  (15, 3,  1, NULL, '2026-05-10', '15:00:00'),   -- needs a driver
  (16, 3,  1, 11,   '2026-05-10', '16:30:00'),
  -- Route 4  Hua Mak -> Assumption U.
  (17, 4,  3, 11,   '2026-05-10', '08:00:00'),
  (18, 4,  3, NULL, '2026-05-10', '09:00:00'),   -- needs a driver
  (19, 4,  3, 13,   '2026-05-10', '10:00:00'),
  (20, 4,  3, 13,   '2026-05-10', '11:00:00'),
  (21, 4,  5, 10,   '2026-05-10', '12:00:00'),
  (22, 4,  5, 12,   '2026-05-10', '13:30:00'),
  (23, 4,  5,  9,   '2026-05-10', '15:00:00'),
  (24, 4,  5, NULL, '2026-05-10', '16:30:00'),   -- needs a driver
  -- past (feed the Completed booking history)
  (25, 3,  2, 10,   '2026-04-28', '12:00:00'),
  (26, 2,  1,  9,   '2026-05-02', '11:00:00'),
  -- future (upcoming beyond today)
  (27, 4,  6, 12,   '2026-05-11', '09:00:00'),
  (28, 3, 10, 13,   '2026-05-12', '12:00:00');

-- ------------------------------------------------------------
-- bookings
-- ------------------------------------------------------------
INSERT INTO bookings
  (reference, trip_id, user_id, dropoff_stop_id, seats, payment_method, total, board_status) VALUES
  ('F134WD24A', 13,  1, 4, 3, 'promptpay', 120, 'boarded'),  -- Jane     -> Bangna Junction
  ('JW77XK2Q',  13,  2, 5, 1, 'card',       40, 'waiting'),  -- Watson   -> Mega Bangna
  ('HQE34EF2',   5,  1, 3, 2, 'card',        80, 'waiting'), -- Jane     -> Hua Mak (upcoming)
  ('A72KD91C',  26,  1, 3, 1, 'promptpay',   40, 'boarded'), -- Jane     -> Hua Mak (completed)
  ('B65QP04E',  25,  1, 2, 4, 'card',       160, 'boarded'), -- Jane     -> Bangna (completed)
  ('GT55RB2K',   1, 14, 1, 2, 'promptpay',   80, 'boarded'), -- Panupong -> Assumption U.
  ('LM09WQ7C',   2,  4, 1, 1, 'card',        40, 'boarded'), -- Somchai  -> Assumption U.
  ('PN34KD8X',   3,  5, 1, 3, 'promptpay',  120, 'waiting'), -- Ananya   -> Assumption U.
  ('QR71ZC5V',  14,  6, 6, 2, 'card',        80, 'waiting'), -- Kevin    -> Market Village
  ('SD82YH3B',  14,  7, 5, 1, 'promptpay',   40, 'boarded'), -- Mei      -> Mega Bangna
  ('TF19MJ6N',  17,  8, 1, 4, 'card',       160, 'waiting'), -- Arthur   -> Assumption U.
  ('UH53PL0D',   5, 14, 3, 1, 'promptpay',   40, 'waiting'), -- Panupong -> Hua Mak
  ('VJ64QK9M',  25,  4, 2, 2, 'card',        80, 'boarded'), -- Somchai  -> Bangna (completed)
  ('WK25RN1P',  27,  5, 1, 1, 'promptpay',   40, 'waiting'), -- Ananya   -> Assumption U. (upcoming)
  ('XL36SP7Q',  28,  6, 4, 3, 'card',       120, 'waiting'); -- Kevin    -> Bangna Junction (upcoming)