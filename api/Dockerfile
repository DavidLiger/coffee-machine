FROM php:8.2-cli

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Configurer le répertoire de travail
WORKDIR /var/www/html

# Copier le projet dans le conteneur
COPY . .

# Installer Composer (gestionnaire de dépendances PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y librabbitmq-dev libssh-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Exécuter composer install pour les dépendances
RUN composer install --no-interaction

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

