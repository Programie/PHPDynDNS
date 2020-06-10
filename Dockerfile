FROM composer AS builder

COPY composer.* /app/
COPY src/main /app/src/main
COPY index.php /app/
COPY .htaccess /app/

WORKDIR /app
RUN composer install --no-dev --ignore-platform-reqs && \
    rm /app/composer.*


FROM php:apache

RUN apt-get update && \
    apt-get install -y dnsutils && \
    rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT /app

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    echo "ServerTokens Prod" > /etc/apache2/conf-enabled/z-server-tokens.conf && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN a2enmod rewrite

COPY --from=builder /app /app

WORKDIR /app
EXPOSE 80