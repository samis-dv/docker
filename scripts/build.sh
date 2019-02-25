#!/bin/bash

API_VERSION=${API_VERSION:-""}
APP_VERSION=${API_VERSION:-""}
DOCKER_TAGS=${DOCKER_TAGS:-""}

# Check API version
if [ "~${API_VERSION}" == "~" ]; then
  echo "No API version specified"
  exit 0
fi

# Check APP version
if [ "~${APP_VERSION}" == "~" ]; then
  echo "No APP version specified"
  exit 0
fi

# Check docker tags
if [ "~${DOCKER_TAGS}" == "~" ]; then
  echo "WARNING: No Docker tags specified. No images will be pushed."
else
  echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USERNAME" --password-stdin
fi

echo "Build script started"

echo "<WIP>"

echo "Build script finished"
