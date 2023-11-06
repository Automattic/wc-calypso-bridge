import { exec } from 'child_process';
import { promisify } from 'util';
import { promises as fsPromises } from 'fs';

import {
	__dirname,
	error,
	success,
	info,
	NOTICE_LEVEL,
	getCurrentVersion,
	getStatus,
	gitFactory,
	isDevBuild,
	promptContinue,
	abortAndSwitchToBranch,
	verifyBuild,
} from '../utils.js';

const execAsync = promisify( exec );

async function buildRelease( currentBranchName ) {
	// Ensure we're always running in the project root.
	process.chdir( `${ __dirname }/..` );

	// Do a new release build

	// Check to see if the working copy is clean
	const status = await getStatus();
	if ( ! status.isClean() ) {
		error(
			'Working copy is not clean. Please commit or stash your changes before releasing.'
		);
		return false;
	}

	if ( isDevBuild() ) {
		return abortAndSwitchToBranch(
			"You may have a dev build. Please make sure you aren't running 'npm start'. You may need to delete the existing build directory by running `rm -rf ./build`.",
			NOTICE_LEVEL.ERROR,
			currentBranchName
		);
	}

	const git = gitFactory();

	await git.checkout( 'master' );
	await git.pull( [ 'origin', 'master', '--rebase' ] );

	const version = getCurrentVersion();
	info(
		`Pulled latest changes from 'master'. Current version is ${ version }.`
	);

	const shouldContinue = await promptContinue(
		'Would you like to create a new release?'
	);

	if ( ! shouldContinue ) {
		return abortAndSwitchToBranch(
			'Aborting release build.',
			NOTICE_LEVEL.INFO,
			currentBranchName
		);
	}

	info( 'Creating a new release build. This may take some time...' );
	const interval = setInterval( () => process.stdout.write( '.' ), 1000 );

	await execAsync( 'npm ci' );
	const { stdout } = await execAsync( 'npm run build' );

	clearInterval( interval );

	// Verify that the build was successful.
	// We do this by getting the last line of the build output (which is the webpack status line) and checking it for errors.
	let buildSuccess = false;
	const lines = stdout.split( '\n' ).filter( ( line ) => line.length > 0 );
	const statusLine = lines.pop();
	if ( statusLine.match( /webpack\s+.*?compiled with/ ) ) {
		buildSuccess = ! statusLine.includes( 'error' );
	}

	if ( ! buildSuccess ) {
		return abortAndSwitchToBranch(
			`npm run build failed (${ statusLine })`,
			NOTICE_LEVEL.ERROR,
			currentBranchName
		);
	}

	// Verify that the build is valid.
	const verificationErrors = await verifyBuild();
	if ( verificationErrors.length > 0 ) {
		const errorDetails = verificationErrors.join( `\t` );

		return abortAndSwitchToBranch(
			`Build verification failed:\n${ errorDetails }`,
			NOTICE_LEVEL.ERROR,
			currentBranchName
		);
	}

	// Delete the node_modules directory so they don't get packaged up.
	await fsPromises.rm( 'node_modules', { recursive: true } );
	info(
		"Deleted node_modules directory. Please note that you'll need to run 'npm install' again."
	);

	success( 'Release build complete.' );
	return true;
}

export default buildRelease;
