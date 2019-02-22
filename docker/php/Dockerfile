# ./docker/php/Dockerfile
FROM php:7.2-fpm

RUN docker-php-ext-install pdo_mysql

RUN pecl install apcu-5.1.8
RUN docker-php-ext-enable apcu

RUN apt-get install php7.2-zip
RUN rm -rf /var/cache/apk/* && rm -rf /tmp/* && \
    curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

ADD symfony.ini /etc/php7/conf.d/
ADD symfony.ini /etc/php7/cli/conf.d/
ADD xdebug.ini /etc/php7/conf.d/

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

WORKDIR /usr/src/app

COPY / /usr/src/app

RUN PATH=$PATH:/usr/src/apps/vendor/bin:bin
