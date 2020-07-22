FROM composer

COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-dev --ignore-platform-reqs --no-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --optimize --no-dev

RUN mkdir composer-autoload
RUN mv vendor/autoload.php vendor/composer ./composer-autoload
