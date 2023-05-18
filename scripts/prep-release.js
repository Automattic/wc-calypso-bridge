import chalk from 'chalk';

import {
	NOTICE_LEVEL,
	abortAndSwitchToBranch,
	checkBinaryExists,
	getNvmrcVersion,
	error,
	info,
	isCorrectNodeVersion,
	promptContinue,
} from './utils.js';

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
}

main();
