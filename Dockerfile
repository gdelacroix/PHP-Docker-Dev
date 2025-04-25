FROM php:8.2.10-apache-bullseye

# Installer toutes les dépendances nécessaires
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    unzip \
    libzip-dev \
    git \
    && docker-php-ext-install zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
