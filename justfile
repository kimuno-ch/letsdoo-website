set dotenv-load := true

# List available recipes
default:
    @just --list

# First-time setup: create .env from the example if it doesn't exist yet
init:
    #!/usr/bin/env bash
    set -euo pipefail
    if [ -f .env ]; then
        echo ".env already exists, skipping"
    else
        cp .env.example .env
        echo "Created .env - edit it and set real passwords before going further"
    fi

# Start the stack in the background
up:
    docker compose up -d

# Stop the stack (keeps volumes/data)
down:
    docker compose down

# Restart the stack
restart:
    docker compose restart

# Show container status
ps:
    docker compose ps

# Follow logs (optionally for a single service: just logs wordpress)
logs service="":
    docker compose logs -f {{service}}

# Open a shell in the wordpress container
shell:
    docker compose exec wordpress bash

# Open a mysql shell in the db container
db-shell:
    docker compose exec db mariadb -u root -p"${DB_ROOT_PASSWORD}" "${DB_NAME}"

# Run a wp-cli command against the running site, e.g. `just wp plugin list`
wp *args:
    docker run --rm \
        --network letsdoo-website_wordpress_net \
        --volumes-from letsdoo_wordpress \
        --user "$(id -u):$(id -g)" \
        -e WORDPRESS_DB_HOST=db:3306 \
        -e WORDPRESS_DB_NAME="${DB_NAME}" \
        -e WORDPRESS_DB_USER="${DB_USER}" \
        -e WORDPRESS_DB_PASSWORD="${DB_PASSWORD}" \
        wordpress:cli {{args}}

# Run a wp-cli command as www-data (uid 33), for anything that needs to write
# wp-config.php or wp-content/plugins — `just wp` runs as your own user, which
# can read those in the wp_data volume but not write them (see the wp() helper
# in acf-pro below, same fix).
wp-root *args:
    docker run --rm \
        --network letsdoo-website_wordpress_net \
        --volumes-from letsdoo_wordpress \
        --user 33:33 \
        -e HOME=/tmp \
        -e WORDPRESS_DB_HOST=db:3306 \
        -e WORDPRESS_DB_NAME="${DB_NAME}" \
        -e WORDPRESS_DB_USER="${DB_USER}" \
        -e WORDPRESS_DB_PASSWORD="${DB_PASSWORD}" \
        wordpress:cli {{args}}

