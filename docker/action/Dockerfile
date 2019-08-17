FROM docker:stable
RUN apk update && apk add bash jq curl unzip
COPY ./rootfs /
RUN chmod +x /usr/bin/common && \
    chmod +x /usr/bin/build && \
    chmod +x /usr/bin/fetch && \
    chmod +x /usr/bin/login && \
    chmod +x /usr/bin/push && \
    chmod +x /usr/bin/release
ENTRYPOINT ["bash", "-c"]
CMD ["release"]
