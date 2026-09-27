# PC Poa – Computer Sales & Repair Management Website

A complete, database-driven PHP + MySQL website for **PC Poa**, following the
project documentation you provided (`pcpoa_doc.txt`). Content language: **English**.

## Tech Stack
HTML5, CSS3 (Bootstrap 5), JavaScript, PHP (PDO/MySQLi), MySQL, XAMPP.

## Setup (XAMPP / local)

1. Copy the whole `PCPOA` folder into `htdocs` (e.g. `C:\xampp\htdocs\PCPOA`).
2. Start Apache and MySQL in the XAMPP control panel.
3. Open `http://localhost/phpmyadmin`, create nothing manually — instead
   import the file `database/pcpoa_db.sql`. It creates the `pcpoa_db`
   database, all tables, and seed data (sample categories, products,
   services, and a default admin account).
4. Check `config/database.php` — default XAMPP credentials
   (`root` / no password) are already set. Update if yours differ.
5. Visit `http://localhost/PCPOA/reset_admin_password.php` **once** to
   correctly hash the default admin password for your PHP build.
   Login afterwards with:
   - Email: `admin@pcpoa.co.tz`
   - Password: `Admin@2026`
   Then **delete `reset_admin_password.php`** and change the password
   under Admin → Website Settings.
6. Visit `http://localhost/PCPOA/index.php` to see the live site.

## What's included

- **Public site**: Home, About Us, Services, Shop (search + category
  filter), Product Details, Delivery, Contact (with map + message form),
  Video Gallery (customers can watch videos uploaded by the admin).
- **Customer accounts**: Register, Login, Dashboard, Profile, Orders,
  Repair Requests. Shopping cart + checkout creates real orders.
- **Repair request system**: public form (works for guests and logged-in
  customers) feeding into the admin repair pipeline (Pending → Received →
  Diagnosing → Repairing → Ready for Collection → Completed / Cancelled).
- **Admin panel** (`/admin`): dashboard with live stats, Products CRUD
  (with image upload), Categories, Services CRUD, Customers (view/suspend),
  Orders (status + payment updates), Repair Requests (status + cost),
  Messages inbox, Video Gallery management (upload/hide/delete videos),
  and **Website Settings** — the **only** place the site logo, phone
  numbers, WhatsApp number, email, office address/location, Instagram
  (`pcpoa_official`) and TikTok (`pcpoa`) handles can be changed. Regular
  customers cannot access any `/admin` page.
- **Location tag**: the office address (Agrey Street, Kariakoo, near
  Mkombozi Bank, Dar es Salaam) is shown in the top bar and footer of
  every single page, and embedded as a Google Map on the Contact page.
- **Logo placement**: the PC Poa logo is pinned top-left of the navigation
  bar on every page (and in the admin panel), pulled live from the
  `settings` table so an admin update reflects everywhere instantly.

## Folder structure

See `database/pcpoa_db.sql` for the full schema. Uploaded images/videos
are stored under `uploads/` (`uploads/logo`, `uploads/products`,
`uploads/services`, `uploads/videos`) — make sure this folder is
writable by the web server (`chmod -R 755 uploads`).

## Security notes already implemented
- Passwords hashed with `password_hash()` / verified with `password_verify()`.
- All database queries use PDO prepared statements (no raw SQL concatenation).
- Admin pages are protected by `require_admin()`; customer pages by
  `require_login()`.
- Uploaded files are restricted to safe image/video extensions and
  renamed on upload.

## Suggested next steps (from the documentation's "Future Improvements")
Online payment gateways (M-Pesa, Tigo Pesa, Airtel Money), SMS/WhatsApp/
email notifications, customer reviews & ratings, PDF invoices, and a
mobile app — add these once the core site above is tested and deployed.

## Security Hardening (Phase 2 — applied on top of the original build)

The site's original design, layout, database schema, and every existing
feature are unchanged. The following defensive layers were added:

- **CSRF protection** on every form and every state-changing admin action
  (`includes/functions.php`: `csrf_token()`, `csrf_field()`, `csrf_verify()`).
  Admin "delete"/"toggle" links that used to be plain GET links are now
  CSRF-protected POST buttons — they look and behave identically.
- **Session hardening**: `HttpOnly` + `SameSite=Lax` cookies (+ `Secure`
  automatically once you're on HTTPS), and `session_regenerate_id()` on
  every login/registration to prevent session fixation.
- **Login rate limiting**: 5 failed attempts per 10 minutes, then a short
  cool-down, before more logins are accepted.
- **Real file-upload validation**: uploaded images/videos are checked by
  their actual content (`getimagesize()` / `finfo`), not just their file
  extension, with a size cap (5 MB images / 100 MB videos). Every
  `uploads/*` folder also gets an `.htaccess` that disables script
  execution, as defense in depth.
- **Safe error handling**: `config/database.php` now has an `APP_DEBUG`
  flag. Set it to `false` before going live and raw database errors are
  hidden from visitors (logged instead) — no more leaked file paths or
  SQL details.
- **Security headers** sent on every page (`includes/functions.php` →
  `send_security_headers()`): a Content-Security-Policy scoped to exactly
  the CDN hosts and Google Maps embed the site already uses, plus
  `X-Content-Type-Options`, `Referrer-Policy`, `X-Frame-Options`,
  `Permissions-Policy`, and HSTS (auto-enabled only once HTTPS is live).
- **Server-side input validation**: email/phone format checks on
  registration, contact, repair requests, profile updates and admin
  settings — not just the HTML5 `type="email"` attribute.
- **Business logic**: stock is re-verified at checkout (can't order more
  than what's in stock even if the cart is stale), and hidden/deactivated
  products can no longer be checked out from an old cart.
- **`reset_admin_password.php` is now locked**: it requires a `?token=`
  match and writes a one-time lock file after first use, so it can never
  be replayed by a stranger who finds the URL. **Still delete this file
  once you've logged in and changed your password** — the lock is a
  safety net, not a reason to leave it on the server.

### Recommended (not code-level — your hosting/server setup)

- **Least-privilege database user**: the sample SQL uses `root` for local
  XAMPP convenience. On a live server, create a dedicated MySQL user for
  `pcpoa_db` with only `SELECT, INSERT, UPDATE, DELETE` on that database
  (no `DROP`, `GRANT`, or access to other databases), and put those
  credentials in `config/database.php` instead of `root`.
- **Set `APP_DEBUG` to `false`** in `config/database.php` before deploying.
- **Set a real `SETUP_TOKEN`** in `reset_admin_password.php`, run it once,
  then delete the file.
- **Serve the site over HTTPS** — the Secure cookie flag and HSTS header
  activate automatically once you do, with no code changes needed.
- Keep PHP and MySQL updated on whatever host you deploy to.
