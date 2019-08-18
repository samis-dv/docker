#
# Base image
#
ARG CORE_IMAGE
FROM ${CORE_IMAGE}

#
# Project specific configurations
#

ENV DIRECTUS_LOAD_ENV=1

#
# Copy files
#
ONBUILD COPY . /var/www/
ONBUILD RUN cd /var/www/ && install-dependencies
