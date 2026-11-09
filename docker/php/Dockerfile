# Image du site : Apache + PHP dans un seul conteneur.
#   target dev  : développement (docker-compose.yml), code monté depuis la machine, erreurs affichées.
#   target prod : production, code copié dans l'image, dépendances de dev absentes, OPcache.

# ── Base commune ─────────────────────────────────────────────
FROM php:8.4-apache AS base

# Extensions PHP : pdo_pgsql pour PostgreSQL, opcache pour la production.
# git et unzip servent à Composer pour télécharger les dépendances.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev git unzip \
    && docker-php-ext-install pdo_pgsql opcache \
    && rm -rf /var/lib/apt/lists/*

# Composer, copié depuis son image officielle.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache sert uniquement public/ : le reste du projet (src/, views/, .env…)
# n'est jamais accessible depuis le navigateur.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite headers \
    && sed -ri 's/^ServerTokens .*/ServerTokens Prod/; s/^ServerSignature .*/ServerSignature Off/' /etc/apache2/conf-available/security.conf

WORKDIR /var/www/html

# ── Développement ────────────────────────────────────────────
FROM base AS dev

# Réglages PHP de développement (erreurs affichées) ; versions masquées ; fichiers jusqu'à 10 Mo.
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && printf 'expose_php = Off\nupload_max_filesize = 12M\npost_max_size = 40M\n' > "$PHP_INI_DIR/conf.d/portfolio.ini"

# ── Production ───────────────────────────────────────────────
FROM base AS prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/php.prod.ini "$PHP_INI_DIR/conf.d/portfolio.ini"

# Derrière le reverse proxy (Caddy), REMOTE_ADDR serait toujours l'adresse de Caddy : les limites
# de tentatives (connexion, contact) seraient communes à tous les visiteurs. mod_remoteip remplace
# REMOTE_ADDR par l'adresse transmise dans X-Forwarded-For, uniquement si la requête vient des
# réseaux Docker : un visiteur ne peut pas se forger une adresse.
RUN a2enmod remoteip \
    && printf 'RemoteIPHeader X-Forwarded-For\nRemoteIPInternalProxy 10.0.0.0/8 172.16.0.0/12 192.168.0.0/16\n' > /etc/apache2/conf-available/remoteip.conf \
    && a2enconf remoteip

# Dépendances d'abord (couche mise en cache tant que composer.lock ne change pas), sans PHPUnit.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Code du site (voir .dockerignore : ni .env, ni storage/uploads, ni design/).
COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative \
    && mkdir -p storage/uploads storage/logs \
    && chown -R www-data:www-data storage
