# CI/CD for attendance-system

Two GitHub Actions workflows, the same pattern as `site-visit` and `tender-management`:

| Workflow | Runs on | When | What it does |
| --- | --- | --- | --- |
| `tests` (`.github/workflows/tests.yml`) | GitHub's Linux runners | Every push, every PR into `main` | `composer install`, `npm run build`, all migrations on a throwaway SQL Server 2022, then `php artisan test` (warning only, see below) |
| `deploy` (`.github/workflows/deploy.yml`) | Self-hosted runner on the attendance server | Manually: **Actions > deploy > Run workflow** | Refuses unless `tests` passed for that commit, then: maintenance mode on, fast-forward to `origin/main`, composer/npm only if needed, `migrate --force`, `optimize`, recycle app pool `attendance`, maintenance mode off |

The site is down for about a minute during a deploy, so kiosks show a "be right back" page. Deploy outside shift sign-in/sign-out times.

## Why the tests only warn

The test suite is the stock Laravel Breeze one. Two tests in `tests/Feature/Auth/RegistrationTest.php` expect a `/register` page the app deliberately removed, so they always fail (23 of 25 pass, on SQLite and on SQL Server alike). Once the app's developer deletes or updates that file, remove `continue-on-error: true` from the **Run tests** step so failing tests also block deploys.

Pint (code style) isn't checked because the existing code isn't Pint-formatted.

## One-time setup

### GitHub (repo Settings)

1. **Actions > Runners > New self-hosted runner** (Windows x64). Copy the `config.cmd` token for the server step below.
2. **Secrets and variables > Actions > Variables**: add `APP_PATH` = the app folder on the server, e.g. `C:\inetpub\attendance-system`.
3. **Environments**: create `production`. Optionally add yourself as a required reviewer, so each deploy waits for an approval click.
4. **Actions > General**: make sure Actions are allowed for this repo. Workflow permissions can stay "Read repository contents".

### Server (PowerShell as Administrator)

**a. Install the runner** in its own folder, separate from any other repo's runner:

```powershell
mkdir C:\actions-runner\attendance-system; cd C:\actions-runner\attendance-system
# download + extract the runner zip exactly as GitHub's "New self-hosted runner" page shows, then:
.\config.cmd --url https://github.com/Solo-GFInternational/attendance-system --token <TOKEN> `
  --name attendance-server --labels attendance-system --runasservice
```

The runner's Windows account needs to write the app folder and restart IIS app pools. If the site-visit or tender-management runners already run on this server, use the same account they use (`--windowslogonaccount`).

**b. Make the app folder a git clone.** Skip this if `C:\inetpub\attendance-system\.git` already exists. These commands keep every file in place:

```powershell
cd C:\inetpub\attendance-system
git init -b main
git remote add origin https://github.com/Solo-GFInternational/attendance-system.git
git fetch origin main
git reset origin/main                     # moves the branch pointer only, files untouched
git branch --set-upstream-to=origin/main
git status                                # see below
```

`git status` should show nothing modified. Untracked files like `public/web.config` are fine, and `.env`, `vendor`, `public/build` are ignored anyway. A **modified** tracked file means the server copy drifted from GitHub. Deal with it before the first deploy, or the fast-forward will refuse.

**c. Let the runner's account use that folder.** Otherwise git reports "dubious ownership":

```powershell
git config --system --add safe.directory C:/inetpub/attendance-system
```

**d. Give the runner's account a database login that can migrate.** The app pool login `IIS APPPOOL\attendance` only has read/write. In SSMS, replacing the account name:

```sql
USE [master];  CREATE LOGIN [<runner account>] FROM WINDOWS;          -- skip if it exists
USE [attendance_system];
CREATE USER [<runner account>] FOR LOGIN [<runner account>];
ALTER ROLE db_owner ADD MEMBER [<runner account>];
```

For `NT AUTHORITY\SYSTEM` or `NETWORK SERVICE`, use those names as they are.

**e. First run:** Actions > deploy > Run workflow. With no new commits it just re-caches and recycles the pool, which is a safe smoke test.

## Rolling back

Each deploy writes the commit it replaced to `storage\app\last-deploy-previous-commit.txt`:

```powershell
cd C:\inetpub\attendance-system
php artisan down
git reset --hard (Get-Content storage\app\last-deploy-previous-commit.txt)
composer install --optimize-autoloader --no-interaction
npm ci; npm run build
php artisan optimize:clear; php artisan optimize
Import-Module WebAdministration; Restart-WebAppPool attendance
php artisan up
```

This doesn't undo migrations. If the bad deploy added one, run `php artisan migrate:rollback` before the `git reset`, while the newer code is still there. The next normal deploy moves forward to `origin/main` again.
