import chalk from 'chalk';

import {
	NOTICE_LEVEL,
	abortAndSwitchToBranch,
	checkBinaryExists,
	createPullRequest,
	getCurrentVersion,
	getNvmrcVersion,
	gitFactory,
	error,
	info,
	success,
	isCorrectNodeVersion,
	openPullRequest,
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

	if ( ! ( await promptContinue( 'Continue?' ) ) ) {
		process.exit( 1 );
	}

	if ( ! ( await bumpVersion() ) ) {
		error(
			'Aborting, something went wrong while bumping the version number.'
		);
		process.exit( 1 );
	}

	const changelogEntry = await updateReadMe();
	if ( changelogEntry === false ) {
		error( 'Aborting, something went wrong while updating the readme.' );
		process.exit( 1 );
	}

	if (
		await promptContinue(
			'Would you like to update the translation files?'
		)
	) {
		await updateTranslations();
	}

	if (
		! ( await promptContinue( 'Would you like to create a pull request?' ) )
	) {
		error( 'Pull request creationg was cancelled.' );
		process.exit( 1 );
	}

	// Push our release prep branch
	const git = gitFactory();
	await git.push( 'origin', git.branch() );

	const currentVersion = getCurrentVersion();
	const title = `Prepare for release ${ currentVersion }`;
	const body = `### Changes proposed in this Pull Request:

Preparation for release of ${ currentVersion }

### Changelog

\`\`\`
= ${ currentVersion } =
${ changelogEntry }
\`\`\``;

	const output = createPullRequest( title, body, [
		'[Status] Needs Review',
		'[Type] Release',
	] );

	const pattern =
		/^https:\/\/github\.com\/Automattic\/wc-calypso-bridge\/pull\/\d+$/;

	if ( ! pattern.test( output ) ) {
		error(
			`Something went wrong while creating the pull request.\n${ output }`
		);
		process.exit( 1 );
	}

	info(
		`Pull request created: ${ output }\n\nYou will need to manually assign reviewers.`
	);

	if (
		await promptContinue(
			'Would you like to open the pull request in your browser?'
		)
	) {
		openPullRequest();
	}

	success( 'Done!' );
}

main();
