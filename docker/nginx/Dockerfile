FROM nginx:alpine

# Copiar configuración de Nginx
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copiar solo el directorio public (Nginx no necesita el resto del código)
COPY ./public /var/www/html/public
