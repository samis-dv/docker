#
# Final image
#
FROM php:7.3-apache

# Variables
ARG RELEASE

# Variables
ENV DIRECTUS_LOAD_ENV=1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Dependencies & extensions
RUN \
  apt-get update && \
  apt-get install -y git zip libpng-dev libzip-dev libxml2-dev  && \
  rm -rf /var/lib/apt/lists/* && \
  a2enmod rewrite && \
  docker-php-ext-configure zip --with-libzip && \
  docker-php-ext-install gd zip exif pdo pdo_mysql xml fileinfo

# Filesystem & permissions
COPY ./variants/apache/rootfs/ /
RUN \
  chmod +x /usr/local/bin/custom-entrypoint.sh && \
  chmod +x /usr/local/bin/install-composer.sh && install-composer.sh && \
  composer global require hirak/prestissimo

# New entrypoint
ENTRYPOINT ["custom-entrypoint.sh"]
CMD ["apache2-foreground"]

# Copy files
ADD ./releases/${RELEASE}.tar.gz /var/
WORKDIR /var
RUN \
  rm -rf /var/www && \
  cd /var && mv "directus-$(echo ${RELEASE} | tr -d v)" "www" > /dev/null && \
  chown -R www-data:www-data /var/www

WORKDIR /var/www

# Automatic export
EXPOSE 80
