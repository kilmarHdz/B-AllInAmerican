FROM php:8.2-fpm-alpine

# Instalar dependencias del sistema requeridas por Laravel y PostgreSQL
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    oniguruma-dev

# Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql zip bcmath mbstring

# Obtener Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias de PHP (sin dev)
RUN composer install --no-dev --optimize-autoloader

# Establecer permisos para los directorios que requieren escritura
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
