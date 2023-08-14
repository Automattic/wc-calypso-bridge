#!/usr/bin/env bash
DEFAULT_BRANCH=$(git symbolic-ref refs/remotes/origin/HEAD | sed 's@^refs/remotes/origin/@@')
INITIAL_COMMIT_FOR_BRANCH=$(git merge-base origin/$DEFAULT_BRANCH HEAD)
CHANGED_PHP_FILES=$(git diff --name-only --diff-filter=d $INITIAL_COMMIT_FOR_BRANCH | grep '\.php$' | xargs)
if [ -z "$CHANGED_PHP_FILES" ]; then
	echo "No PHP files changed. Exiting."
	exit 0
fi
vendor/bin/phpcs-changed --git $CHANGED_PHP_FILES
