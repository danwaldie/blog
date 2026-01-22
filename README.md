# Blog (Laravel 11 + Filament Admin + Inertia Public)

A small but production-minded personal blog built to ramp quickly on a modern Laravel backend stack (Laravel 11, PHP 8.4), with:
* Filament v3 for the admin CMS (Posts + Tags)
* Inertia + React for the public-facing blog pages
* Model events (Observers) for consistent domain invariants (slugs, rendering, excerpts, publish state)
* Strict typing (declare(strict_types=1);) across the app code
* Pest test suite covering public visibility rules, scheduling, and key invariants

## Tech Stack
* PHP 8.4
* Laravel 11
* MySQL
* Filament v3 (admin panel)
* Inertia.js + React (public pages)
* league/commonmark (Markdown → HTML)
* spatie/laravel-data (DTOs / request-data shaping)
* Pest (testing)

## Key Behaviors

### Post lifecycle & visibility

Posts have three statuses:
* draft — never publicly visible
* published — visible when published_at <= now()
* scheduled — becomes visible when published_at <= now()

This repo intentionally treats scheduling as a visibility rule (not a cron-driven state transition). Once the scheduled time passes, the post becomes publicly visible automatically.

### Observers

Observers are used for data consistency, not side-effect orchestration:
* PostObserver
  * generates a unique slug when blank
  * renders markdown to HTML when markdown changes
  * generates an excerpt (when excerpt is empty)
  * enforces published_at invariants:
  * set when status becomes Published
  * cleared when status becomes Draft
* TagObserver
  * generates a unique slug on create (Tag slugs are stable even if name changes)

Important: bulk updates like Post::query()->where(...)->update([...]) bypass model events.
This repo includes tests that demonstrate that behavior explicitly.

### Service container (DI)

Markdown rendering, excerpt generation, and slug generation are implemented behind interfaces and bound in a service provider, so the “magic” stays understandable and testable.

## Local Setup

### Requirements
* PHP 8.4
* Composer
* Node.js + npm
* MySQL (local, e.g., DBngin)
* (Optional) Redis

### Install

composer install
npm install

### Configure environment

Copy .env.example to .env and set your MySQL credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog
DB_USERNAME=root
DB_PASSWORD=
```
Generate key, run migrations, seed an admin user:
```
php artisan key:generate
php artisan migrate
php artisan db:seed
```
### Run the app

Start the frontend dev server:

`npm run dev`

Then open the app (Valet example):
* Public blog: https://blog.test/
* Admin panel: https://blog.test/admin

Admin credentials (local only)

Seeded via AdminUserSeeder:
* Email: admin@example.com
* Password: password

### Testing

Run the full test suite:

`composer test`

Feature tests cover:
* public visibility rules (draft/published/scheduled)
* scheduled visibility timing
* slug uniqueness
* observer invariants (published_at behavior)
* admin access control
