# Image de développement : Apache + PHP dans un seul conteneur.
FROM php:8.4-apache

# Extensions PHP : pdo_pgsql pour PostgreSQL.
# git et unzip servent à Composer pour télécharger les dépendances.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev git unzip \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Composer, copié depuis son image officielle.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache sert uniquement public/ : le reste du projet (src/, views/, .env…)
# n'est jamais accessible depuis le navigateur.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite headers

# Réglages PHP de développement (erreurs affichées).
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html
