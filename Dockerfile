FROM composer AS builder

COPY composer.* /app/
COPY src/main /app/src/main
COPY index.php /app/
COPY .htaccess /app/

WORKDIR /app
RUN composer install --no-dev --ignore-platform-reqs && \
    rm /app/composer.*


FROM php:apache

ENV APACHE_DOCUMENT_ROOT /app

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite

COPY --from=builder /app /app

WORKDIR /app
EXPOSE 80