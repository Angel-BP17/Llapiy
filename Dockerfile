# ==============================
# 1) Build PHP Dependencies
# ==============================
FROM composer:2 AS composer_builder

WORKDIR /app

# Optimizar caché de capas: copiar solo archivos de composer primero
COPY composer.json composer.lock ./

# Instalar dependencias sin scripts ni autoloader (para aprovechar caché)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-scripts \
    --prefer-dist

# Copiar el resto del código del proyecto
COPY . .

# Generar el autoloader optimizado ahora que tenemos el código
RUN composer install \
    --no-dev \
    --no-interaction \
    --optimize-autoloader \
    --classmap-authoritative


# ==============================
# 2) Production Image
# ==============================
FROM php:8.3-fpm-alpine

# Variables de entorno de producción
ENV APP_ENV=production \
    APP_DEBUG=false

# Dependencias del sistema (Runtime)
RUN apk add --no-cache \
    nginx \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    postgresql-libs \
    oniguruma

# Instalación de extensiones y limpieza de dependencias de compilación (.build-deps)
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        mbstring \
        zip \
        exif \
        gd \
        opcache \
        pcntl \
    && apk del .build-deps

WORKDIR /var/www

# Copiar aplicación compilada desde el stage builder
COPY --from=composer_builder /app /var/www

# Configuración recomendada de OPcache para Laravel
RUN echo "opcache.memory_consumption=128" > $PHP_INI_DIR/conf.d/opcache-recommended.ini \
    && echo "opcache.interned_strings_buffer=8" >> $PHP_INI_DIR/conf.d/opcache-recommended.ini \
    && echo "opcache.max_accelerated_files=4000" >> $PHP_INI_DIR/conf.d/opcache-recommended.ini \
    && echo "opcache.revalidate_freq=0" >> $PHP_INI_DIR/conf.d/opcache-recommended.ini \
    && echo "opcache.fast_shutdown=1" >> $PHP_INI_DIR/conf.d/opcache-recommended.ini

# Configuración de Nginx
COPY nginx.conf /etc/nginx/http.d/default.conf

# Permisos de carpetas de Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

# Comando final: caché de Laravel, validación de Nginx y ejecución de servicios
CMD sh -c "php artisan config:cache && php artisan route:cache && php artisan view:cache && nginx -t && php-fpm -D && nginx -g 'daemon off;'"
