FROM php:8.3-apache

# Instalar MySQLi
RUN docker-php-ext-install mysqli

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar los archivos del proyecto
COPY . /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

# Puerto de Apache
EXPOSE 80

CMD ["apache2-foreground"]
