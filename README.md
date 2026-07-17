# Rhymio Musical Instrument Store

Rhymio is PHPStorm's CCS0043 final project: a responsive online musical instrument store with separate buyer and seller experiences.

Live website: https://ryhmio.infinityfree.me/

## Group Members

- Marco Arsenio B. De Leon
- Mohammad Al Sharip A. Sakaluran
- Tracey Justin A. Devilleres

## Buyer Features

- Buyer registration with complete name, valid email, password confirmation, structured Philippine address, four-digit postal code, and 11-digit contact number
- Email confirmation through PHPMailer and Gmail SMTP
- Categorized store with 14 products and unique product images
- Login-protected cart, checkout, and payment pages
- About page with company and member information

## Seller Features

- Protected system administrator dashboard
- Add and modify administrator accounts
- Add categories and products; modify prices, stock, images, and availability
- Remaining-inventory report
- Audit-log report showing the user, activity, details, and date

## Technology

- Procedural PHP
- MySQLi and MySQL/MariaDB
- HTML, CSS, JavaScript, and Bootstrap 5
- PHPMailer
- No PHP framework

## XAMPP Setup

1. Place the project folder in XAMPP's `htdocs` directory.
2. Import `rhymio_music_xampp.sql` in phpMyAdmin.
3. Copy `db_config.example.php` to `db_config.php` and enter the local database values.
4. Copy `mail_config.example.php` to `mail_config.php` and enter the Gmail address and 16-character app password.
5. Update `SITE_URL` in `mail_config.php` to the local project URL.
6. Open the project through `http://localhost/`.

## InfinityFree Setup

1. Upload the website files directly into the domain's `htdocs` directory.
2. Import `rhymio_music_infinityfree.sql` into the database assigned by InfinityFree.
3. Create `db_config.php` using the database host, username, password, and database name from the control panel.
4. Create `mail_config.php` using the Gmail SMTP settings and the hosted Rhymio URL.
5. Do not commit either active configuration file to a public repository.

## Test Accounts

See `sample_accounts.txt`.

## Submission Files

- `rhymio_music_xampp.sql` and `rhymio_music_infinityfree.sql`: database files
- `Rhymio_Output_Screenshots.pdf`: output screenshots with descriptions
- `sample_accounts.txt`: test accounts

Every user-facing page displays the Rhymio logo, PHPStorm group name, and the required educational-purpose disclaimer.
