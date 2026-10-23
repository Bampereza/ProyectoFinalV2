FROM php:8.3-apache

# Instalar MySQLi
RUN docker-php-ext-install mysqli

# Habilitar URLs amigables
RUN a2enmod rewrite

# Mantener únicamente el MPM prefork
RUN find /etc/apache2/mods-enabled/ \
    -maxdepth 1 -type l \
    -name 'mpm_*.load' \
    ! -name 'mpm_prefork.load' \
    -delete \
    && a2enmod mpm_prefork

# Copiar el proyecto PHP
COPY . /var/www/html/

# Permisos del proyecto
RUN chown -R www-data:www-data /var/www/html

# Verificar la configuración de Apache
RUN apache2ctl -t

EXPOSE 80

CMD ["apache2-foreground"]
