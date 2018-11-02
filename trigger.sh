#!/bin/bash

#
# Usage:
#
#   export WEBHOOK_URL=https://website.com/webhook
#   export WEBHOOK_SECRET=somesecret
#   ./trigger.sh <user/repo> <release>
#
# Examples:
#
#   export WEBHOOK_URL=https://directus.io/webhook
#   export WEBHOOK_SECRET=webhook
#   ./trigger.sh directus/api 2.0.4
#
#   ~or~
#
#   WEBHOOK_URL=https://directus.io/webhook WEBHOOK_SECRET=webhook ./trigger.sh directus/api 2.0.4
#

if [ -f ./.env ]; then
    source .env
fi

WEBHOOK_URL=${WEBHOOK_URL:-""}
WEBHOOK_SECRET=${WEBHOOK_SECRET:-""}

if [ "${WEBHOOK_URL}" == "" ]; then
    echo "Undefined WEBHOOK_URL environment variable"
    exit 1
fi

PAYLOAD="{\"repository\":{\"full_name\":\"${1}\"},\"release\":{\"tag_name\":\"${2}\",\"draft\":false}}"
HASH=$(echo -n ${PAYLOAD} | openssl dgst -sha1 -hmac "${WEBHOOK_SECRET}" | awk '{print $2}')

curl --verbose \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --header "X-Github-Event: release" \
    --header "X-Github-Delivery: 50bbd230-dd3d-11e8-817e-8a75e529f55c" \
    --header "X-Hub-Signature: sha1=${HASH}" \
    --data ${PAYLOAD} \
    ${WEBHOOK_URL}
