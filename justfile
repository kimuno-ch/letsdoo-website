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