# Swap the free ACF plugin for ACF PRO using ACF_PRO_LICENSE from .env
acf-pro:
    #!/usr/bin/env bash
    set -euo pipefail

    # Safe to re-run — that is also how you pull a newer PRO release.

    key="${ACF_PRO_LICENSE:-}"
    if [ -z "$key" ]; then
        echo "ACF_PRO_LICENSE is empty." >&2
        echo "Add your key to .env (advancedcustomfields.com -> My Account -> Licenses)," >&2
        echo "then run 'just acf-pro' again." >&2
        exit 1
    fi

    # ACF distributes PRO through its Composer repository. The old
    # index.php?p=pro&a=download&k=<key> download URL is retired and now answers
    # 404 for every key, valid or not.
    #
    # packages.json is public and lists a dist URL per release; the download
    # itself wants HTTP basic auth, licence key as the username and the site URL
    # as the password. Only the key is actually checked — a wrong one gets a 401.
    repo="https://connect.advancedcustomfields.com"

    # Newest version wins. `sort -V` rather than trusting the order in the JSON,
    # so a reordered feed can't silently pin an old release.
    version="$(curl -sS -L --max-time 30 "$repo/packages.json" \
        | grep -o '"version": *"[0-9][^"]*"' \
        | sed 's/.*"\([0-9][^"]*\)"$/\1/' \
        | sort -V | tail -1)"
    if [ -z "$version" ]; then
        echo "Could not read a version from $repo/packages.json." >&2
        echo "Nothing on the site was changed." >&2
        exit 1
    fi

    # Download before touching the running site, so a bad key or a network
    # problem fails while the free plugin is still installed and working.
    zip="$(mktemp -t acf-pro-XXXXXX.zip)"
    trap 'rm -f "$zip"' EXIT
    site="http://localhost:${WORDPRESS_PORT:-8080}"
    status="$(curl -sS -o "$zip" -w '%{http_code}' -L --max-time 120 \
        -u "${key}:${site}" \
        "$repo/v2/plugins/composer_download?s=composer&p=pro&t=${version}" || echo 000)"
    if [ "$status" != "200" ]; then
        echo "ACF did not accept the licence key (HTTP $status)." >&2
        echo "Check ACF_PRO_LICENSE in .env — nothing on the site was changed." >&2
        exit 1
    fi

    # mktemp makes the file 0600 and owned by you. wp-cli runs as uid 33, so it
    # has to be world-readable or the unzip fails with READ_OPEN_FAIL.
    chmod 644 "$zip"

    echo "Downloaded ACF PRO ${version}."

    # Same invocation as the `wp` recipe but as uid 33 (www-data inside the
    # wordpress container), which owns wp-config.php and wp-content/plugins in the
    # wp_data volume. `just wp` runs as your own user and can read those but not
    # write them, so installing or deleting a plugin through it fails.
    wp() {
        docker run --rm \
            --network letsdoo-website_wordpress_net \
            --volumes-from letsdoo_wordpress \
            --user 33:33 \
            -e HOME=/tmp \
            -e WORDPRESS_DB_HOST=db:3306 \
            -e WORDPRESS_DB_NAME="${DB_NAME}" \
            -e WORDPRESS_DB_USER="${DB_USER}" \
            -e WORDPRESS_DB_PASSWORD="${DB_PASSWORD}" \
            wordpress:cli "$@"
    }

    # --quiet: wp-cli otherwise echoes the value it just wrote, which puts the
    # licence key in the terminal and in any captured log.
    # The constant goes in before the install so PRO finds a key the moment it
    # boots, rather than nagging for one on the next admin page load.
    wp --quiet config set ACF_PRO_LICENSE "$key"

    # wp-cli runs in its own container, so the zip has to reach a path both
    # containers see — i.e. somewhere under the shared wp_data volume.
    #
    # NOT wp-content/upgrade, the obvious choice: WP_Upgrader::unpack_package()
    # empties that directory before it unzips anything, so a package staged there
    # is deleted and then reported missing.
    stage=/var/www/html/wp-content/acf-pro-install
    docker exec letsdoo_wordpress mkdir -p "$stage"
    docker cp "$zip" "letsdoo_wordpress:$stage/acf-pro.zip"
    trap 'rm -f "$zip"; docker exec letsdoo_wordpress rm -rf "$stage" >/dev/null 2>&1 || true' EXIT

    # ACF refuses to run with both editions active, so the free one goes before
    # PRO arrives. Nothing is lost in the swap: the field *groups* are registered
    # in code (inc/acf-fields.php) and the field *values* are ordinary post meta.
    # If the install fails anyway, put the free edition back rather than leaving
    # the site with no ACF at all and every field group gone from wp-admin.
    if wp plugin is-installed advanced-custom-fields; then
        wp plugin delete advanced-custom-fields
        rollback() {
            echo "PRO install failed — restoring the free edition." >&2
            wp plugin install advanced-custom-fields --activate || true
        }
    else
        rollback() { :; }
    fi

    if ! wp plugin install "$stage/acf-pro.zip" --force --activate; then
        rollback
        exit 1
    fi

    # Redeem the key against ACF's server. Without this the licence sits defined
    # but inactive until someone loads wp-admin, and updates stay switched off.
    docker exec letsdoo_wordpress php -r '
        define("WP_ADMIN", true);
        require "/var/www/html/wp-load.php";
        $r = acf_pro_activate_license( ACF_PRO_LICENSE, true );
        echo isset($r["message"]) ? strip_tags($r["message"]) . "\n" : "";
        echo "Licence status: ", acf_pro_get_license_status()["status"] ?? "unknown", "\n";
    '

    echo
    wp plugin list --name=advanced-custom-fields-pro --fields=name,status,version
    echo "Open http://localhost:${WORDPRESS_PORT:-8080}/wp-admin/edit.php?post_type=acf-field-group&page=acf-settings-updates"
    echo "once to confirm the licence shows as active."

# Dump the database to backups/<timestamp>.sql
backup-db:
    #!/usr/bin/env bash
    set -euo pipefail
    mkdir -p backups
    file="backups/$(date +%Y%m%d-%H%M%S).sql"
    docker compose exec -T db mariadb-dump -u root -p"${DB_ROOT_PASSWORD}" "${DB_NAME}" > "$file"
    echo "Saved $file"

# Restore the database from a dump, e.g. `just restore-db backups/20260720-160200.sql`
restore-db file:
    docker compose exec -T db mariadb -u root -p"${DB_ROOT_PASSWORD}" "${DB_NAME}" < {{file}}

# Stop the stack and permanently delete all data (db + wordpress volumes)
nuke:
    #!/usr/bin/env bash
    set -euo pipefail
    read -p "This deletes ALL WordPress and database data. Type 'yes' to continue: " confirm
    if [ "$confirm" = "yes" ]; then
        docker compose down -v
    else
        echo "Aborted"
    fi
