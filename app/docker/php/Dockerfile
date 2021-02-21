FROM php:7.4-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER="1"
ENV PATH="/app/bin:/app/vendor/bin:${PATH}"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

WORKDIR /app

EXPOSE 9000

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-3.0.0 \
    && docker-php-ext-enable xdebug \
    && apk add --no-cache mysql-client

COPY docker/php/php.ini /usr/local/etc/php/php.ini
ARG XDEBUG_REMOTE_HOST="172.17.0.1"
RUN sed -i "s|xdebug.client_host=localhost|xdebug.client_host=$XDEBUG_REMOTE_HOST|g" /usr/local/etc/php/php.ini

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN install-php-extensions intl mysqli opcache pdo_mysql

ENTRYPOINT ["sh", "/usr/local/bin/entrypoint.sh"]
CMD php-fpm

