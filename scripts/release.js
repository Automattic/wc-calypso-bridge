import chalk from 'chalk';

import buildRelease from './commands/build.js';
import tagRelease from './commands/tag.js';
import {
	error,
	info,
	isCorrectNodeVersion,
	getCurrentBranchName,
	getCurrentVersion,
	getNvmrcVersion,
	gitFactory,
	promptContinue,
	success,
	switchToBranchWithMessage,
} from './utils.js';

async function main() {
	if ( ! isCorrectNodeVersion() ) {
		error(
			`Your version of NodeJS is not correct. Please install NodeJS v${ getNvmrcVersion() }.`
		);
		return;
	}

	const currentBranchName = await getCurrentBranchName();
	let res = null;
	let shouldContinue = null;

	res = await buildRelease();
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
		info( 'Aborting release deploy.' );
		await switchToBranchWithMessage( currentBranchName );

		return;
	}

	res = await tagRelease();
	if ( ! res ) {
		return;
	}

	const git = gitFactory();

	await git.checkout( 'master' );
	success( 'Release complete!' );
}

main();
