FROM php:8.3

RUN apt-get update && apt-get install -y --no-install-recommends \
  libzip-dev \
  libicu-dev \
  libpq-dev \
  libsqlite3-dev \
  libcurl4-openssl-dev \
  libxml2-dev \
  zip \
  unzip \
  curl \
  pkg-config \
  && docker-php-ext-configure zip \
  && docker-php-ext-install \
  dom \
  intl \
  pdo_pgsql \
  pdo_sqlite \
  pdo_mysql \
  opcache \
  zip \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

CMD php artisan serve --host 0.0.0.0
