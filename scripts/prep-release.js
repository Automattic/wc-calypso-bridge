import chalk from 'chalk';

import {
	NOTICE_LEVEL,
	abortAndSwitchToBranch,
	checkBinaryExists,
	createPullRequest,
	getNvmrcVersion,
	error,
	info,
	success,
	isCorrectNodeVersion,
	promptContinue,
} from './utils.js';

import bumpVersion from './commands/bump-version.js';
import updateReadMe from './commands/readme.js';
import updateTranslations from './commands/update-translations.js';

async function main() {
	if ( ! isCorrectNodeVersion() ) {
		return abortAndSwitchToBranch(
			`Your version of NodeJS is not correct. Please install NodeJS v${ getNvmrcVersion() }.`,
			NOTICE_LEVEL.ERROR
		);
	}

	if ( ! checkBinaryExists( 'gh' ) ) {
		error(
			`The Github CLI (gh) is not installed. 
			
Please install it from https://cli.github.com/ or using 'brew install gh' if you're on a Mac.`
		);
		process.exit( 1 );
	}

	info( `This command will prepare a new wc-calypso-bridge release with the following steps:

1. Bump the version number in the composer.json file and the wc-calypso-bridge.php file.
2. Update the changelog in readme.txt.
3. Update the translation files (optional).
4. Create a new PR with the changes.
	` );

	if ( ! ( await promptContinue( 'Continue? (y/N)' ) ) ) {
		process.exit( 1 );
	}

	if ( ! ( await bumpVersion() ) ) {
		error(
			'Aborting, something went wrong while bumping the version number.'
		);
		process.exit( 1 );
	}

	if ( ! ( await updateReadMe() ) ) {
		error( 'Aborting, something went wrong while updating the readme.' );
		process.exit( 1 );
	}

	if (
		await promptContinue(
			'Would you like to update the translation files? (y/N)'
		)
	) {
		await updateTranslations();
	}

	if (
		! ( await promptContinue(
			'Would you like to update the translation files? (y/N)'
		) )
	) {
		error( 'Pull request creationg was cancelled.' );
		process.exit( 1 );
	}

	// TODO - Make this a nicer message with a link to the PR.
	success( 'All done!' );
}

main();
