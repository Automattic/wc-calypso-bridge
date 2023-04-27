import chalk from 'chalk';

import buildRelease from './commands/build.js';
import {
	error,
	info,
	isCorrectNodeVersion,
	getCurrentVersion,
	getNvmrcVersion,
	promptContinue,
} from './utils.js';

async function main() {
	if ( ! isCorrectNodeVersion() ) {
		error(
			`Your version of NodeJS is not correct. Please install NodeJS v${ getNvmrcVersion() }.`
		);
		return;
	}

	const version = getCurrentVersion();
	let res = null;

	const shouldContinue = await promptContinue(
		`The current version is ${ chalk.blue(
			version
		) }. Would you like to create a new release?`
	);

	if ( ! shouldContinue ) {
		info( 'Aborting release build.' );
		return;
	}

	res = await buildRelease();
	if ( ! res ) {
		return;
	}
}

main();
