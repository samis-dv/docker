#!/bin/bash

#
# Exports
#

export DOCKER_PROJECTS=./projects

export BUILDER_IMAGE=${BUILDER_IMAGE:-"directus/builder:latest"}

export TARGET_IMAGE_BUILDER=${TARGET_IMAGE_BUILDER:-"directus/builder"}
export TARGET_IMAGE_API=${TARGET_IMAGE_API:-"directus/api"}
export TARGET_IMAGE_APP=${TARGET_IMAGE_APP:-"directus/app"}
export TARGET_TAG_SUFFIX=${TARGET_TAG_SUFFIX:-""}

export PROJECT_NAME=${PROJECT_NAME:-"<empty>"}
export PROJECT_TAG=${PROJECT_TAG:-"<empty>"}
export PROJECT_TAG_ALIASES=${PROJECT_TAG_ALIASES:-""}

#
# Builds the builder image
#

build_builder() {

    docker build --tag "${TARGET_IMAGE_BUILDER}:${PROJECT_TAG}${TARGET_TAG_SUFFIX}" ${DOCKER_PROJECTS}/builder
    if [ "$?" != "0" ]; then
        echo "Failed to build image"
        exit 1
    fi

    docker push "${TARGET_IMAGE_BUILDER}:${PROJECT_TAG}${TARGET_TAG_SUFFIX}"

    for tag_alias in ${PROJECT_TAG_ALIASES}
    do
        docker tag "${TARGET_IMAGE_BUILDER}:${PROJECT_TAG}${TARGET_TAG_SUFFIX}" "${TARGET_IMAGE_BUILDER}:${tag_alias}${TARGET_TAG_SUFFIX}"
        docker push "${TARGET_IMAGE_BUILDER}:${tag_alias}${TARGET_TAG_SUFFIX}"
    done

}

#
# Builds the api image
#

build_api() {

    docker build --tag "${TARGET_IMAGE_API}:${PROJECT_TAG}${TARGET_TAG_SUFFIX}" \
        --build-arg "BUILDER_IMAGE=${BUILDER_IMAGE}" \
        --build-arg "API_VERSION=${PROJECT_TAG}" \
        ${DOCKER_PROJECTS}/api

    if [ "$?" != "0" ]; then
        echo "Failed to build image"
        exit 1
    fi

    docker push "${TARGET_IMAGE_API}:${PROJECT_TAG}${TARGET_TAG_SUFFIX}"

    for tag_alias in ${PROJECT_TAG_ALIASES}
    do
        docker tag "${TARGET_IMAGE_API}:${PROJECT_TAG}${TARGET_TAG_SUFFIX}" "${TARGET_IMAGE_API}:${tag_alias}${TARGET_TAG_SUFFIX}"
        docker push "${TARGET_IMAGE_API}:${tag_alias}${TARGET_TAG_SUFFIX}"
    done

}

#
# Builds the app image
#

build_app() {

    docker build --tag "${TARGET_IMAGE_APP}:${PROJECT_TAG}${TARGET_TAG_SUFFIX}" \
        --build-arg "BUILDER_IMAGE=${BUILDER_IMAGE}" \
        --build-arg "APP_VERSION=${PROJECT_TAG}" \
        ${DOCKER_PROJECTS}/app

    if [ "$?" != "0" ]; then
        echo "Failed to build image"
        exit 1
    fi

    docker push "${TARGET_IMAGE_APP}:${PROJECT_TAG}${TARGET_TAG_SUFFIX}"

    for tag_alias in ${PROJECT_TAG_ALIASES}
    do
        docker tag "${TARGET_IMAGE_APP}:${PROJECT_TAG}${TARGET_TAG_SUFFIX}" "${TARGET_IMAGE_APP}:${tag_alias}${TARGET_TAG_SUFFIX}"
        docker push "${TARGET_IMAGE_APP}:${tag_alias}${TARGET_TAG_SUFFIX}"
    done

}

#
# Entrypoint
#

if [ "${PROJECT_NAME}" == "<empty>" ]; then
    echo "Missing PROJECT_NAME environment variable"
    exit 1
fi

if [ "${PROJECT_TAG}" == "<empty>" ]; then
    echo "Missing PROJECT_TAG environment variable"
    exit 1
fi

echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USERNAME" --password-stdin

case $PROJECT_NAME in
    "api" | "app" | "builder")
        build_$PROJECT_NAME
        ;;
    *)
        echo "Unknown project name '${PROJECT_NAME}'"^
        exit 1
esac

