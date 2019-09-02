#
# Base image
#
ARG CORE_IMAGE
FROM ${CORE_IMAGE}

#
# Server setup
#
COPY ./rootfs/ /
RUN cd /server/ && npm install

#
# Copy files
#
ONBUILD COPY . /app/
