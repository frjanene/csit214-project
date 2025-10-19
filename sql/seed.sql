USE flydreamair;

-- =============== MEMBERSHIP PLANS ===============
INSERT INTO membership_plans (slug, name, monthly_fee_usd, guest_allowance, normal_access, premium_access, benefits_json)
VALUES
 ('basic',     'Basic (Free)',  0.00, 0, 'pay_per_use', 'pay_per_use', JSON_ARRAY('Free membership signup','Buy single-visit pass for all lounges, including premium lounges','Wi-Fi access')),
 ('silver',    'Silver',     299.00, 1, 'free',        'pay_per_use', JSON_ARRAY('Free access to normal lounges','Pay-per-use for premium lounges','Wi-Fi & printing','Light refreshments')),
 ('gold',      'Gold',       499.00, 2, 'free',        'free',        JSON_ARRAY('Free access to all lounges, incl. premium','Unlimited time','Premium amenities','Full dining')),
 ('platinum',  'Platinum',   699.00, 3, 'free',        'free',        JSON_ARRAY('Free access to all lounges, incl. premium','Unlimited time','Concierge service','Private meeting rooms'));

-- =============== USERS ===============
-- bcrypt for 'password123' (example) -> replace later as needed
-- $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi is a common bcrypt hash of 'password'
INSERT INTO users (first_name, last_name, email, password_hash, phone, city, country, role)
VALUES ('John','Smith','john.smith@email.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        '+1 (555) 123-4567','New York','United States','member');

-- Link John to Basic plan
INSERT INTO user_memberships (user_id, plan_id, status)
SELECT u.id, p.id, 'active'
FROM users u CROSS JOIN membership_plans p
WHERE u.email='john.smith@email.com' AND p.slug='basic';

-- =============== AIRPORTS ===============
INSERT INTO airports (iata, name, city, country) VALUES
 ('SIN','Singapore Changi Airport','Singapore','Singapore'),
 ('SYD','Sydney Kingsford Smith Airport','Sydney','Australia'),
 ('MEL','Melbourne Airport','Melbourne','Australia'),
 ('DXB','Dubai International Airport','Dubai','United Arab Emirates'),
 ('CDG','Charles de Gaulle Airport','Paris','France');

-- =============== LOUNGES ===============
-- Prices are set to $55 per person (per UI mocks); capacities reflect the cards.
INSERT INTO lounges (name, airport_id, terminal, is_premium, address, city, country, open_time, close_time, capacity, price_usd, image_url)
SELECT 'FlyDreamAir Premium Lounge', a.id, 'Terminal 1', 1, NULL,'Singapore','Singapore','05:00','23:00',120,55.00,'assets/img/lounge-1.jpg'
FROM airports a WHERE a.iata='SIN';

INSERT INTO lounges (name, airport_id, terminal, is_premium, address, city, country, open_time, close_time, capacity, price_usd, image_url)
SELECT 'FlyDreamAir Sydney Lounge', a.id, 'Terminal 1', 0, NULL,'Sydney','Australia','04:30','23:30',150,55.00,'assets/img/lounge-2.jpg'
FROM airports a WHERE a.iata='SYD';

INSERT INTO lounges (name, airport_id, terminal, is_premium, address, city, country, open_time, close_time, capacity, price_usd, image_url)
SELECT 'FlyDreamAir Melbourne Lounge', a.id, 'Terminal 2', 0, NULL,'Melbourne','Australia','05:00','22:30',140,55.00,'assets/img/lounge-3.jpg'
FROM airports a WHERE a.iata='MEL';

-- These two exist to match examples in the "My Bookings" UI
INSERT INTO lounges (name, airport_id, terminal, is_premium, address, city, country, open_time, close_time, capacity, price_usd, image_url)
SELECT 'FlyDreamAir Dubai Lounge', a.id, 'Terminal 3', 1, NULL,'Dubai','United Arab Emirates','05:00','23:00',140,55.00,'assets/img/lounge-3.jpg'
FROM airports a WHERE a.iata='DXB';

INSERT INTO lounges (name, airport_id, terminal, is_premium, address, city, country, open_time, close_time, capacity, price_usd, image_url)
SELECT 'FlyDreamAir Paris Lounge', a.id, 'Terminal 2', 0, NULL,'Paris','France','05:00','22:00',120,55.00,'assets/img/lounge-2.jpg'
FROM airports a WHERE a.iata='CDG';

-- =============== AMENITIES ===============
INSERT INTO amenities (code,label) VALUES
 ('WIFI','Wi-Fi'),
 ('SHOWERS','Showers'),
 ('DINING','Premium Dining'),
 ('BAR','Bar'),
 ('BUSINESS_CENTER','Business Center'),
 ('COFFEE_BAR','Coffee Bar'),
 ('CHAMPAGNE_BAR','Champagne Bar');

-- Map amenities to lounges (based on the cards)
-- SIN (Premium): WIFI, SHOWERS, DINING, CHAMPAGNE_BAR
INSERT INTO lounge_amenities (lounge_id, amenity_id)
SELECT l.id, a.id FROM lounges l JOIN amenities a
  ON (l.name='FlyDreamAir Premium Lounge' AND l.city='Singapore' AND a.code IN ('WIFI','SHOWERS','DINING','CHAMPAGNE_BAR'));

-- SYD: WIFI, BAR, BUSINESS_CENTER
INSERT INTO lounge_amenities (lounge_id, amenity_id)
SELECT l.id, a.id FROM lounges l JOIN amenities a
  ON (l.name='FlyDreamAir Sydney Lounge' AND a.code IN ('WIFI','BAR','BUSINESS_CENTER'));

-- MEL: WIFI, COFFEE_BAR, BUSINESS_CENTER
INSERT INTO lounge_amenities (lounge_id, amenity_id)
SELECT l.id, a.id FROM lounges l JOIN amenities a
  ON (l.name='FlyDreamAir Melbourne Lounge' AND a.code IN ('WIFI','COFFEE_BAR','BUSINESS_CENTER'));

-- DXB (demo): WIFI, SHOWERS
INSERT INTO lounge_amenities (lounge_id, amenity_id)
SELECT l.id, a.id FROM lounges l JOIN amenities a
  ON (l.name='FlyDreamAir Dubai Lounge' AND a.code IN ('WIFI','SHOWERS'));

-- CDG (demo): WIFI, DINING
INSERT INTO lounge_amenities (lounge_id, amenity_id)
SELECT l.id, a.id FROM lounges l JOIN amenities a
  ON (l.name='FlyDreamAir Paris Lounge' AND a.code IN ('WIFI','DINING'));

-- =============== DEMO BOOKINGS (match UI examples) ===============
-- Helper: fetch IDs
SET @u_john := (SELECT id FROM users WHERE email='john.smith@email.com');

SET @l_sin := (SELECT id FROM lounges WHERE name='FlyDreamAir Premium Lounge'   AND city='Singapore');
SET @l_syd := (SELECT id FROM lounges WHERE name='FlyDreamAir Sydney Lounge'    AND city='Sydney');
SET @l_mel := (SELECT id FROM lounges WHERE name='FlyDreamAir Melbourne Lounge' AND city='Melbourne');
SET @l_dxb := (SELECT id FROM lounges WHERE name='FlyDreamAir Dubai Lounge'     AND city='Dubai');
SET @l_cdg := (SELECT id FROM lounges WHERE name='FlyDreamAir Paris Lounge'     AND city='Paris');

-- Upcoming (Confirmed) — SIN: Tue, Dec 15, 2026, 14:00–18:00, 2 people
INSERT INTO bookings
(user_id, guest_name, guest_email, lounge_id, flight_number, visit_date, start_time, end_time, people_count, method, unit_price_usd, total_usd, status, qr_code)
VALUES
(@u_john, NULL, NULL, @l_sin, 'FD456', '2026-12-15', '14:00:00','18:00:00', 2, 'membership', NULL, 0.00, 'confirmed', 'QR-SIN-20261215-1400-JS');

-- Upcoming (Confirmed) — SYD: Sun, Dec 20, 2026, 09:00–13:00, 3 people
INSERT INTO bookings
(user_id, guest_name, guest_email, lounge_id, flight_number, visit_date, start_time, end_time, people_count, method, unit_price_usd, total_usd, status, qr_code)
VALUES
(@u_john, NULL, NULL, @l_syd, 'FD123', '2026-12-20', '09:00:00','13:00:00', 3, 'membership', NULL, 0.00, 'confirmed', 'QR-SYD-20261220-0900-JS');

-- Past (Completed) — MEL: Mon, Nov 10, 2026, 08:00–10:00, 1 person
INSERT INTO bookings
(user_id, lounge_id, visit_date, start_time, end_time, people_count, method, unit_price_usd, total_usd, status, qr_code)
VALUES
(@u_john, @l_mel, '2026-11-10', '08:00:00','10:00:00', 1, 'pay_per_use', 55.00, 55.00, 'completed', 'QR-MEL-20261110-0800-JS');

-- Past (Cancelled) — CDG: Fri, Oct 30, 2026, 12:00–14:00, 2 people
INSERT INTO bookings
(user_id, lounge_id, visit_date, start_time, end_time, people_count, method, unit_price_usd, total_usd, status, qr_code)
VALUES
(@u_john, @l_cdg, '2026-10-30', '12:00:00','14:00:00', 2, 'pay_per_use', 55.00, 0.00, 'cancelled', 'QR-CDG-20261030-1200-JS');

-- Example Guest Checkout (no registration): DXB, Fri, Dec 25, 2026, 11:00–13:00, 1 person
INSERT INTO bookings
(user_id, guest_name, guest_email, lounge_id, visit_date, start_time, end_time, people_count, method, unit_price_usd, total_usd, status, qr_code)
VALUES
(NULL, 'Guest Traveler', 'guest@example.com', @l_dxb, '2026-12-25', '11:00:00','13:00:00', 1, 'pay_per_use', 55.00, 55.00, 'cancelled', 'QR-DXB-20261225-1100-GUEST');

-- =============== DEMO PAYMENTS ===============
-- Free via membership (amount 0 but status paid to simplify UI)
INSERT INTO booking_payments (booking_id, provider, provider_ref, amount_usd, currency, status, paid_at)
SELECT b.id, 'demo', 'FREE-MEMBERSHIP', 0.00, 'USD', 'paid', NOW()
FROM bookings b WHERE b.qr_code IN ('QR-SIN-20261215-1400-JS','QR-SYD-20261220-0900-JS');

-- Pay-per-use completed (MEL)
INSERT INTO booking_payments (booking_id, provider, provider_ref, amount_usd, currency, status, paid_at)
SELECT b.id, 'demo', 'PPU-MEL-1', 55.00, 'USD', 'paid', NOW()
FROM bookings b WHERE b.qr_code = 'QR-MEL-20261110-0800-JS';

-- Cancelled booking — no payment or a failed one (CDG example)
INSERT INTO booking_payments (booking_id, provider, provider_ref, amount_usd, currency, status)
SELECT b.id, 'demo', 'PPU-CDG-FAIL', 55.00, 'USD', 'failed'
FROM bookings b WHERE b.qr_code = 'QR-CDG-20261030-1200-JS';
