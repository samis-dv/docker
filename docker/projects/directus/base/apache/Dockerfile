#
# Base image
#
ARG BASE_IMAGE
FROM ${BASE_IMAGE}

#
# Some additional setup for apache
#

# None yet

#
# Copy files
#
ONBUILD COPY . /var/www/
ONBUILD RUN cd /var/www/ && install-dependencies
