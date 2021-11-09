#!/usr/bin/env bash
source "$(dirname "$0")/common.sh"

log "\e[36m--------- Performing clean install ---------\e[39m"

log "Go to the project root."
cd "$(cd -P -- "$(dirname -- "$0")" && pwd -P)/.." || exit 1;

log "Installing drupal composer dependencies."
composer install --no-interaction --no-progress --no-suggest "$@" || exit 1;

log "Go to the drupal root."
cd web || exit 1;

DRUSH="../vendor/bin/drush -y"

log "Install drupal."
# Add --locale=nl to install in dutch.
${DRUSH} site-install config_installer --locale=en --account-name=root --account-pass=eskerebeskere --account-mail=gabor@cleverhoods.com --db-url=mysql://drupal8:drupal8@database:3306/drupal8 || exit 1

log "Set default directory permission."
chmod 755 /app/web/sites/default || exit 1;

log "\e[36m========= End of clean install =========\e[39m"
