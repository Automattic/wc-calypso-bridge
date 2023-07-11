import {
	getCurrentVersion,
	promptChangeDegree,
	updateWCCalypsoBridgeVersion,
	updateComposerJsonVersion,
	error,
	isCorrectNodeVersion,
	promptContinue,
	createNewBranch,
	createNewCommit,
} from '../utils.js';
import { inc as incVersion } from 'semver';

/**
 * Bumps the version number in the composer.json file and the wc-calypso-bridge.php file.
 */
export default async function bumpVersion() {
	if ( ! isCorrectNodeVersion() ) {
		error(
			`Your version of NodeJS is not correct. Please install NodeJS v${ getNvmrcVersion() }.`
		);
		return;
	}

	const degree = await promptChangeDegree();

	const currentVersion = getCurrentVersion().replace( 'v', '' );
	const newVersion = incVersion( currentVersion, degree );

	const shouldContinue = await promptContinue(
		`Are you sure you want to bump the version from ${ currentVersion } to ${ newVersion }?`
	);

	if ( ! shouldContinue ) {
		return error( 'Aborting version bump.' );
	}

	if ( ! ( await createNewBranch( `update-version-${ newVersion }` ) ) ) {
		return error( 'Aborting version bump.' );
	}

	updateComposerJsonVersion( `v${ newVersion }` );
	updateWCCalypsoBridgeVersion( newVersion );

	if ( ! ( await createNewCommit( `Bump version to ${ newVersion }` ) ) ) {
		return error( 'Aborting version bump.' );
	}

	return true;
}
