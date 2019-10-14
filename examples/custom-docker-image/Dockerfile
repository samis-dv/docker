FROM directus/api:v2-apache

# Redis is going to be in the dist image soon, but with this package we can easily show how to create custom images
RUN pecl install redis && docker-php-ext-enable redis
