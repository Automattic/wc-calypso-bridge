#!/usr/bin/env bash
REQUIRED_VERSION=$1
echo -e "${BLUE}Checking for version: $REQUIRED_VERSION${NC}"
PACKAGE_BASE=$(echo "$GITHUB_REPOSITORY" | awk -F'/' '{print $2}' )

# HELPERS.
BLUE='\033[0;32m'
NC='\033[0m' # No Color
UNDERLINE_START='\e[4m'
UNDERLINE_STOP='\e[0m'

# GET BASE PHP VERSIONS.
PHP_DOCKBLOCK_VERSION=$( awk '/\* *Version/ {print}' $PACKAGE_BASE.php | sed 's/[^0-9.]*\([0-9.]*\).*/\1/' )
PHP_CURRENT_VERSION=$( awk '/WC_CALYPSO_BRIDGE_CURRENT_VERSION\047, \047/ {print}' $PACKAGE_BASE.php | sed 's/[^0-9.]*\([0-9.]*\).*/\1/' )
if [[ $PHP_DOCKBLOCK_VERSION != $REQUIRED_VERSION ]]; then
	echo "Wrong version in the PHP main file... Exiting with error."
	exit 1
fi

if [[ $PHP_DOCKBLOCK_VERSION != $PHP_CURRENT_VERSION ]]; then
	echo "Different Versions in the main PHP file... Exiting with error."
	exit 1
else
	echo -e "${BLUE}- Main PHP file versions: OK${NC}"
fi

COMPOSER_VERSION=$(awk -F'"' '/version/{print $4; exit}' composer.json | sed 's/^v\?\(.*\)/\1/' | sed 's/[^0-9.]*\([0-9.]*\).*/\1/')
if [[ $COMPOSER_VERSION != $PHP_CURRENT_VERSION ]]; then
	echo "composer.json version does not match with main file... Exiting with error."
	exit 1
else
	echo -e "${BLUE}- composer.json version: OK${NC}"
fi

CHANGELOG_EXIST=$( awk "/= $PHP_CURRENT_VERSION =/" readme.txt )
if [[ -z $CHANGELOG_EXIST ]]; then
	echo "No changelog entry found... Exiting with error."
	exit 1
else
	echo -e "${BLUE}- Changelog version: OK${NC}"
fi

echo -e "${UNDERLINE_START}File versions checked. Moving on...${UNDERLINE_STOP}"
exit 0
