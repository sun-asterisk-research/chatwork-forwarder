#### Web build
FROM node:12-alpine as web

WORKDIR /cw-forwarder

ARG APP_URL
ARG GOOGLE_CLIENT_ID
ARG GOOGLE_CLIENT_SECRET
ARG GOOGLE_REDIRECT

ENV APP_URL=${APP_URL} \
    GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID} \
    GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET} \
    GOOGLE_REDIRECT=${GOOGLE_REDIRECT}

COPY package.json package-lock.json ./

RUN npm ci

COPY . .

RUN npm run production

#### PHP vendor
FROM composer as vendor

COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-dev --ignore-platform-reqs --no-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --optimize --no-dev

RUN mkdir composer-autoload
RUN mv vendor/autoload.php vendor/composer ./composer-autoload

#### PHP application
FROM sunasteriskrnd/php:7.4-caddy-alpine

ENV PHP.opcache.validate_timestamps=0

WORKDIR /cw-forwarder

RUN wget -qO /usr/local/bin/wait-for https://raw.githubusercontent.com/eficode/wait-for/master/wait-for \
    && chmod +x /usr/local/bin/wait-for

COPY --from=vendor /app/vendor ./vendor
COPY --from=vendor /app/composer-autoload ./vendor

COPY --from=web /cw-forwarder/public/mix-manifest.json public/
COPY --from=web /cw-forwarder/public/css/ public/css/
COPY --from=web /cw-forwarder/public/js/ public/js/

COPY --chown=www-data:www-data  . .

RUN mv docker/Caddyfile /etc/Caddyfile
