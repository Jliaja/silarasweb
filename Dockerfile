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

EXPOSE 8080
CMD ["frankenphp", "php-server", "--listen", "0.0.0.0:8080"]