import fs from 'fs';
import chalk from 'chalk';

import {
	__dirname,
	info,
	NOTICE_LEVEL,
	getCurrentVersion,
	gitFactory,
	promptContinue,
	switchToBranchWithMessage,
	abortAndSwitchToBranch,
	tagExists,
	verifyBuild,
} from '../utils.js';

async function tagRelease( currentBranchName ) {
	// Ensure we're always running in the project root.
	process.chdir( `${ __dirname }/..` );

	const verificationErrors = await verifyBuild();
	if ( verificationErrors.length > 0 ) {
		const errorDetails = verificationErrors.join( `\t` );

		return abortAndSwitchToBranch(
			`Build verification failed:\n${ errorDetails }`,
			NOTICE_LEVEL.ERROR,
			currentBranchName
		);
	}

	if ( fs.existsSync( 'node_modules' ) ) {
		return abortAndSwitchToBranch(
			'A node_modules folder exists. Please remove it and try again.',
			NOTICE_LEVEL.ERROR,
			currentBranchName
		);
	}

	let res = null;
	const version = getCurrentVersion();
	const versionStr = `${ chalk.blue( version ) }`;

	res = await tagExists( version );
	if ( res ) {
		return abortAndSwitchToBranch(
			`The tag ${ version } already exists. Deploy failed.`,
			NOTICE_LEVEL.ERROR,
			currentBranchName
		);
	}

	const git = gitFactory();

	await git.tag( [ version ] );
	await git.checkout( version );
	await git.add( [ './build', '--force' ] );
	await git.commit( 'Adding build directory to release', null, {
		'--no-verify': null,
	} );
	await git.tag( [ '-f', version ] );
	info( `We've created a tag for release ${ versionStr }.` );

	const shouldContinue = await promptContinue(
		`Would you like to publish the release for ${ versionStr }?`
	);

	if ( ! shouldContinue ) {
		await switchToBranchWithMessage( currentBranchName );

		// Delete the tag we just created.
		await git.tag( [ '-d', version ] );

		return abortAndSwitchToBranch(
			`Aborting. The release for ${ versionStr } was not published and the new tag has been deleted!`,
			NOTICE_LEVEL.WARNING
		);
	}

	res = await tagExists( version );
	if ( ! res ) {
		return abortAndSwitchToBranch(
			`The tag ${ version } does not exist. Deploy failed.`,
			NOTICE_LEVEL.ERROR,
			currentBranchName
		);
	}

	try {
		await git.push( [ 'origin', version ] );
	} catch ( err ) {
		return abortAndSwitchToBranch(
			`The tag ${ version } was not deployed. ${ err }`,
			NOTICE_LEVEL.ERROR,
			currentBranchName
		);
	}

	return true;
}

export default tagRelease;
