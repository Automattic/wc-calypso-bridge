#!/usr/bin/env bash
CHANGED_STAGED_PHP_FILES=$(git diff --staged --name-only --diff-filter=d | grep '\.php$' | xargs)
if [ -z "$CHANGED_STAGED_PHP_FILES" ]; then
	echo "No modified PHP files are staged. Exiting."
	exit 0
fi
vendor/bin/phpcs-changed --warning-severity=0 --extensions=php --git --git-staged $CHANGED_STAGED_PHP_FILES
