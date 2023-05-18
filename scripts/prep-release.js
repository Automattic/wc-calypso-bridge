import chalk from 'chalk';

import {
	NOTICE_LEVEL,
	abortAndSwitchToBranch,
	getNvmrcVersion,
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
}

main();
