#!/bin/bash

# Check if we have the code
if [ ! -d /var/www/public ]; then
  echo "Application files not found."
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
