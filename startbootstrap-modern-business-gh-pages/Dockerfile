FROM php:8.3-apache

# Instala extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita o mod_rewrite
RUN a2enmod rewrite

# Permite utilizar configurações do .htaccess
RUN echo "<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/custom.conf \
&& a2enconf custom
# Aumenta os limites de envio de arquivos
RUN echo "upload_max_filesize = 5M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 30M" >> /usr/local/etc/php/conf.d/uploads.ini