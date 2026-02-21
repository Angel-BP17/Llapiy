# ==============================
# 1️⃣ Build stage
# ==============================
FROM composer:2 AS builder

WORKDIR /app

# Copiar TODO el proyecto primero
COPY . .

# Instalar dependencias sin dev
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


# ==============================
# 2️⃣ Production image
# ==============================
FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    nginx \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    bash

# Instalar extensiones necesarias
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    pcntl

WORKDIR /var/www

# Copiar app compilada desde builder
COPY --from=builder /app /var/www

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache

# Copiar config nginx
COPY nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 8080

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"