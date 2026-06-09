FROM dunglas/frankenphp:php8.2

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    nodejs \
    npm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN install-php-extensions \
    pdo_mysql \
    gd \
    mbstring \
    zip \
    exif \
    bcmath

WORKDIR /app

COPY . .

COPY Caddyfile /etc/caddy/Caddyfile

RUN composer install --no-dev --optimize-autoloader

RUN npm install
RUN npm run build

RUN chmod -R 777 storage bootstrap/cache

RUN php artisan storage:link || true
RUN php artisan route:clear
RUN php artisan config:clear
EXPOSE 8080
RUN mkdir -p /app/storage/app/public
# ... terus di bagian CMD, lu bikin link-nya dulu
# Ganti CMD lu jadi ini:
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]