import {
    getCurrentVersion,
    promptChangeDegree,
    updateWCCalypsoBridgeVersion,
    updateComposerJsonVersion,
    error,
    isCorrectNodeVersion,
    promptContinue,
} from "../utils.js";
import { inc as incVersion } from "semver";

/**
 * Bumps the version number in the composer.json file and the wc-calypso-bridge.php file.
 */
async function bumpVersion() {
	if ( ! isCorrectNodeVersion() ) {
		error(
			`Your version of NodeJS is not correct. Please install NodeJS v${ getNvmrcVersion() }.`
		);
		return;
	}

    const degree = await promptChangeDegree();

    const currentVersion = getCurrentVersion().replace('v', '');
    let newVersion = null;
    switch (degree) {
        case 'Patch (bug fixes)':
            newVersion = incVersion(currentVersion, 'patch');
            break;
        case 'Minor (new features, backwards compatible)':
            newVersion = incVersion(currentVersion, 'minor');
            break;
        case 'Major (breaking changes)':
            newVersion = incVersion(currentVersion, 'major');
            break;
        default:
            throw new Error(`Invalid degree of change: ${degree}`);
    }

    const shouldContinue = await promptContinue(
		`Are you sure you want to bump the version from ${ currentVersion } to ${newVersion}?`
	);

    if ( ! shouldContinue ) {
        return error( 'Aborting version bump.' );
    }

    updateComposerJsonVersion( `v${newVersion}` );
    updateWCCalypsoBridgeVersion( newVersion );
}

bumpVersion();