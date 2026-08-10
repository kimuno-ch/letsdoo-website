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
| Custom fields    | [ACF](https://www.advancedcustomfields.com/) — **PRO** ([`just acf-pro`](#installing-updating-acf-pro)) |
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

1. **Install & activate ACF PRO.** The theme's field groups are registered in code
   (`inc/acf-fields.php`) but the plugin must be present for them to appear, and the theme
   uses PRO-only field types.
   ```bash
   $EDITOR .env       # set ACF_PRO_LICENSE=<your key>
   just acf-pro
   ```
   See [Installing / updating ACF PRO](#installing-updating-acf-pro). Without a licence key
   the free plugin (`just wp plugin install advanced-custom-fields --activate`) will get most
   of the site rendering, but any repeater field stays invisible in the admin.
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
just acf-pro       # install/update ACF PRO using ACF_PRO_LICENSE from .env
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
├── tools/                     # one-off migration scripts (run inside the container)
└── wp-content/
    └── themes/
        └── letsdoo/           # the only thing that actually ships
            ├── style.css      # theme header + reset/base
            ├── functions.php  # setup, asset enqueue, includes
            ├── header.php / footer.php
            ├── home.php / index.php / archive.php / single.php
            ├── single-referenz.php        # case-study single view
            ├── single-standort.php        # local SEO landing page (hero + blocks)
            ├── inc/
            │   ├── cpt.php                # custom post types
            │   ├── acf-fields.php         # ACF field groups (in code)
            │   ├── blocks.php             # ACF block registration + helpers
            │   ├── seo.php                # title, meta description, schema.org
            │   ├── settings-page.php      # "Firmenangaben" settings page
            │   └── template-helpers.php   # image fallbacks, buttons, queries
            ├── blocks/                    # one dir per block: block.json + render.php
            ├── page-templates/            # Startseite, Über uns, Angebote, Kontakt
            ├── template-parts/            # reusable partials
            └── assets/
                ├── css/                   # 01..25, one file per section (ordered!)
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
| `standort`      | Local SEO landing pages          | **yes** — root-level permalink (`/odoo-hochdorf/`), block editor enabled, unlinked and reachable only via the sitemap |

Ordered lists use `menu_order` (the Page Attributes "order" box).

### Blocks (`blocks/`, `inc/blocks.php`)

Blog posts and Standort pages compose their body from the theme's own ACF Blocks. The page
templates in `page-templates/` do **not** — their layout is fixed and the template is the
guardrail. Blocks exist where the running order genuinely varies per post.

**No build step.** A block is a `block.json` plus a PHP render template; ACF server-renders
the editor preview over AJAX. There is nothing to bundle or transpile.

| Block | Used for |
| ----- | -------- |
| `letsdoo/textabschnitt`  | Heading + WYSIWYG body — the local copy on a Standort page |
| `letsdoo/leistungen`     | Card grid pulled from the `leistung` post type |
| `letsdoo/referenz-karte` | One chosen Referenz as a card |
| `letsdoo/zahlen`         | Kennzahlen grid (repeater) |
| `letsdoo/faq`            | Frage/Antwort repeater — also feeds the `FAQPage` schema |
| `letsdoo/cta-band`       | Closing CTA, as a gradient card or a full-bleed band |

Adding one: create `blocks/<name>/block.json` + `render.php`, list `<name>` in
`letsdoo_blocks()`, and add a field group located on `block == letsdoo/<name>`.

> ⚠️ **Do not set `apiVersion` in `block.json`.** ACF derives it from its own block version
> and only ever pairs 3 with 3. Forcing `apiVersion: 3` while ACF's stays at its default of 2
> iframes the editor canvas under a block that doesn't expect it — **every edit screen for a
> post type using these blocks goes blank white**, with nothing in the PHP log because the
> failure is client-side. The front end keeps working, so it is easy to miss. Leave it out.

Three things every section block has to get right, all handled by
`letsdoo_block_section_open()` — use it rather than writing the `<section>` by hand:

- **The wave sequence.** `05-sections.css` keys the wave shapes and background blends off
  `section:nth-of-type(3n + k)` among siblings. Blocks must land as direct children of
  `<main>`, so `the_content()` is emitted with no wrapper. `nth-of-type` counts only
  `<section>`, so interleaved paragraphs and images are harmless — but a core **Group** block
  would restart the count, which is why Group and Columns are kept out of the Standort
  inserter (`letsdoo_blocks_allowed()`).
- **`alignfull`.** Inside a post body, `.entry-content > *` caps children at 780px. Every
  section block defaults to `align: full` to escape it; `assets/css/25-blocks.css` then tames
  the doubled vertical padding.
- **Anchors.** Only emitted when an editor sets one — `home.php` and `single.php` still
  hardcode `id="kontakt"`, so a default would produce duplicate IDs.

The `FAQPage` structured data in `inc/seo.php` is generated by reading the FAQ blocks back out
of `post_content` (`letsdoo_faq_block_items()`), because `wp_head` runs long before the blocks
render. **Changing the FAQ block's field names breaks the schema silently** — the page still
renders, it just stops emitting the markup it exists for.

### Custom fields (ACF PRO)

Field groups are registered **in code** in `inc/acf-fields.php` so they're version-controlled
rather than living only in the database.

The site runs **ACF PRO**, so repeaters, `have_rows()` / `the_row()`, Flexible Content, Clone
fields, Gallery fields, Options Pages and ACF Blocks are all available, and the Pro IDE stubs
in `composer.json` match the runtime.

#### Installing / updating ACF PRO

```bash
$EDITOR .env       # set ACF_PRO_LICENSE=<your key>
just acf-pro
```

`just acf-pro` verifies the key against ACF's download endpoint *before* touching the site,
writes `define( 'ACF_PRO_LICENSE', ... )` into `wp-config.php` so the licence self-activates,
removes the free plugin if it's installed, and installs and activates PRO. Re-run it any time
to pull a newer PRO release. Nothing is lost in the swap — field *groups* are registered in
code, field *values* are ordinary post meta.

Because the key is stored in `wp-config.php` inside the `wp_data` volume, `just nuke` clears
it; re-running `just acf-pro` puts it back.

#### Free-edition leftovers

The theme predates the PRO licence and several workarounds from that period are still in
place. They all work; none of them convert automatically. Listed roughly in order of value:

| Workaround today | Replace with |
| ---------------- | ------------ |
| `inc/settings-page.php` — 75 lines of Settings API | `acf_add_options_page()` |
| Duplicated hero/CTA field definitions across six field groups | Clone fields |
| `button_label` + `button_url` pairs everywhere | Link fields (the blocks already use them) |
| `letsdoo_merkmale_liste()` in `inc/template-helpers.php` — the Paket "Merkmale" textarea parsed on line breaks and a leading `-` | Repeater field |
| `warum_letsdoo_punkt_1/2/3_*` — nine flat fields, hardcoded to exactly three points | one Repeater |
| The fixed section order in every `page-templates/` file | Flexible Content, or extend the blocks to pages |
| Hardcoded CTA banners in `home.php` and `single.php` | the `letsdoo/cta-band` block |

⚠️ Most of these **change the meta key layout** — `warum_letsdoo_punkt_1_titel` becomes
`warum_letsdoo_punkte_0_titel`, and the Firmenangaben option is not where ACF looks for an
Options Page — so existing content needs a one-off migration or manual re-entry. Take a
`just backup-db` first, and see `tools/migrate-standorte-to-blocks.php` for the pattern.

Already converted: the Standort local copy, Referenz, FAQ and closing CTA are blocks
(`tools/migrate-standorte-to-blocks.php`, already run). Their old ACF fields were removed from
the field group but **the meta was deliberately left in the database**, so the pre-block
content is still recoverable.

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

`composer.json` + `composer.lock` are committed so stub versions stay pinned. The stubs are
ACF Pro and so is the runtime, so autocomplete matches what actually works.

---

## Environment & secrets

- Copy `.env.example` → `.env` and set real values. `.env` is **gitignored** — never commit
  credentials.
- `.env` drives DB credentials, table prefix, and the exposed ports (`WORDPRESS_PORT` 8080,
  `PHPMYADMIN_PORT` 8081).
- `ACF_PRO_LICENSE` is **required** — the theme uses PRO-only field types. Set it and run
  `just acf-pro`. It is empty in `.env.example`; each machine supplies its own copy of the key.

---

## Backups & data

Site content and the database live in Docker volumes (`wp_data`, `db_data`), not in git.

- `just backup-db` writes a timestamped SQL dump to `backups/` (gitignored).
- `just restore-db backups/<file>.sql` restores one.
- Media uploads live in the `wp_data` volume — back that up separately if you need it.
- `just nuke` deletes **all** volumes; use it to reset to a clean install.
```
