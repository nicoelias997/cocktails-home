FROM serversideup/php:8.2-fpm-nginx

USER root

# Install Node.js 20 (ext-mongodb already included in serversideup image)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# PHP dependencies (layer cache: composer files first)
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-autoloader --no-interaction

# JS dependencies (layer cache: package files first)
COPY --chown=www-data:www-data package.json package-lock.json ./
RUN npm ci

# Full source copy + optimized autoloader + frontend build
COPY --chown=www-data:www-data . .
RUN composer dump-autoload --optimize \
    && npm run build \
    && mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache
