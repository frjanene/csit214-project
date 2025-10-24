
# FlyDreamAir (PHP + MySQL + Bootstrap)


## Run
### Option 1
1) Create DB `flydreamair` in phpMyAdmin.
2) Import `sql\flydreamair.sql`.
3) Copy `.env.example` to `.env` and set credentials.
4) Serve `public\`:
   - `php -S localhost:8000 -t public`
   - or configure Apache vhost to `public\`

Open: `http://localhost:8000` (defaults to Welcome).

### Option 2
1) Download `XAMPP`.
2) Launch `XAMPP Control Panel`.
3) Start `Apache` and `MySQL` modules.
4) Go to `http://localhost/phpmyadmin`.
5) Create `New` database in phpMyAdmin.
6) Import `sql\flydreamair.sql`.
7) Navigate to XAMPP install folder:
   - Copy the entire flydreamair project folder to `XAMPP\htdocs\`.
8) In the flydreamair project folder, copy `.env.example` to `.env` and set credentials.

Open: `http://localhost/flydreamair/public` (defaults to Welcome).
