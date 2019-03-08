#!/bin/bash

#
# Generic build script
#
build_image() {
  PROJECT=$1
  RELEASE=$2
  FLAVOUR=$3
  IMAGE=$4
  TAG=$5
  PUSH=${6:-"0"}

  echo "=================================================================="
  echo " (${FLAVOUR}) ${IMAGE}:${TAG}-${FLAVOUR} "
  echo "=================================================================="

  docker build -t "${IMAGE}:${TAG}-${FLAVOUR}" --build-arg "RELEASE=${RELEASE}" docker/${FLAVOUR}/${PROJECT}

  if [ "${PUSH}" != "0" ]; then
    # Push image
    docker push "${IMAGE}:${TAG}-${FLAVOUR}"

    # Apache is the default supported flavour
    if [ "${FLAVOUR}" == "apache" ]; then
      docker tag "${IMAGE}:${TAG}-${FLAVOUR}" "${IMAGE}:${TAG}"
      docker push "${IMAGE}:${TAG}"
    fi

    # Push latest if requested
    if [ "${PUSH}" == "latest" ]; then
      docker tag "${IMAGE}:${TAG}-${FLAVOUR}" "${IMAGE}:latest"
      docker push "${IMAGE}:latest"
    fi
  fi
}

#
# Main
#
main() {

  # Variables
  export PROJECT_NAME=${PROJECT_NAME:-""}
  export PROJECT_RELEASE=${PROJECT_RELEASE:-""}
  export PROJECT_IMAGE=${PROJECT_IMAGE:-"directus/${PROJECT_NAME}"}
  export PROJECT_TAG=${PROJECT_TAG:-"?"}
  export PROJECT_FLAVOUR=${PROJECT_FLAVOUR:-"apache"}
  export PROJECT_PUSH=${PROJECT_PUSH:-"0"}

  # Normalize tag
  if [ "${PROJECT_TAG}" == "?" ]; then
    if [ "${PROJECT_RELEASE:0:1}" == "v" ]; then
      export PROJECT_TAG="${PROJECT_RELEASE:1}"
    else
      export PROJECT_TAG="${PROJECT_RELEASE}"
    fi
  fi

  # Check project name
  if [ "~${PROJECT_NAME}" == "~" ]; then
    echo "No project specified"
    exit 0
  fi

  # Check project release
  if [ "~${PROJECT_RELEASE}" == "~" ]; then
    echo "No release specified"
    exit 0
  fi

  # Check docker tags
  if [ "~${DOCKER_PASSWORD}" != "~" ]; then
    echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USERNAME" --password-stdin
  fi

  if [[ "app api directus" =~ (^|[[:space:]])${PROJECT_NAME}($|[[:space:]]) ]]; then
    build_image "${PROJECT_NAME}" "${PROJECT_RELEASE}" "${PROJECT_FLAVOUR}" "${PROJECT_IMAGE}" "${PROJECT_TAG}" "${PROJECT_PUSH}"
  else
    echo "Unsupported project ${PROJECT_NAME}."
    exit 1
  fi
}

main
