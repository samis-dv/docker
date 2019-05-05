#!/bin/bash

# Check if we have the code
if [ ! -d /var/www/public ]; then
  echo "Application files are not mounted."
  exit 1
fi

# Make life easier when mounting code
if [ -f /var/www/composer.json ]; then
  if [ ! -d /var/www/vendor ]; then
    pushd /var/www > /dev/null
    composer install
    popd > /dev/null
  fi
fi

# Workaround slow chmod
GROUP=$(stat -c '%G:%U' /var/www/public)
if [ "$GROUP" != "www-data:www-data" ]; then
  echo "Updating ownership..."
  chown -R www-data:www-data /var/www
fi

# Forward commands
exec /usr/local/bin/docker-php-entrypoint $@
