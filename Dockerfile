FROM php:8.2-apache

# Habilitar mod_rewrite
RUN a2enmod rewrite
RUN a2enmod headers

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install mysqli

#Esconde la versión de PHP
RUN echo "expose_php=Off" > /usr/local/etc/php/conf.d/security.ini 

#Esconde los leaks del campo Server en los requests
RUN echo "ServerTokens Prod" >> /etc/apache2/conf-enabled/security.conf && \
    echo "ServerSignature Off" >> /etc/apache2/conf-enabled/security.conf && \
    echo "Header always unset Server" >> /etc/apache2/conf-enabled/security.conf

# Copiar una configuración de Apache personalizada que permite .htaccess
# y establece permisos adecuados para el documento raíz
COPY apache2.conf /etc/apache2/apache2.conf

# Asegurar permisos correctos en /var/www/html
RUN chown -R www-data:www-data /var/www/html \
	&& chmod -R 755 /var/www/html
