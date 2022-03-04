ARG IMAGE_PHP_VERSION=${IMAGE_PHP_VERSION}
FROM php:${IMAGE_PHP_VERSION:-8.0}-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk add git \
&&  git config --global user.email "${GITHUB_USER}"

RUN wget https://github.com/symfony-cli/symfony-cli/releases/download/v5.4.1/symfony-cli_5.4.1_x86_64.apk \
&& apk add --allow-untrusted symfony-cli_5.4.1_x86_64.apk \
&& rm symfony-cli_5.4.1_x86_64.apk

ENTRYPOINT chmod +x ./entrypoint.sh && ./entrypoint.sh
