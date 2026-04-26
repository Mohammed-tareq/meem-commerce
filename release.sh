#!/bin/sh
set -e

APP_DIR="/var/www/html"

log() {
    echo "$1"
}

normalize_railway_database_env() {
    if [ -z "${DB_CONNECTION:-}" ]; then
        export DB_CONNECTION="mysql"
    fi

    if [ "${DB_CONNECTION:-}" = "mysql" ] && [ -n "${MYSQLHOST:-}" ]; then
        export DB_HOST="${DB_HOST:-$MYSQLHOST}"
        export DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
        export DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-}}"
        export DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-}}"
        export DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"
    fi
}

wait_for_database() {
    max_attempts="${DB_WAIT_MAX_ATTEMPTS:-30}"
    sleep_seconds="${DB_WAIT_SLEEP_SECONDS:-2}"
    attempt=1

    while [ "$attempt" -le "$max_attempts" ]; do
        if php -r '
            $host = getenv("DB_HOST") ?: "127.0.0.1";
            $port = getenv("DB_PORT") ?: "3306";
            $db = getenv("DB_DATABASE") ?: "";
            $user = getenv("DB_USERNAME") ?: "";
            $pass = getenv("DB_PASSWORD") ?: "";
            $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4", $host, $port, $db);

            new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
        ' >/dev/null 2>&1; then
            return 0
        fi

        log "Database not reachable yet (attempt ${attempt}/${max_attempts}). Retrying in ${sleep_seconds}s..."
        sleep "$sleep_seconds"
        attempt=$((attempt + 1))
    done

    log "ERROR: Database connection failed after ${max_attempts} attempts."
    return 1
}

log "Running Railway pre-deploy tasks..."
cd "$APP_DIR"

normalize_railway_database_env

if [ "${RUN_MIGRATIONS:-true}" != "true" ]; then
    log "RUN_MIGRATIONS is not true, skipping migrations."
    exit 0
fi

if [ -z "${APP_KEY:-}" ]; then
    log "ERROR: APP_KEY environment variable is not set."
    exit 1
fi

wait_for_database

log "Running database migrations..."
php artisan migrate --force --no-interaction

if [ "${RUN_SEED:-false}" = "true" ]; then
    log "RUN_SEED=true, running database seeder..."
    php artisan db:seed --force --no-interaction
fi

log "Pre-deploy tasks completed successfully."
