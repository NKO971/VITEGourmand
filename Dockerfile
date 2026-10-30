# Image de base : PHP 8.4.27 minimum (correctif php2shell CVE-2026-109618 / CVE-2026-109619)
FROM php:8.4.27-apache

# Paquets système : libssl-dev et pkg-congig pour compiler MongoDB avec TLS (exigé par Atlas), unzip pour composer 
RUN apt-get update \
    && apt-get install -y --no-install-recommends libssl-dev pkg-config unzip \
    && rm -rf /var/lib/apt/lists/*

# Extention PHP manquantes
RUN docker-php-ext-install pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Configuration : fuseau horaire, nom du serveur, et Apache qui sert uniquement public/
RUN echo "date.timezone=Europe/Paris" > /usr/local/etc/php/conf.d/timezone.ini \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf


# Composer, récupéré depuis son image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Dossier de travail dans le conteneur
WORKDIR /var/www/html

# Dépendances PHP d'abord (pour profiter du cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Le code de l'application ensuite
COPY . .

# Documentation : le conteneur écoute sur le port 80
EXPOSE 80