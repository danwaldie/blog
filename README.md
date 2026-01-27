# Blog (Laravel 11 + Filament Admin + Inertia Public)

## Human Commentary
This project represents a learning exercise to explore modern Laravel and PHP capabilities. As a result, it is over-engineered and inefficient in some ways. I originally intended to actually use this as the base for a personal blog, but will probably stick to a static site since that will be leaner, cheaper and right-sized for the job.

I will go through a bit of my process and learnings in this **Human Commentary** section, and then below that is an AI-generated overview with some more technical details.

### Development Process
Since my background and experience is mainly in Swift, Python and Typescript, I used ChatGPT 5.2 to aid with CLI commands in getting a fresh Laravel project started up and scaffolded. 

To maximize learning, I stayed away from Claude Code/Cursor/Antigravity (with minor caveats I'll touch on later), however I also didn't want to waste a ton of time on boilerplate. As a compromise, I went over the scope of the project with ChatGPT and had it give me initial code for basic Posts and Tags functionality, which I hand-typed to copy over as a way to start building muscle memory for the syntax and conventions in Laravel/PHP.

In order to learn about some different patterns, I implemented some services and used observers in ways that were probably not strictly necessary. There's also some inconsistency within the codebase as I played around with different strategies.

The majority of actual dev time time was spent on the final addition of Comments to the blog project. For this feature, I used very little AI help at all - only if I was very stuck on something and unable to find a good example from the Posts/Tags code or the Laravel docs. To make things interesting, I included some extra touches to let me play around with different parts of the framework, such as dispatching a job to moderate comments after they are submitted. That triggers an action to call out to OpenAI's API to get a judgement on whether the comment should be published or rejected.

The implementations aren't fully rigorous (e.g. didn't get into retries on jobs), just explorations of elements of the framework.

An area I spent some extra time was customizing the Filament tables and forms, including using a Relations Manager to see comments under each Post in the admin and exploring different ways of manipulating that data.

Oh, I should mention that since my focus was on the backend, I _did_ use Antigravity (since I like Gemini's UI output best) to do the frontend. I also used it to generate tests - I want to dive deeper on creating tests manually but it just didn't seem like the absolute best use of my time at the moment.

### Takeaways
Overall, I feel good about this foray into Laravel. A lot of the things I've grown to really like about Swift such as static and strong typing are better approximated by modern PHP (if you opt into it) than I expected. It's still not close to the level of type-safety in Swift but it's decent. I appreciate how opinionated Laravel is in everything from namespacing to architecture, with a clear "best way" or at least only a couple of good ways of accomplishing most things (at least in this basic project).

It will take some time to get fully comfortable and fast with Laravel/PHP at a senior level. It would/will help a lot working in an established repo with existing code to read from and patterns to emulate. I learn very fast, so my confidence in being able to pick up and contribute quickly is still high.

## AI Intro and Overview
A small but production-minded personal blog built to ramp quickly on a modern Laravel backend stack (Laravel 11, PHP 8.4), with:
* Filament v3 for the admin CMS (Posts, Tags, and Comments)
* Inertia + React for the public-facing blog pages
* AI-Powered Comment Moderation via OpenAI (background jobs + structured output)
* Model events (Observers) for consistent domain invariants (slugs, rendering, excerpts, publish state)
* Strict typing (declare(strict_types=1);) across the app code
* Extensive Pest test suite covering public visibility rules, moderation logic, and key invariants

## Tech Stack
* PHP 8.4
* Laravel 11
* MySQL
* Filament v3 (admin panel)
* Inertia.js + React (public pages)
* OpenAI API (for comment moderation)
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

### Comment submission & Moderation

Comments undergo a multi-step lifecycle:
1. **Submission**: Guests submit comments via the blog post page.
2. **Initial State**: Comments are created with a `Submitted` status and are not visible publicly.
3. **Async Moderation**: A background job (`ModerateCommentJob`) is dispatched immediately.
4. **AI Processing**: The job calls OpenAI with structured output instructions to determine if the comment is spam/inappropriate.
5. **Final State**: Based on the AI result, the comment is marked as `Published` or `Rejected`.
6. **Visibility**: Only `Published` comments are visible on the public post page.

Administrators can manually override comment status and view moderation reasoning/errors via the Filament admin panel.

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
* (Optional) Redis (for background jobs)

### Install

composer install
npm install

### Configure environment

Copy .env.example to .env and set your MySQL and OpenAI credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog
DB_USERNAME=root
DB_PASSWORD=

# OpenAI Configuration
OPENAI_API_KEY=your-api-key
OPENAI_MODEL=gpt-4o-mini
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

(Optional) Start the queue worker for moderation:

`php artisan queue:work`

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

The suite includes over 50 tests covering:
* **Public visibility rules**: (draft/published/scheduled)
* **Comment system**: Submission, AI moderation mocking, and visibility logic.
* **Invariants**: Slug uniqueness and observer behaviors.
* **Admin**: Access control and resource management.
