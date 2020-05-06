#!/usr/bin/env bash
# usage: travis.sh before|after

PLUGIN_SLUG=`echo $TRAVIS_REPO_SLUG | cut -f2 -d/`

if [ "$1" == 'before' ]; then
	cd "$WP_CORE_DIR/wp-content/plugins/$PLUGIN_SLUG/"
	if [[ "$COMPOSER_DEV" == "1" ]]; then
		composer install
	else
		composer install --no-dev
	fi
fi
