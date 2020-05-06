#!/usr/bin/env bash

if [[ ${RUN_PHPCS} == 1 ]]; then
	CHANGED_FILES=`git diff --name-only --diff-filter=ACMR $TRAVIS_COMMIT_RANGE | grep \\\\.php | awk '{print}' ORS=' '`

	if [ "$CHANGED_FILES" != "" ]; then
		echo "Running Code Sniffer."
		PLUGIN_SLUG=`echo $TRAVIS_REPO_SLUG | cut -f2 -d/`
		cd "$WP_CORE_DIR/wp-content/plugins/$PLUGIN_SLUG/"
		./vendor/bin/phpcs --encoding=utf-8 -n -p $CHANGED_FILES
	fi
fi
