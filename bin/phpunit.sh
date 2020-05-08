#!/usr/bin/env bash
WORKING_DIR="$PWD"
PLUGIN_SLUG=`echo $TRAVIS_REPO_SLUG | cut -f2 -d/`

cd "$WP_CORE_DIR/wp-content/plugins/$PLUGIN_SLUG/"
if [[ {$COMPOSER_DEV} == 1 ]]; then
	./vendor/bin/phpunit --version
	./vendor/bin/phpunit -c phpunit.xml
else
	phpunit --version
	phpunit -c phpunit.xml
fi
TEST_RESULT=$?
cd "$WORKING_DIR"
exit $TEST_RESULT
