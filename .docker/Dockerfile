FROM php:7-apache
MAINTAINER Elvis Oliveira <elvis.olv@gmail.com>

# Project Dependencies
WORKDIR /var/www/blacklist

# Enviroment Dependencies:
RUN apt-get update && \
    apt-get install build-essential sqlite3 libsqlite3-dev -y

# Install APCu and APC backward compatibility
RUN pecl install apcu \
    && pecl install apcu_bc-1.0.3 \
    && docker-php-ext-enable apcu --ini-name 10-docker-php-ext-apcu.ini \
    && docker-php-ext-enable apc --ini-name 20-docker-php-ext-apc.ini

# PHP / Apache Conf.
COPY ./.docker/php.ini ${PHP_INI_DIR}/php.ini
COPY ./.docker/apache2.conf ${APACHE_CONFDIR}/apache2.inc.conf
COPY ./.docker/000-default.conf ${APACHE_CONFDIR}/sites-available/000-default.conf

RUN echo "include apache2.inc.conf" >> /etc/apache2/apache2.conf

RUN a2enmod rewrite && \
    apache2ctl restart

# Exec.
EXPOSE 80