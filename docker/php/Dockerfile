FROM php:7.4-fpm-alpine

RUN apk update && apk upgrade
RUN apk add --no-cache git make
RUN apk add --no-cache curl-dev libzip-dev libcurl libxml2-dev
RUN apk add --no-cache pkgconfig openssl openssl-dev zlib-dev
RUN apk add --no-cache libintl icu icu-dev musl-dev
RUN apk add --no-cache mariadb-client

RUN apk add --no-cache tzdata
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

RUN docker-php-ext-install curl zip xml opcache pcntl intl pdo pdo_mysql mysqli soap

RUN set -x \
    && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .phpize-deps

ARG XDEBUG=0
RUN if [ "$XDEBUG" = "1" ] ; then set -x \
    && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .phpize-deps ; fi

STOPSIGNAL SIGQUIT

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY overrides.conf /usr/local/etc/php-fpm.d/zz-overrides.conf

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php-cli.ini

RUN sed -i 's/memory_limit = 128M/memory_limit = -1/' /usr/local/etc/php/php-cli.ini

EXPOSE 9000

CMD ["php-fpm"]