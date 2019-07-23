#!/bin/bash

# Check and install dependencies
install-dependencies

# Workaround slow chmod
GROUP=$(stat -c '%G:%U' /var/www/public)
if [ "$GROUP" != "www-data:www-data" ]; then
  echo "Updating ownership..."
  chown -R www-data:www-data /var/www
fi

# Forward commands
exec /usr/local/bin/docker-php-entrypoint $@
