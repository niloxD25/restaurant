FROM php:8.2-fpm

# Installer les dépendances de base
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers du projet
COPY . /app

# Installer les dépendances PHP avec Composer
RUN composer install --ignore-platform-reqs

# Exposer le port
EXPOSE 8025

CMD ["php-fpm"]