FROM php:7.2-fpm

RUN useradd --user-group --shell /bin/false app

RUN apt-get update
RUN apt-get install -y unzip zlib1g-dev libcurl3-dev curl git openssh-client

RUN docker-php-ext-install pdo_mysql mysqli zip curl fileinfo

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php

RUN mkdir -p /code
ENV HOME=/code
WORKDIR $HOME

USER root
COPY . $HOME

RUN chown -R app $HOME
RUN chmod -R 777 $HOME

RUN printf '[www]\n\nuser=app\ngroup=app\n' >> /usr/local/etc/php-fpm.d/app-user.conf

USER app
