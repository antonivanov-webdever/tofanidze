# Portfolio — Anton Tofanidze

Personal site and portfolio for a full-stack engineer working on B2B marketing and enterprise platforms.
Laravel 12, Blade, Tailwind CSS 4 and Alpine.js, with a database-backed admin panel so every piece of
content is editable without a deployment.

---

## What is in here

**Public site**

| Route | What it is |
| --- | --- |
| `/` | Hero, services, featured case studies, stack, experience, latest writing |
| `/projects`, `/projects/{slug}` | Case studies with client-side filtering by technology, plus challenge / solution / outcome pages |
| `/blog`, `/blog/{slug}` | Markdown articles with tag filtering |
| `/about` | Long-form background, skills with proficiency levels, full career timeline |
| `/resume` | Résumé on screen |
| `/resume/download` | The same résumé as a generated PDF |
| `/contact` | Contact form with validation, spam protection and email notification |
| `/sitemap.xml`, `/feed.xml`, `/robots.txt` | Generated from published content |

**Admin panel** — `/admin`

Case studies, articles, experience, services, skills, technologies, contact messages and site settings.
Everything in `config/site.php` (name, headline, intro copy, contact details, social links, SEO defaults)
can be overridden from **Settings** at runtime; the database wins, the config file is the fallback.

**Search & social**

Per-page meta and Open Graph tags, JSON-LD (`Person`, `CreativeWork`, `BlogPosting`), an XML sitemap, an
RSS feed, and a social preview card rendered to PNG by `php artisan site:og-image`.

---

## Requirements

- PHP 8.2+ with `gd` (for the social card) and `pdo_sqlite` or `pdo_mysql`
- Composer 2
- Node 20+ and npm

---

## Local setup

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed        # prints the generated admin password — save it
php artisan site:og-image

npm run build                     # or: npm run dev
php artisan serve
```

The site is at <http://localhost:8000>, the admin at <http://localhost:8000/admin>.

`php artisan migrate --seed` creates the admin account from `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env`,
falling back to the email in `config/site.php` and a generated password printed to the console.

To run the server, queue worker, log tail and Vite together:

```bash
composer run dev
```

### Using MySQL instead of SQLite

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portfolio
DB_USERNAME=portfolio
DB_PASSWORD=secret
```

---

## Docker

```bash
cp .env.example .env
php artisan key:generate          # or set APP_KEY in the environment

docker compose up -d --build
docker compose exec app php artisan db:seed
```

| Service | URL |
| --- | --- |
| Site | <http://localhost:8080> |
| Mailpit (catches all outgoing mail) | <http://localhost:8025> |
| MySQL | `127.0.0.1:3307` |

The image builds in three stages — npm for assets, Composer for dependencies, `php:8.3-fpm-alpine` for
the runtime. The entrypoint waits for MySQL, runs migrations, and caches config, routes and views when
`APP_ENV=production`.

---

## Production: VPS behind a CDN

`docker-compose.prod.yml` is a separate stack for a VPS deployment — MySQL runs as its own container
(no manual install or host setup needed), there's no Mailpit, and the whole site (not just `/build` and
images) is expected to sit behind a CDN edge.

```bash
cp .env.example .env.prod    # fill in real values — see the file's own comments
docker compose -f docker-compose.prod.yml --env-file .env.prod up -d --build
docker compose -f docker-compose.prod.yml --env-file .env.prod exec app php artisan db:seed --force
```

`DB_PASSWORD` and `DB_ROOT_PASSWORD` are required — the stack refuses to start without them rather than
falling back to the weak defaults the local dev compose file uses.

A few things that only matter in this topology, and are easy to miss:

**MySQL's data lives in the `mysql` named volume.** It survives `docker compose down` and rebuilds, but
`docker compose down -v` deletes it along with the database — never run that in production without a
backup in hand. The container's port is intentionally not published to the host or the internet; only
the `app` service can reach it, over the internal `web` network. Back it up on a schedule:

```bash
docker compose -f docker-compose.prod.yml --env-file .env.prod exec mysql \
  mysqldump -u root -p"$DB_ROOT_PASSWORD" portfolio > backup-$(date +%F).sql
```

**The CDN is the only thing that should ever reach this VPS.** With the whole site — not just static
files — proxied through the edge, `$request->ip()` in Laravel (used by the contact form's rate limiter
and stored on every `contact_messages` row) and the HTTPS detection both depend on forwarded headers
being trustworthy. `docker/nginx/prod.conf` handles this two ways:

1. `set_real_ip_from` (commented out, needs your CDN's actual IP ranges — pull them from your provider's
   own docs/dashboard, not a hardcoded copy here that can go stale) restricts which connections nginx
   will trust a forwarded-IP header from at all.
