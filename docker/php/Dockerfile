FROM php:8.2-fpm

RUN apt-get update --fix-missing
RUN apt-get install -y zip unzip libonig-dev libicu-dev
RUN apt-get install -y libzip-dev libpq-dev
RUN apt-get install -y build-essential libssl-dev zlib1g-dev
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo zip pdo_pgsql pgsql opcache
RUN docker-php-ext-enable pdo zip pdo_pgsql pgsql opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www
WORKDIR /var/www

RUN chmod 777 -R /var/www/cache
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

RUN php saya key
RUN php saya view:cache

EXPOSE 9000
CMD ["php-fpm"]
