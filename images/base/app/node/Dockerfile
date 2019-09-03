#
# Base image
#
ARG CORE_IMAGE
FROM ${CORE_IMAGE}

#
# Settings
#
WORKDIR /server/

#
# Server setup
#
COPY ./rootfs/ /
RUN npm install --silent

#
# Copy files
#
ONBUILD COPY package.json /app/package.json
ONBUILD RUN cd /app/ && npm install --silent
ONBUILD COPY . /app/
ONBUILD RUN cd /app/ && \
            npm run build && \
            mv /app/dist /server/dist && \
            rm -rf /app/

#
# Entrypoint
#
CMD ["node", "index"]
