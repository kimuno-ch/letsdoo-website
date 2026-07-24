# Let's Doo Website

Marketing website for [letsdoo.com](https://letsdoo.com), built on **WordPress** with a custom
theme. The site is a German-language, content-managed brochure site (services, team,
partner references / case studies, offering packages, and a blog).

Only the custom theme lives in this repository. WordPress core, plugins, the database, and
uploaded media are **not** version-controlled — they live inside Docker volumes on each
machine. This keeps the repo small and makes the theme the single source of truth.

---

## Stack

| Layer            | Choice                                                              |
| ---------------- | ------------------------------------------------------------------ |
| CMS              | WordPress (`wordpress:latest` image)                               |
| Database         | MariaDB 11                                                          |
| PHP              | 8.1+ (per theme header)                                             |
| Custom fields    | [ACF](https://www.advancedcustomfields.com/) — **free** edition    |
| Local runtime    | Docker Compose (WordPress + MariaDB + phpMyAdmin)                   |
| Task runner      | [`just`](https://github.com/casey/just)                            |
| Dev tooling      | Composer (IDE/static-analysis stubs only — never loaded at runtime) |

There is **no** JS/CSS build step. CSS is authored by hand and split into ordered files;
JavaScript is a single vanilla script. Nothing is bundled, transpiled, or minified.

---

## Requirements

- [Docker](https://docs.docker.com/get-docker/) + Docker Compose
- [`just`](https://github.com/casey/just#installation) (optional but recommended — every
  workflow below has a `just` recipe)
- [Composer](https://getcomposer.org/) + PHP 8.1+ **only** if you want IDE autocomplete for
  WordPress/ACF APIs (see [Dev tooling](#dev-tooling))

---

## Quick start

```bash
# 1. Create your local env file and set real passwords
just init          # copies .env.example -> .env (or do it by hand)
$EDITOR .env       # set DB_ROOT_PASSWORD / DB_PASSWORD etc.

# 2. Start the stack (WordPress, MariaDB, phpMyAdmin) in the background
just up

# 3. Finish the WordPress install in the browser (first run only)
open http://localhost:8080     # follow the 5-minute WP installer
```

After the installer, log into `wp-admin` and do the one-time setup below.

| Service     | URL                     |
| ----------- | ----------------------- |
| Site        | http://localhost:8080   |
| WP admin    | http://localhost:8080/wp-admin |
| phpMyAdmin  | http://localhost:8081   |

Ports and credentials come from `.env` (see `.env.example`).

### One-time WordPress setup

Because plugins and content are not in the repo, a fresh install needs a little manual
wiring after the WP installer:

1. **Install & activate the ACF plugin** (free edition — "Advanced Custom Fields"). The
   theme's field groups are registered in code (`inc/acf-fields.php`) but the plugin must be
   present for them to appear. Install via **Plugins → Add New**, or:
   ```bash
   just wp plugin install advanced-custom-fields --activate
   ```
2. **Activate the Let's Doo theme** under **Appearance → Themes**.
3. **Create the pages** and assign the matching page templates (Startseite, Über uns,
   Angebote, Kontakt) under **Pages** → *Page Attributes → Template*. Set the Startseite
   page as the static front page under **Settings → Reading**.
4. **Fill company details** under **Settings → Firmenangaben** (address, email, phone,
   socials — used by the footer and Kontakt page).
5. **Add content** for the custom post types (Leistungen, Team, Referenzen, Pakete).

The theme ships placeholder images and default field values, so pages render sensibly even
before real content is added.

---

## Common tasks (`just`)

Run `just` with no argument to list every recipe. The most-used ones:

```bash
just up            # start the stack (detached)
just down          # stop the stack, keep all data
just restart       # restart containers
just ps            # container status
just logs          # follow all logs (just logs wordpress for one service)
just shell         # bash shell inside the wordpress container
just db-shell      # mysql shell inside the db container
just wp <args>     # run wp-cli, e.g. `just wp plugin list`
just backup-db     # dump the DB to backups/<timestamp>.sql
just restore-db <file>   # restore a DB dump
just nuke          # DESTROY all data (db + wp volumes) — asks for confirmation
```

Not using `just`? Every recipe is a thin wrapper over `docker compose ...` — read the
`justfile` for the exact commands.

---

## Repository layout

```
.
├── docker-compose.yml         # WordPress + MariaDB + phpMyAdmin
├── justfile                   # all dev/ops workflows
├── .env.example               # copy to .env, fill in credentials
├── composer.json / .lock      # dev-only IDE stubs (WordPress + ACF Pro)
└── wp-content/
    └── themes/
        └── letsdoo/           # the only thing that actually ships
            ├── style.css      # theme header + reset/base
            ├── functions.php  # setup, asset enqueue, includes
            ├── header.php / footer.php
            ├── home.php / index.php / archive.php / single.php
            ├── single-referenz.php        # case-study single view
            ├── inc/
            │   ├── cpt.php                # custom post types
            │   ├── acf-fields.php         # ACF field groups (in code)
            │   ├── settings-page.php      # "Firmenangaben" settings page
            │   └── template-helpers.php   # image fallbacks, buttons, queries
            ├── page-templates/            # Startseite, Über uns, Angebote, Kontakt
            ├── template-parts/            # reusable partials
            └── assets/
                ├── css/                   # 01..23, one file per section (ordered!)
                ├── js/navigation.js       # vanilla nav script
                ├── fonts/ · images/
```

`docker-compose.yml` mounts **only** `./wp-content/themes` into the container. WordPress
core, plugins, and uploads live in the `wp_data` Docker volume, not in the repo.

---

## How the theme is built

### Content model (custom post types)

Defined in `inc/cpt.php`. Most are admin-managed lists pulled into page templates via
`WP_Query` — they have no public URL:

| Post type       | Purpose                          | Public? |
| --------------- | -------------------------------- | ------- |
| `leistung`      | Services (Leistungen)            | no      |
| `team_mitglied` | Team members                     | no      |
| `referenz`      | Partner references / case studies| **yes** — has its own single view (`single-referenz.php`), block editor enabled |
| `angebot_paket` | Offering packages (Pakete)       | no      |

Ordered lists use `menu_order` (the Page Attributes "order" box).

### Custom fields (ACF free)

Field groups are registered **in code** in `inc/acf-fields.php` so they're version-controlled
rather than living only in the database.

> ⚠️ **This site runs ACF *free*, but the IDE stubs are ACF *Pro*** (no free-only stub package
> exists). The editor will autocomplete Pro-only APIs that **do not work here** — `have_rows()`,
> `the_row()`, and repeater fields. On free ACF, `get_field()` on a repeater returns the row
> count as a *string* (truthy) and silently renders an empty list. Use a **textarea +
> `letsdoo_lines()`** instead — see the Paket "Merkmale" field for the established pattern.

Company-wide settings (address, phone, socials) are **not** an ACF Options Page (Pro-only).
They live in `inc/settings-page.php` as a plain WordPress Settings API page under
**Settings → Firmenangaben**.

### Templates & helpers

- Page templates in `page-templates/` map to WordPress "Template" choices (Startseite,
  Über uns, Angebote, Kontakt).
- `inc/template-helpers.php` provides shared helpers: `letsdoo_image_url()` (resolves ACF
  image fields with placeholder fallbacks), `letsdoo_button()` (the gradient pill button),
  and ordered CPT queries.

### CSS — order matters

The stylesheet is split into one file per section under `assets/css/` (`01-base.css` …
`23-mobile.css`). CSS is cascade-sensitive: later rules win, and `23-mobile` overrides the
rest. `functions.php` enqueues each part with the **previous** part as its dependency, which
forces WordPress to emit them in exactly the numbered order.

**When adding a stylesheet:** create the numbered file, then add its slug to the
`letsdoo_style_parts()` array in `functions.php` in cascade order — and keep `23-mobile`
last.

Cache-busting uses the theme version from `style.css`; bump `Version:` there when shipping
CSS/JS changes.

---

## Dev tooling

`composer.json` pulls **dev-only** stubs (`php-stubs/wordpress-stubs`,
`php-stubs/acf-pro-stubs`) purely for IDE autocomplete and static analysis. They are **never
loaded at runtime** — `vendor/` is gitignored and only `wp-content/themes` is mounted into the
container, so `vendor/` never reaches PHP.

```bash
composer install     # restore the stubs locally (optional; IDE only)
```

`composer.json` + `composer.lock` are committed so stub versions stay pinned. See the ACF
caveat above — the stubs are Pro, the runtime is free.

---

## Environment & secrets

- Copy `.env.example` → `.env` and set real values. `.env` is **gitignored** — never commit
  credentials.
- `.env` drives DB credentials, table prefix, and the exposed ports (`WORDPRESS_PORT` 8080,
  `PHPMYADMIN_PORT` 8081).

---

## Backups & data

Site content and the database live in Docker volumes (`wp_data`, `db_data`), not in git.

- `just backup-db` writes a timestamped SQL dump to `backups/` (gitignored).
- `just restore-db backups/<file>.sql` restores one.
- Media uploads live in the `wp_data` volume — back that up separately if you need it.
- `just nuke` deletes **all** volumes; use it to reset to a clean install.
```
