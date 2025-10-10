
# FlyDreamAir — Scaffold v3 (PHP + MySQL + Bootstrap)

**Purpose:** Clean, best-practice scaffold matching your page map. Pages are accessible but intentionally empty.

## Pages (routes)
- Welcome (no header): `?r=welcome`
- Auth (Sign in / Sign up tabs, no header): `?r=auth`
- Dashboard (with header): `?r=dashboard`
- Find Lounges (with header): `?r=find`
- My Bookings (with header): `?r=bookings`
- Memberships (with header): `?r=memberships`
- Profile (with header): `?r=profile`

> Booking happens via **modals** later; there is intentionally **no standalone booking page**.

## Run
1) Create DB `flydreamair` in phpMyAdmin.
2) Import `sql/schema.sql` then `sql/seed.sql`.
3) Copy `.env.example` to `.env` and set credentials.
4) Serve `public/`:
   - `php -S localhost:8000 -t public`
   - or configure Apache vhost to `public/`

Open: `http://localhost:8000` (defaults to Welcome).
