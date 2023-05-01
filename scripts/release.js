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
	const shouldContinue = null;

	res = await buildRelease();
	if ( ! res ) {
	}
}

main();
