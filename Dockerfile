FROM php:8.3-apache

# Instalar MySQLi
RUN docker-php-ext-install mysqli

# Activar mod_rewrite
RUN a2enmod rewrite

# Copiar la aplicación PHP
COPY . /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

# Puerto Apache
EXPOSE 80

# Corregir los módulos MPM al iniciar el contenedor
CMD ["bash", "-c", "set -e; rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf; a2enmod mpm_prefork; apache2ctl -t; exec apache2-foreground"]
