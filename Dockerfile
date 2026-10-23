FROM php:8.3-apache

# Instalar la extensión MySQLi
RUN docker-php-ext-install mysqli

# Dejar habilitado solamente MPM prefork
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true
RUN a2enmod mpm_prefork rewrite

# Verificar la configuración de Apache
RUN apache2ctl -t

# Copiar los archivos PHP del proyecto
COPY . /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

# Puerto de Apache
EXPOSE 80

CMD ["apache2-foreground"]
