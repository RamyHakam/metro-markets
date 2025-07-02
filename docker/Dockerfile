FROM dunglas/frankenphp

WORKDIR /app

# install build dependencies, including MySQL client dev headers
RUN apt-get update \
    && apt-get install -y \
        unzip git \
        libzip-dev libpng-dev libonig-dev libxml2-dev pkg-config libssl-dev zlib1g-dev \
        default-libmysqlclient-dev default-mysql-client \
    && pecl install redis \
    && docker-php-ext-enable redis \
    \
    # install & enable pdo_mysql
    && docker-php-ext-install pdo_mysql

# install the Symfony CLI for Development
RUN curl -sS https://get.symfony.com/cli/installer | bash \
  && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

COPY . /app

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-interaction --no-scripts --optimize-autoloader


EXPOSE  80
EXPOSE 443

