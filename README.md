# Attendance System

A web app for staff attendance. Employees sign in and out at a kiosk using face recognition, and they record when they go out and return during the day. Visitors sign in and out too. Admins manage employees, sites and admin users, and they view attendance history, outings and an emergency list of who is on site.

Built with Laravel 12 on PHP 8.3.

## What it does

- **Kiosk (no login):** a page for each site, such as `/attendance/DG`. Employees are recognised by face and can then sign in, mark going out, mark returning and sign out.
- **Visitors:** sign in and sign out.
- **Admin (login required):**
  - Dashboard and attendance history, with PDF and Excel export
  - Outing history and the emergency list
  - Employee management, including Excel import and face registration
  - Sites and admin users
- **AI questions (optional):** admins can ask questions about attendance data. This uses a local [Ollama](https://ollama.com) server at `http://127.0.0.1:11434` with the `qwen2.5:3b` model. Everything else works without it.

There is no public registration page. Admin accounts are created by another admin.

### Sites

The kiosk link uses a site code. The codes and names are fixed in [routes/web.php](routes/web.php):

| Code | Site name |
|---|---|
| `DG` | Dry Goods HQ |
| `FG` | Frozen Goods |
| `FM` | Feedmill |

Each site must also exist on the admin **Sites** page with exactly that name and set to active. Otherwise, its kiosk link returns 404.

## Requirements

- PHP 8.3 with these extensions:
  - `pdo_sqlsrv` (for SQL Server) or `pdo_mysql` (for MySQL/MariaDB)
  - `gd` (for PDF export)
  - `zip` (for Excel)
  - `fileinfo`, `mbstring`, `openssl`
- [Composer](https://getcomposer.org) 2
- Node.js 20 or newer, with npm
- A database: Microsoft SQL Server (the target), or MySQL/MariaDB (the original)
- For SQL Server: [Microsoft ODBC Driver 17 or 18 for SQL Server](https://learn.microsoft.com/sql/connect/odbc/download-odbc-driver-for-sql-server)
- A browser with camera access for the kiosk. Browsers only allow the camera on `https://`, `localhost` or `127.0.0.1` addresses.

The face recognition models are already included in `public/models`.

## Setup

1. Clone the repository and switch to the branch you need. Use `sqlserver-fixes` for SQL Server.

2. Install the PHP and JavaScript packages:

   ```powershell
   composer install
   npm install
   npm run build
   ```

3. Create `.env` from the example and generate an app key:

   ```powershell
   copy .env.example .env
   php artisan key:generate
   ```

   If you are moving an existing installation, copy its `.env` instead and keep its `APP_KEY`. A new key logs everyone out and makes encrypted values unreadable.

4. Set the database in `.env`. For a SQL Server instance with Windows Authentication:

   ```ini
   DB_CONNECTION=sqlsrv
   DB_HOST=localhost\SQLDEV
   DB_PORT=
   DB_DATABASE=attendance_system
   DB_USERNAME=
   DB_PASSWORD=
   ```

   Leave `DB_USERNAME` and `DB_PASSWORD` blank for Windows Authentication. For SQL Server logins, fill them in. For MySQL/MariaDB, use `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1` and `DB_PORT=3306`.

   Also set `APP_URL` to the address people will use, and set `APP_NAME`.

5. Create the empty database (for example, `attendance_system`) on the server, then create the tables:

   ```powershell
   php artisan migrate
   ```

6. Link public file storage:

   ```powershell
   php artisan storage:link
   ```

7. Create the first admin user. Registration is turned off, so use Tinker:

   ```powershell
   php artisan tinker
   ```

   ```php
   $u = new App\Models\User(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'choose-a-strong-password', 'is_admin' => true]);
   $u->email_verified_at = now();
   $u->save();
   ```

8. Start the app:

   ```powershell
   php artisan serve --port=8020
   ```

   Then open `http://127.0.0.1:8020`. Log in, and add the sites and employees.

If several Laravel apps run on one computer, give each one its own port and its own `APP_URL`. Browsers share cookies between ports on the same host, so opening two apps on the same host (for example, both on `127.0.0.1`) can log you out of one of them. Use a different host for each app, such as `127.0.0.1` for one and `localhost` for another.

## SQL Server notes

The `sqlserver-fixes` branch contains three changes from the original MySQL version:

| Change | File | Why |
|---|---|---|
| Outings `employee_id` foreign key uses `NO ACTION` instead of cascade | `database/migrations/2026_09_08_123018_create_attendance_outings_table.php` | SQL Server rejects two cascade paths to the same table (error 1785). Deleting an employee still removes their outings through their attendance records. |
| Outing duration rounded to whole minutes before saving | `app/Http/Controllers/AttendanceController.php` | MySQL rounds automatically, but SQL Server does not. |
| Time columns created as `time(0)` | Attendances and outings migrations | Without this, SQL Server returns times with seven decimal places. |

Things to know:

- **New MySQL/MariaDB databases:** if you create a new MySQL/MariaDB database from this branch, deleting an employee who has outings fails (error 1451). Existing MySQL databases are not affected, because their tables were created by the original migrations.
- **Accent-sensitive search:** with the default `Latin1_General_CI_AS` collation, searching "Jose" does not find "José". To avoid this, create the database with an accent-insensitive collation such as `Latin1_General_CI_AI`.
- **Existing data:** `php artisan migrate` only creates empty tables. To move existing data from MySQL/MariaDB, copy each table in this order, keeping the original IDs:
  1. users
  2. sites
  3. employees
  4. attendances
  5. attendance_outings
  6. visitors

  Use `IDENTITY_INSERT` in SQL Server to keep the IDs. Copy the `storage` folder from the old installation as well.

## Updating an existing installation

1. Back up the database, the `storage` folder and `.env`.
2. Rename the old app folder instead of deleting it, so you can go back.
3. Put the new code in the same location.
4. Copy back `.env` and the `storage` folder.
5. Rebuild and clear the caches:

   ```powershell
   composer install --no-dev
   npm install
   npm run build
   php artisan migrate
   php artisan optimize:clear
   ```

## Tests

`phpunit.xml` does not switch tests to a separate database, so `php artisan test` runs against the database in `.env` and **erases it**. Only run the tests with a throwaway database. For example, enable the SQLite lines in `phpunit.xml` first:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

The two registration tests fail by design, because registration is turned off.

## Known issues

- **Employee detail page:** `GET /employees/{id}` returns a server error. The controller has no `show` method.
- **Deleting a site:** `DELETE /sites/{id}` returns a server error. The controller has no `destroy` method. Use the activate/deactivate toggle instead.
- **Duplicate site route:** [routes/web.php](routes/web.php) defines `/attendance/{site}` twice, and the second definition (with the site code map) is the one used.

## Troubleshooting

- **Certificate errors during `composer install` or `npm install`:** if `npm install` fails with `SELF_SIGNED_CERT_IN_CHAIN`, or Composer fails with an SSL error, a network security proxy is probably inspecting traffic. Point the tools at the Windows certificate store rather than turning off verification:
  - **npm:** set `$env:NODE_OPTIONS='--use-system-ca'` in PowerShell, then run `npm install`. This needs Node 22.15 or newer.
  - **Composer:** export the trusted root certificates from Windows to a `.pem` file, set `$env:SSL_CERT_FILE` to that file, then run `composer install`.
- **Camera does not start:** open the kiosk on `https://`, `localhost` or `127.0.0.1`, and allow camera access in the browser.
- **Settings changes have no effect:** run `php artisan optimize:clear`.
