FROM php:8.3-cli

# dépendances système
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev \
    nodejs npm \
    && docker-php-ext-install zip pdo pdo_mysql

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# copier projet
COPY . .

# installer PHP deps
RUN composer install --no-dev --optimize-autoloader

# installer frontend (Vite / Tailwind)
RUN npm install && npm run build

# permissions Laravel
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000