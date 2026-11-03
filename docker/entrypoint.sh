#!/bin/sh
set -e

# Buat symbolic link storage public jika belum ada
php artisan storage:link --no-interaction || true

# Optimasi konfigurasi, route, dan view cache
if [ -n "$APP_KEY" ]; then
    echo "==> Caching Laravel configuration, routes, and views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Jalankan migrasi database otomatis jika RUN_MIGRATIONS=true
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force || echo "Warning: Migration failed or database not ready yet."
fi

# Jalankan seeder otomatis jika RUN_SEEDER=true
if [ "$RUN_SEEDER" = "true" ]; then
    echo "==> Running database seeder..."
    php artisan db:seed --force || echo "Warning: Seeder failed or already seeded."
fi

# Jalankan FrankenPHP web server pada port yang ditentukan oleh Render
PORT="${PORT:-10000}"
echo "==> Starting FrankenPHP web server on port $PORT..."
exec frankenphp php-server --root /app/public --listen "0.0.0.0:$PORT"

