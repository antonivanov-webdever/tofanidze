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

## Production: VPS behind a CDN, sharing a host with Xray/XHTTP

This deployment shares the VPS with an Xray (XHTTP) VPN endpoint sitting behind the same CDN — this site
is the "cover": to anyone who isn't the VPN client, or anyone who finds the origin's IP and connects
directly, it's just a working portfolio site. That means **two nginx instances**, not one:

```
client → CDN (terminates TLS) → edge nginx, bare-metal, :443
                                    ├─ VPN path  → 127.0.0.1:8080 (Xray)
                                    └─ everything else → 127.0.0.1:8081 (this stack's nginx)
```

- **`docker/nginx/edge.conf.example`** — the *outer* nginx. Installed via `apt install nginx` directly
  on the VPS (not in Docker — it needs to share the host with Xray/3x-ui, which aren't containerized in
  the usual setup). It owns `:80`/`:443`, terminates TLS with a Certbot cert, proxies the VPN path to
  Xray, and reverse-proxies everything else to this app. It's a template, not meant to be committed
  filled in — copy it to the server and fill in the real domain and secret path there, same reasoning as
  `.env.example`.
- **`docker-compose.prod.yml`** — the *inner* stack: app, MySQL (its own container, no manual install
  needed) and this repo's nginx, which now binds only to `127.0.0.1:8081` — reachable from the edge
  nginx on the same host, not from the internet.

```bash
cp .env.example .env.prod    # fill in real values — see the file's own comments
docker compose -f docker-compose.prod.yml --env-file .env.prod up -d --build
docker compose -f docker-compose.prod.yml --env-file .env.prod exec app php artisan db:seed --force
```

`DB_PASSWORD` and `DB_ROOT_PASSWORD` are required — the stack refuses to start without them rather than
falling back to the weak defaults the local dev compose file uses.

A few things that only matter in this topology, and are easy to miss:

**The real client IP is resolved once, at the edge — the inner nginx just trusts it.**
`edge.conf.example` uses `real_ip_module`, scoped to Yandex Cloud CDN's own published edge ranges (kept
current by `docker/nginx/refresh-yc-cdn-ips.sh`, see below), so `$remote_addr` there is the genuine
visitor IP rather than the CDN's. It forwards that on to the inner Docker nginx over loopback, which — because
its port is only reachable from that one trusted, same-host process — passes the header through as-is
rather than re-deriving it. This is what the contact form's per-IP rate limiter and the IP stored on
every `contact_messages` row rely on.

Before trusting this in production: **Yandex's docs don't explicitly confirm Cloud CDN forwards
`X-Forwarded-For`** (they do for some other products in the same platform). Verify it empirically —
temporarily log `$http_x_forwarded_for` for a request made through the CDN domain and confirm it's a
plausible external IP — before relying on it for anything security-relevant.

**Keep the IP allowlist current:**

```bash
sudo cp docker/nginx/refresh-yc-cdn-ips.sh /usr/local/bin/
sudo chmod +x /usr/local/bin/refresh-yc-cdn-ips.sh
sudo /usr/local/bin/refresh-yc-cdn-ips.sh          # run once before nginx's first start — edge.conf
                                                    # includes its output unconditionally
echo '17 4 * * * root /usr/local/bin/refresh-yc-cdn-ips.sh >> /var/log/yc-cdn-ips-refresh.log 2>&1' \
  | sudo tee /etc/cron.d/refresh-yc-cdn-ips
```

It pulls from Yandex's own published feed (`tech.cdn.yandex.net/prefixes/yc.json`) rather than a
hardcoded copy that would go stale, validates the result before writing it, and reloads nginx only if
`nginx -t` passes against the new list.

**Firewall the VPS itself** (`ufw`/security group) so `:80`/`:443` only accept connections from the same
CDN ranges. The allowlist above only decides which forwarded-IP headers nginx *trusts* — it doesn't stop
someone from connecting to the VPS's real IP directly and skipping the CDN entirely. For a VPN endpoint
you're specifically trying to keep off a censor's radar, that's the actual security boundary, not a
header-spoofing nuisance — the origin's IP is visible in DNS (the A record used to point the CDN at it),
so this is not a hypothetical.

**MySQL's data lives in the `mysql` named volume.** It survives `docker compose down` and rebuilds, but
`docker compose down -v` deletes it along with the database — never run that in production without a
backup in hand. The container's port is intentionally not published to the host or the internet; only
the `app` service can reach it, over the internal `web` network. Back it up on a schedule:

```bash
docker compose -f docker-compose.prod.yml --env-file .env.prod exec mysql \
  mysqldump -u root -p"$DB_ROOT_PASSWORD" portfolio > backup-$(date +%F).sql
```

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

**If you're not chaining this behind Xray/a VPN at all** — the simpler version of this setup (this stack's
nginx facing the CDN directly on `:80`/`:443`, no edge nginx, no MySQL-in-Docker requirement to skip) is
what an earlier version of this section covered; the compose file and `docker/nginx/prod.conf` would need
their port/TLS assumptions adjusted back if that's actually what you need instead.

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
      same ranges — see [Production: VPS behind a CDN, sharing a host with Xray/XHTTP](#production-vps-behind-a-cdn-sharing-a-host-with-xrayxhttp)
- [ ] Database backups scheduled (`mysqldump` + `storage/app/public`)
