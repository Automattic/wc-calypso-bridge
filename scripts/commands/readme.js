import fs from 'fs';

import {
	__dirname,
	info,
	success,
	NOTICE_LEVEL,
	getCurrentBranchName,
	getCurrentVersion,
	promptContinue,
	abortAndSwitchToBranch,
	updateChangelog,
	openEditorAndGetText,
	gitFactory,
} from '../utils.js';

export default async function updateReadMe() {
	// Ensure we're always running in the project root.
	process.chdir( `${ __dirname }/..` );

	const currentBranchName = await getCurrentBranchName();

	if ( ! fs.existsSync( 'readme.txt' ) ) {
		return abortAndSwitchToBranch(
			'The file readme.txt does not exist. Verify you are in the wc-calypso-bridge project directory.',
			NOTICE_LEVEL.ERROR,
			currentBranchName
		);
	}

	info(
		"Now we'll open your default editor so you can update the changelog."
	);

	if ( ! ( await promptContinue( 'Continue?' ) ) ) {
		process.exit( 1 );
	}

	const version = getCurrentVersion();
	let changelogEntry = `= ${ version } =\n`;

	changelogEntry += openEditorAndGetText();

	const shouldContinue = await promptContinue(
		'Would you like to update readme.txt?'
	);
	if ( ! shouldContinue ) {
		return abortAndSwitchToBranch(
			'Aborting readme update.',
			NOTICE_LEVEL.INFO,
			currentBranchName
		);
	}

	// Update the changelog and commit the change.
	await updateChangelog( changelogEntry );

	const git = gitFactory();

	await git.add( [ './readme.txt' ] );
	await git.commit( `Added version ${ version } to the changelog` );

	success( 'Readme updated successfully.' );
	return changelogEntry;
}
