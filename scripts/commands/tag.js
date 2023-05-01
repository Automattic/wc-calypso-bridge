import fs from 'fs';
import chalk from 'chalk';

import {
	__dirname,
	error,
	info,
	getCurrentVersion,
	getCurrentBranchName,
	gitFactory,
	promptContinue,
	switchToBranchWithMessage,
	tagExists,
	verifyBuild,
	warning,
} from '../utils.js';

async function tagRelease() {
	// Ensure we're always running in the project root.
	process.chdir( `${ __dirname }/..` );

	const verificationErrors = await verifyBuild();
	if ( verificationErrors.length > 0 ) {
		error( 'Build verification failed:' );
		verificationErrors.map( ( err ) => error( `\t${ err }` ) );
		return false;
	}

	if ( fs.existsSync( 'node_modules' ) ) {
		error(
			'A node_modules folder exists. Please remove it and try again.'
		);
		return false;
	}

	let res = null;
	const git = gitFactory();
	const currentBranchName = await getCurrentBranchName();
	const version = getCurrentVersion();
	const versionStr = `${ chalk.blue( version ) }`;

	res = await tagExists( version );
	if ( res ) {
		error( `The tag ${ version } already exists. Deploy failed.` );
		await switchToBranchWithMessage( currentBranchName );
		return false;
	}

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
		warning(
			`Aborting. The release for ${ versionStr } was not published!`
		);
		// TODO - Should we delete the tag here?
		return false;
	}

	res = await tagExists( version );
	if ( ! res ) {
		error( `The tag ${ version } does not exist. Deploy failed.` );
		await switchToBranchWithMessage( currentBranchName );
		return false;
	}

	await git.push( [ 'origin', version ] );

	return true;
}

export default tagRelease;
