import chalk from 'chalk';

import buildRelease from './commands/build.js';
import tagRelease from './commands/tag.js';
import {
	NOTICE_LEVEL,
	isCorrectNodeVersion,
	getCurrentBranchName,
	getCurrentVersion,
	getNvmrcVersion,
	promptContinue,
	success,
	switchToBranchWithMessage,
	abortAndSwitchToBranch,
} from './utils.js';

async function main() {
	if ( ! isCorrectNodeVersion() ) {
		return abortAndSwitchToBranch(
			`Your version of NodeJS is not correct. Please install NodeJS v${ getNvmrcVersion() }.`,
			NOTICE_LEVEL.ERROR
		);
	}

	const currentBranchName = await getCurrentBranchName();
	let res = null;
	let shouldContinue = null;

	res = await buildRelease( currentBranchName );
	if ( ! res ) {
		return;
	}

	const version = getCurrentVersion();

	shouldContinue = await promptContinue(
		`We've built the release for ${ chalk.blue(
			version
		) }. Would you like to tag the release and deploy? You will be prompted one more time before the release is published.`
	);

	if ( ! shouldContinue ) {
		return abortAndSwitchToBranch(
			'Aborting release deploy.',
			NOTICE_LEVEL.INFO,
			currentBranchName
		);
	}

	res = await tagRelease( currentBranchName );
	if ( ! res ) {
		return;
	}

	await switchToBranchWithMessage( currentBranchName );
	success( 'Release complete!' );
}

main();
