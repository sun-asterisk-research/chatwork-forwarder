ARG REGISTRY_PATH
ARG TAG

#### PHP
FROM ${REGISTRY_PATH}/laravel-app:${TAG} as laravel

#### Web build
FROM ${REGISTRY_PATH}/web-app:${TAG} as web

FROM sunasteriskrnd/php:7.3-caddy-alpine

ENV PHP.opcache.validate_timestamps=0

WORKDIR /cw-forwarder

RUN wget -qO /usr/local/bin/wait-for https://raw.githubusercontent.com/eficode/wait-for/master/wait-for \
    && chmod +x /usr/local/bin/wait-for

COPY --from=laravel /app/vendor ./vendor
COPY --from=laravel /app/composer-autoload ./vendor

COPY --from=web /cw-forwarder/public/mix-manifest.json public/
COPY --from=web /cw-forwarder/public/css/ public/css/
COPY --from=web /cw-forwarder/public/js/ public/js/

COPY --chown=www-data:www-data  . .

RUN mv docker/Caddyfile /etc/Caddyfile
