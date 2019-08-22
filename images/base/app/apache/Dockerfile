#
# Base image
#
ARG CORE_IMAGE
FROM ${CORE_IMAGE}

#
# Project specific configurations
#

# ...

#
# Copy files
#
ONBUILD COPY --chown=www-data:www-data . /var/www/
ONBUILD RUN cd /var/www/ && install-dependencies
