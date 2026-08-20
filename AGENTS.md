# AGENTS.md

## Cursor Cloud specific instructions

### What this project is
A single **Laravel 12 / PHP 8.3** application (not a monorepo): a Brazilian
(Portuguese-language) e-commerce storefront + multi-role CRM/admin portal for a
cosmetics brand ("K'ENZZA"). Frontend is Blade + Livewire + Alpine + Tailwind,
built with Vite. Default datastore is **SQLite** (`database/database.sqlite`);
queue/cache/session all use the `database` driver by default.

### Running the app (dev)
- Full dev stack (server + queue worker + log tailer + Vite) is the `dev`
  Composer script: `composer dev` (see `composer.json`). It uses
  `--kill-others`, so if any one process fails all are torn down.
- If you prefer running pieces separately: `php artisan serve --host=0.0.0.0 --port=8000`
  for the app (port **8000**) and `npm run dev` for Vite (port **5173**).
  Prebuilt assets from `npm run build` also work without the Vite dev server.
- Lint: `./vendor/bin/pint` (add `--test` for a dry run). Tests: `php artisan test`.

### Environment file (important, non-obvious)
- There is **no `.env.example` committed**, even though `composer setup` /
  `post-root-package-install` try to copy one. A working local `.env` (SQLite,
  `MAIL_MAILER=log`, `QUEUE_CONNECTION=database`) is kept in the VM snapshot.
- If `.env` is ever missing, recreate a standard Laravel `.env` with
  `DB_CONNECTION=sqlite` and `MAIL_MAILER=log`, then run:
  `php artisan key:generate`, `touch database/database.sqlite`,
  `php artisan migrate`. Dependencies (`composer install`, `npm install`) are
  refreshed automatically by the startup update script.

### Known PRE-EXISTING code defects (NOT environment problems)
These are bugs in the committed application code. Do not treat them as broken
setup, and do not "fix" them unless the task explicitly asks:
- **`database/migrations/2026_05_26_154745_create_audit_logs_table.php`** defines
  its method as `River()` instead of `up()`, so the `audit_logs` table is never
  created. `AuditObserver` is registered on `User`, `Product`, `Order`, and
  `Ticket` (`app/Providers/AppServiceProvider.php`), so any create/update/delete
  on those models throws `no such table: audit_logs`. This makes ~21 of 25
  `php artisan test` cases fail and breaks user registration, product/order/ticket
  writes in the running app.
- **`database/seeders/ProductSeeder.php`** inserts columns
  (`preco_black`, `preco_gold`, `preco_diamante`) that no longer exist; the real
  `products` price columns are `preco_distribuidor` / `preco_varejo`. So
  `php artisan db:seed` fails partway (the test user is created first, products are not).
- **`app/Notifications/WelcomeDistributor.php`** has a PHP parse error around
  line 52, which makes `pint` report an error and breaks the lead "promote" flow.

### Flows that work despite the above (useful for smoke tests)
- Public storefront pages, and the **lead capture** flow: `GET /seja-um-distribuidor`
  → `POST /enviar-lead` (`LeadController::store`) creates a `LeadDistribuidor`
  (not audited), returning a green success flash. Cart operations
  (`Cart`/`CartItem`) are also not audited.
