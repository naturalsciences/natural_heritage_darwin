#https://github.com/docker-library/php/blob/7f89b1440e0122cc0edaf4ee0dbd8e1d7510df73/8.2/alpine3.18/fpm/Dockerfile
FROM php:8.2.11-fpm-alpine3.18

EXPOSE 9000

COPY php.ini /usr/local/etc/php/
RUN apk update

RUN apk add --no-cache g++
RUN apk add --no-cache make
RUN apk add --no-cache alpine
RUN apk add --no-cache autoconf
RUN apk add --no-cache pkgconf
RUN apk add --no-cache zlib
RUN apk add --no-cache zlib-dev
RUN apk add --no-cache zlib-ng
RUN apk add --no-cache zlib-ng-dev
RUN apk add --no-cache libecpg
#RUN apk add --no-cache postgresql
#RUN apk add --no-cache postgresql-client
RUN apk add --no-cache postgresql-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

RUN apk add --no-cache php82-xml
RUN apk add --no-cache php82-mbstring
RUN apk add --no-cache php82-iconv
RUN apk add --no-cache php82-curl
RUN apk add --no-cache php82-pecl-yaml
#RUN apk add --no-cache php82-pgsql
#RUN apk add --no-cache php82-pdo_pgsql
#RUN apk add --no-cache php82-pdo_pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN apk add --no-cache libmemcached
RUN apk add --no-cache libmemcached-dev
RUN apk add --no-cache memcached
RUN apk add --no-cache memcached-dev
RUN apk add --no-cache php82-pecl-apcu
RUN apk add --no-cache php82-pecl-memcache
RUN apk add --no-cache php82-pecl-memcached
RUN pecl install memcache
RUN pecl install memcached
#RUN ln -s /etc/php82/conf.d/00_pgsql.ini /usr/local/etc/php/conf.d/00_pgsql.ini
RUN ln -s /etc/php82/conf.d/20_memcached.ini /usr/local/etc/php/conf.d/20_memcached.ini
RUN ln -s /etc/php82/conf.d/memcache.ini /usr/local/etc/php/conf.d/20_memcache.ini
#RUN chmod -R 777 /etc/php82
CMD ["php-fpm"]
