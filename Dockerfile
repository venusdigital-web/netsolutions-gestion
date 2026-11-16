# Image de l'application : PHP avec le serveur web Apache, image officielle du Docker Hub.
# L'étiquette est précise : la même version de PHP à chaque construction.
FROM php:8.3.36-apache

# Le site est servi depuis public/ : src/, donnees/, stockage/ et vendor/ ne sont pas accessibles par le web.
ENV APACHE_DOCUMENT_ROOT=/var/www/app/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Réglages PHP propres à l'application.
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-appli.ini

COPY . /var/www/app
RUN mkdir -p /var/www/app/donnees /var/www/app/stockage \
    && chown -R www-data:www-data /var/www/app/donnees /var/www/app/stockage
WORKDIR /var/www/app
