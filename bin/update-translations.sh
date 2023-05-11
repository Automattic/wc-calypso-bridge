#!/usr/bin/env bash

cd $(dirname "$(dirname "$0")")

curl -Ls https://translate.wordpress.com/api/projects/woocommerce/wc-calypso-bridge | jq -c '.translation_sets[] | select(.percent_translated >= 85)' | while IFS= read -r lang; do
	LANG_NAME=$(echo $lang | jq -r '.name')
	LANG_LOCALE=$(echo $lang | jq -r '.locale')
	LANG_SLUG=$(echo $lang | jq -r '.slug')

	LANG_WP_LOCALE=$(echo $lang | jq -r '.wp_locale')

	if [[ "$LANG_WP_LOCALE" == "null" ]]; then
		LANG_WP_LOCALE=${LANG_LOCALE}
	fi

	if [[ "$LANG_WP_LOCALE" == "de_DE" ]] && [[ "$LANG_SLUG" == "formal" ]]; then
		LANG_WP_LOCALE="de_DE_formal"
	fi

	LANG_FILENAME=languages/wc-calypso-bridge-$LANG_WP_LOCALE

	echo "Downloading $LANG_NAME ($LANG_LOCALE/$LANG_SLUG → $LANG_WP_LOCALE)"
	curl -so $LANG_FILENAME.po https://translate.wordpress.com/projects/woocommerce/wc-calypso-bridge/$LANG_LOCALE/$LANG_SLUG/export-translations/?format=po
	[[ -e $LANG_FILENAME.po ]] && msgfmt $LANG_FILENAME.po -o $LANG_FILENAME.mo
done
