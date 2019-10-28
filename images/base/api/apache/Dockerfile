#
# Base image
#
ARG CORE_IMAGE
FROM ${CORE_IMAGE}

#
# Project specific configurations
#

ENV DIRECTUS_USE_ENV=1

# Image specific tweaks
RUN pecl install redis && docker-php-ext-enable redis && \
  php-ini-add "memory_limit=512M"

# Image files
COPY ./rootfs/ /
RUN chmod +x /usr/local/bin/api-entrypoint && \
  chmod +x /usr/local/bin/argsf

#
# Copy files
#
ONBUILD COPY --chown=www-data:www-data . /var/www/
ONBUILD RUN \
  cd /var/www/ && \
  install-dependencies && \
  composer require aws/aws-sdk-php && \
  composer require league/flysystem-aws-s3-v3 && \
  ( find . -type d -name ".git" && find . -name ".gitignore" && find . -name ".gitmodules" ) | xargs rm -rf

ENTRYPOINT ["api-entrypoint"]

CMD ["apache2-foreground"]
