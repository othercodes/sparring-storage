FROM phpswoole/swoole:4.8-php8.1-alpine
WORKDIR /app
COPY . .

RUN apk --no-cache upgrade && \
    apk --no-cache add openssh libxml2-dev oniguruma-dev autoconf gcc g++ make libzip-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_mysql mbstring xml gd zip pcntl bcmath pdo soap
RUN docker-php-ext-enable pdo pdo_mysql mbstring xml gd zip pcntl bcmath pdo soap

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install
RUN php artisan key:generate
CMD php artisan octane:start --server="swoole" --host="0.0.0.0"
EXPOSE 8000
