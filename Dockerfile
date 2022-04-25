FROM php:8.0.9-apache

# Install gd and pdo_mysql extension
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql

ENV TIME_ZONE=Africa/Douala
ENV HOME=/var/www
ENV DOCUMENT_ROOT=/var/www/html

# Use the php default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN mkdir ${HOME}/vendor

# Copy application source
WORKDIR ${HOME}
COPY app/ html/
COPY  vendor/ vendor/
RUN chown -R www-data:www-data .

# Use production configurations
RUN mv "./html/config/db-config-production.php" "./html/config/db-config.php" \
    && rm "./html/config/db-config-development.php" \
    && sed -ri "s/db-config-development/db-config/g" ./html/config/index.php \
    && sed -ri "s/#ServerName www.example.com/ServerName www.cadexsa.org/g" ${APACHE_CONFDIR}/sites-enabled/000-default.conf \
    && sed -ri "s/#ServerName www.example.com/ServerName www.cadexsa.org/g" ${APACHE_CONFDIR}/sites-available/000-default.conf

# Configure php.ini file
RUN sed -ri -e "s!;date.timezone =!date.timezone = $TIME_ZONE!g" ${PHP_INI_DIR}/php.ini \
    && sed -ri "s/;sendmail_from = me@example.com/sendmail_from = team@cadexsa.org/g" ${PHP_INI_DIR}/php.ini \
    && sed -ri "s/;opcache.enable/opcache.enable/g" ${PHP_INI_DIR}/php.ini

WORKDIR ${DOCUMENT_ROOT}