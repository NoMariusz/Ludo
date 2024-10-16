FROM php:7.4.3-fpm-alpine3.11

# Copy the application code
COPY . /app

VOLUME ["/app"]

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
