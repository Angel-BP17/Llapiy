# ==============================
# 1) Build stage
# ==============================
FROM composer:2 AS builder

WORKDIR /app

# Copiar todo el proyecto
COPY . .

# Instalar dependencias sin dev
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


# ==============================
# 2) Production image
# ==============================
FROM php:8.3-fpm-alpine

# Dependencias del sistema
RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev

# Extensiones PHP
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    gd \
    pcntl

WORKDIR /var/www

# Copiar app compilada desde builder
COPY --from=builder /app /var/www

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache

# Nginx config
COPY nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 8080

# php-fpm escucha en 9000 dentro del contenedor
CMD sh -c "nginx -t && php-fpm -D && nginx -g 'daemon off;'"
