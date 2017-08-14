FROM php:7.1-cli

RUN apt-get update --fix-missing && apt-get install -y git zip

ADD composer.json /app

ADD composer.lock /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && echo 'PATH=$PATH:/root/.composer/vendor/bin' >> /root/.bashrc

RUN composer global require "hirak/prestissimo"

WORKDIR /app

RUN php -d memory_limit=-1 /usr/local/bin/composer install \
        --no-ansi \
        --prefer-dist \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --optimize-autoloader \
        --working-dir \
            /app

ADD . /app

CMD php -S 0.0.0.0:8080 -t web
