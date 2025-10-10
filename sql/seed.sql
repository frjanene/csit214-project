
INSERT INTO users (first_name,last_name,email,role) VALUES
('John','Smith','john@example.com','member'),
('Admin','User','admin@flydreamair.com','admin');

INSERT INTO airports (code, city, country) VALUES
('SIN','Singapore','Singapore'),
('SYD','Sydney','Australia'),
('MEL','Melbourne','Australia');

INSERT INTO lounges (airport_id, name, price_per_person, capacity, opens, closes) VALUES
(1, 'FlyDreamAir Premium Lounge', 85, 120, '05:00:00', '23:00:00'),
(2, 'FlyDreamAir Sydney Lounge', 55, 150, '04:30:00', '23:30:00'),
(3, 'FlyDreamAir Melbourne Lounge', 55, 140, '05:00:00', '22:30:00');
