#
# Base image
#
ARG CORE_IMAGE
FROM ${CORE_IMAGE}

#
# Project specific configurations
#

ENV DIRECTUS_USE_ENV=1

#
# Copy files
#
ONBUILD COPY --chown=www-data:www-data . /var/www/
ONBUILD RUN \
  cd /var/www/ && \
  install-dependencies && \
  ( find . -type d -name ".git" && find . -name ".gitignore" && find . -name ".gitmodules" ) | xargs rm -rf