2. Once trusted, nginx **overwrites** `X-Forwarded-For` / `X-Forwarded-Proto` with its own resolved
   values before proxying to PHP — the app never sees a client- or CDN-supplied header verbatim.

Fill in the IP ranges, then firewall the VPS itself (`ufw`/security group) so ports 80/443 only accept
connections from those same ranges. The nginx allowlist alone doesn't stop someone from connecting to the
VPS directly and skipping the CDN — the firewall is what actually closes that door.

**TLS.** The compose file serves plain HTTP on `:80` and assumes the CDN terminates TLS at the edge
(e.g. Cloudflare's default "Flexible"/"Full" modes). If your CDN needs to re-encrypt to the origin
("Full (strict)"), add a `:443` server block with real certs to `docker/nginx/prod.conf`, mount them,
and publish 443 in the compose file (both are commented scaffolding already).

**`ASSET_URL` is not needed here.** It only matters when assets are served from a *different* domain
than the site (a dedicated CDN/object-storage hostname). With the CDN edge sitting in front of the same
domain, `/build/*` and image URLs stay same-origin — the CDN just caches them per the `Cache-Control`
headers `docker/nginx/prod.conf` already sets (1 year immutable for hashed Vite assets, 30 days for images).

**Cache-bust the social preview image after changing it.** `favicon.svg` and `images/og-default.png`
have stable filenames with no hash, cached 30 days. If you edit Settings and regenerate the card with
`php artisan site:og-image`, purge that path on the CDN (or it'll keep serving the old image for up to
a month).

**Back up `storage/app/public` too**, if you've placed files there by hand — there's no upload UI yet,
cover images are URLs or manually-placed paths. The database backup is covered above.

---

## Content model

| Model | Notes |
| --- | --- |
| `Project` | Case study. `metrics` and `highlights` are JSON; slug auto-generates from the title |
| `Post` | Markdown article. Publishing stamps `published_at`; scheduled posts stay hidden until due |
| `Experience` | Career timeline, with `highlights` and `stack` as JSON lists |
| `SkillCategory` / `Skill` | Grouped skills with a 1–100 proficiency level |
| `Technology` | Stack tags, grouped by category, attached to projects many-to-many |
| `Service` | The "What I build" cards on the home page |
| `ContactMessage` | Form submissions, with read/unread state |
| `Setting` | Key–value overrides for `config/site.php`, cached forever and flushed on save |

In the admin, JSON list fields are edited as plain text — one item per line. Metrics use
`value | label`, for example `600+ | active partner accounts`.

### Replacing the sample content

The seeders ship with realistic but **fictional** case studies, roles and clients so the site is not
empty on a fresh install. Replace them with your own work before publishing: edit them in the admin, or
rewrite `database/seeders/ProjectSeeder.php` and `ExperienceSeeder.php` and re-run
`php artisan migrate:fresh --seed`.

---

## Contact form protection

Four layers, none of which ask the visitor to solve a puzzle:

1. A honeypot field hidden off-screen — bots fill it, humans never see it.
2. A minimum time between rendering and submitting (3 seconds).
3. Rate limiting: 3 submissions per minute and 20 per day per IP.
4. Full server-side validation.

The message is stored **before** the notification is sent, so an SMTP outage logs an error instead of
losing the lead.

---

## Testing

```bash
php artisan test
```

32 feature tests cover the public pages, draft and scheduling visibility, the PDF résumé, sitemap and
feed, the contact form (including honeypot and mailer failure), admin authentication, every admin
screen, and the CRUD and settings flows.

---

## Useful commands

```bash
php artisan site:og-image        # regenerate the social preview card after changing settings
php artisan migrate:fresh --seed # rebuild the database with sample content
php artisan optimize             # cache config, routes and views for production
./vendor/bin/pint                # format PHP to Laravel's style
```

---

## Deployment checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` set to the real domain
- [ ] `APP_KEY` generated and kept out of version control
- [ ] Real SMTP credentials in `MAIL_*`, and the notification address set in **Settings**
- [ ] Replace the sample case studies and experience entries
- [ ] Change the seeded admin password
- [ ] `php artisan site:og-image` after the final settings edit
- [ ] `npm run build` and `php artisan optimize`
- [ ] HTTPS enforced (the app forces the `https` scheme when `APP_ENV=production`)
- [ ] Behind a CDN: `set_real_ip_from` filled in with your provider's ranges, VPS firewalled to those
      same ranges — see [Production: VPS behind a CDN](#production-vps-behind-a-cdn)
- [ ] Database backups scheduled (`mysqldump` + `storage/app/public`)
