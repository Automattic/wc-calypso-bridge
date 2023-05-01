import { exec } from 'child_process';
import { promisify } from 'util';
import { promises as fsPromises } from 'fs';

import {
	__dirname,
	error,
	success,
	info,
	warning,
	getCurrentBranchName,
	getCurrentVersion,
	getStatus,
	gitFactory,
	isDevBuild,
	promptContinue,
	switchToBranchWithMessage,
	verifyBuild,
} from '../utils.js';

const execAsync = promisify( exec );

async function buildRelease() {
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

	// TODO - Verify (somehow) that we're not running with `start`.
	/*
	Some possible ideas for checking for `start`:

	- Check for `build/*.map` files.
	- The real build has a 948.js (not sure if this number changes) and start doesn't.
	*/
	if ( isDevBuild() ) {
		error(
			"You may have a dev build. Please make sure you aren't running 'npm start'"
		);
		return false;
	}

	const git = gitFactory();
	const currentBranchName = await getCurrentBranchName();

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
		info( 'Aborting release build.' );
		await switchToBranchWithMessage( currentBranchName );

		return false;
	}

	await execAsync( 'npm i' );
	const { stdout, stderr } = await execAsync( 'npm run build' );

	// Verify that the build was successful.
	// We do this by getting the last line of the build output (which is the webpack status line) and checking it for errors.
	let buildSuccess = false;
	const lines = stdout.split( '\n' ).filter( ( line ) => line.length > 0 );
	const statusLine = lines.pop();
	if ( statusLine.match( /webpack\s+.*?compiled with/ ) ) {
		buildSuccess = ! statusLine.includes( 'error' );
	}

	if ( ! buildSuccess ) {
		error( `npm run build failed (${ statusLine })` );
		error( stderr );
		return false;
	}

	// Verify that the build is valid.
	const verificationErrors = await verifyBuild();
	if ( verificationErrors.length > 0 ) {
		error( 'Build verification failed:' );
		verificationErrors.map( ( err ) => error( `\t${ err }` ) );
		return false;
	}

	// Delete the node_modules directory so they don't get packaged up.
	await fsPromises.rm( 'node_modules', { recursive: true } );

	success( 'Release build complete.' );
	return true;
}

export default buildRelease;
