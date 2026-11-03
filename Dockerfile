# ==========================================
# Stage 1: Build Frontend Assets (Vite)
# ==========================================
FROM node:20-alpine AS frontend
WORKDIR /app

# Copy package manifests and install dependencies
COPY package*.json ./
RUN npm ci --ignore-scripts || npm install --ignore-scripts

# Copy assets and files required by Vite / Tailwind
COPY resources/ resources/
COPY public/ public/
COPY vite.config.js ./
COPY app/ app/
COPY routes/ routes/
COPY config/ config/

# Build production assets (outputs to public/build)
RUN npm run build

# ==========================================
# Stage 2: Runtime PHP with FrankenPHP
# ==========================================
FROM dunglas/frankenphp:php8.4-bookworm

# Install required PHP extensions for Laravel & POS features
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    bcmath \
    intl \
    zip \
    opcache \
    gd \
    pcntl

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Set Composer environment
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy composer files and install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy application source code
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Complete composer autoload optimization
RUN composer dump-autoload --optimize --no-dev

# Prepare Laravel storage and cache directories with proper permissions
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint script and sanitize line endings (prevents CRLF error)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Render provides the port in $PORT (usually 10000)
ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

