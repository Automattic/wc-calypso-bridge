import {
    getCurrentVersion,
    promptChangeDegree,
    updateWCCalypsoBridgeVersion,
    updateComposerJsonVersion,
    promptVersionConfirmation,
    error,
} from "../utils.js";
import { inc as incVersion } from "semver";

/**
 * Bumps the version number in the composer.json file and the wc-calypso-bridge.php file.
 */
async function bumpVersion() {
    const degree = await promptChangeDegree();

    const currentVersion = getCurrentVersion();
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

    if ( ! await promptVersionConfirmation( currentVersion, newVersion ) ) {
        error( 'Aborting version bump.' );
    }

    updateComposerJsonVersion( newVersion )
    updateWCCalypsoBridgeVersion( newVersion );
}

bumpVersion();