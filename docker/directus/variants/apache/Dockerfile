#
# Download release
#
FROM alpine AS builder

ARG RELEASE

ADD "https://github.com/directus/directus/archive/${RELEASE}.tar.gz" /tmp/
RUN \
  mkdir /download && \
  tar -xzf /tmp/${RELEASE}.tar.gz -C / && \
  mv -v "/directus-$(echo ${RELEASE} | tr -d v)" "/artifacts"

#
# Final image
#
FROM php:7.3-apache

RUN a2enmod rewrite

COPY --from=builder --chown=www-data:www-data /artifacts/ /var/www/
RUN \
  rm -rf /var/www/html/ && \
  mv /var/www/public/ /var/www/html/

EXPOSE 80
