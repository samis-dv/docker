#
# Final image
#
FROM php:7.3-apache

#
# Variables
#
ENV DIRECTUS_LOAD_ENV=1
ENV COMPOSER_ALLOW_SUPERUSER=1

#
# Dependencies & extensions
#
RUN \
  apt-get update && \
  apt-get install -y git zip libpng-dev libzip-dev libxml2-dev  && \
  rm -rf /var/lib/apt/lists/* && \
  a2enmod rewrite && \
  docker-php-ext-configure zip --with-libzip && \
  docker-php-ext-install gd zip exif pdo pdo_mysql xml fileinfo

#
# Filesystem & permissions
#
COPY ./rootfs/ /
RUN \
  chmod +x /usr/local/bin/docker-entrypoint && \
  chmod +x /usr/local/bin/install-dependencies && \
  chmod +x /usr/local/bin/install-composer && install-composer && \
  composer global require hirak/prestissimo && \
  rm -rf /var/www

#
# Port
#
EXPOSE 80

#
# New entrypoint
#
ENTRYPOINT ["docker-entrypoint"]
CMD ["apache2-foreground"]
