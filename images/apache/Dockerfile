#
# Final image
#
FROM php:7.3-apache

#
# Variables
#
ENV DIRECTUS_USE_ENV=1
ENV COMPOSER_ALLOW_SUPERUSER=1

#
# Dependencies & extensions
#
RUN \
  apt-get update && \
  apt-get install -y git zip libpng-dev libjpeg-dev libzip-dev libfreetype6-dev libxml2-dev libmagickwand-dev && \
  rm -rf /var/lib/apt/lists/* && \
  docker-php-ext-configure zip --with-libzip && \
  docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
  docker-php-ext-install gd zip exif pdo pdo_mysql xml fileinfo && \
  pecl install imagick && pecl install redis && \
  docker-php-ext-enable imagick && docker-php-ext-enable redis

#
# Filesystem & permissions
#
COPY ./rootfs/ /
RUN \
  chmod +x /usr/local/bin/php-ini-add && \
  chmod +x /usr/local/bin/argsf && \
  chmod +x /usr/local/bin/directus-entrypoint && \
  chmod +x /usr/local/bin/install-dependencies && \
  chmod +x /usr/local/bin/install-composer && install-composer && \
  composer global require hirak/prestissimo && \
  a2enmod rewrite headers && \
  a2dissite 000-default && \
  a2enconf directus && \
  a2ensite directus && \
  php-ini-add "memory_limit=512M"

#
# Port
#
EXPOSE 80

#
# Copy files
#

COPY --chown=www-data:www-data ./directus/ /var/directus/

#
# Final
#
WORKDIR /var/directus
RUN \
  ls -lah . && \
  install-dependencies && \
  composer require aws/aws-sdk-php && \
  composer require league/flysystem-aws-s3-v3 && \
  ( find . -type d -name ".git" && find . -name ".gitignore" && find . -name ".gitmodules" ) | xargs rm -rf

#
# New entrypoint
#
ENTRYPOINT ["directus-entrypoint"]
CMD ["apache2-foreground"]
