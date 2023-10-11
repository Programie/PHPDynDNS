FROM composer AS builder

COPY composer.* /app/
COPY src/main /app/src/main
COPY index.php /app/
COPY .htaccess /app/

WORKDIR /app
RUN composer install --no-dev --ignore-platform-reqs && \
    rm /app/composer.*


FROM ghcr.io/programie/dockerimages/php

ENV WEB_ROOT=/app

RUN apt-get update && \
    apt-get install -y dnsutils && \
    install-php 8.1 && \
    a2enmod rewrite

COPY --from=builder /app /app

WORKDIR /app