import fs, { promises as fsPromises } from 'fs';
import { fileURLToPath } from 'url';
import path from 'path';
import inquirer from 'inquirer';
import simpleGit from 'simple-git';
import chalk from 'chalk';

const git = simpleGit();
export const __dirname = path.dirname( fileURLToPath( import.meta.url ) );

export const CHANGE_DEGREE_PATCH = 'Patch (bug fixes, x.x.N)';
export const CHANGE_DEGREE_MINOR = 'Minor (new features, backwards compatible, x.N.x)';
export const CHANGE_DEGREE_MAJOR = 'Major (breaking changes, N.x.x)';

export function error( message ) {
	console.log( 'ERROR: ' + chalk.red( message ) );
}

export function success( message ) {
	console.log( 'SUCCESS: ' + chalk.green( message ) );
}

export function warning( message ) {
	console.log( 'WARNING: ' + chalk.yellow( message ) );
}

export function info( message ) {
	console.log( 'INFO: ' + chalk.blue( message ) );
}

export function gitFactory() {
	return git;
}

export async function promptContinue( msg ) {
	const answer = await inquirer.prompt( [
		{
			type: 'confirm',
			name: 'continue',
			message: msg,
			default: false,
		},
	] );

	return answer.continue;
}

export function getNvmrcVersion() {
	const nvmrcContents = fs.readFileSync( `${ __dirname }/../.nvmrc`, 'utf8' );

	return parseInt( nvmrcContents.trim() );
}

export function isCorrectNodeVersion() {
	try {
		const nodeVersion = process.versions.node;
		const [ major ] = nodeVersion.split( '.' );

		return getNvmrcVersion() === parseInt( major );
	} catch ( err ) {
		error( `There was a problem checking the node version (${ err })` );
		return false;
	}
}

// Retrieve the version from the project composer.json
export function getCurrentVersion() {
	try {
		const packageJson = fs.readFileSync(
			`${ __dirname }/../composer.json`
		);
		const { version } = JSON.parse( packageJson );

		return version.replace( /[^\d.]/g, '' );
	} catch ( err ) {
		error( err );
		return null;
	}
}

/**
 * Updates the version number in the composer.json file.
 *
 * @param {string} newVersion The new version number to set in the composer.json file
 */
export function updateComposerJsonVersion( newVersion ) {
	const composerJsonPath = `${ __dirname }/../composer.json`;
	// Read the contents of the composer.json file
	const composerFile = fs.readFileSync( composerJsonPath );
	const composerData = JSON.parse( composerFile );

	composerData.version = newVersion;

	fs.writeFileSync( composerJsonPath, JSON.stringify( composerData, null, 2 ));

	success(`Updated version the composer.json file to ${ newVersion }`);
}

/**
 * Updates the version number in the wc-calypso-bridge.php file.
 *
 * @param {string} newVersion The new version number to set in the wc-calypso-bridge.php file
 */
export function updateWCCalypsoBridgeVersion( newVersion ) {
	const wcCalypsoBridgePath = `${ __dirname }/../wc-calypso-bridge.php`;
	// Read the contents of the PHP file
	const wcCalypsoBridgeContent = fs.readFileSync( wcCalypsoBridgePath, 'utf-8' );

	// Update the version number in the define() statement using a regular expression
	const updatedDefineStatement = wcCalypsoBridgeContent.replace(/(define\(\s*['"]WC_CALYPSO_BRIDGE_CURRENT_VERSION['"]\s*,\s*['"])(\d+\.\d+\.\d+)['"]\s*\)/i, ( match, prefix ) => {
		return `${prefix}${newVersion}' )`;
	});

	// Update the version number in the Version: line using a regular expression
	const updatedWcCalypsoBridge = updatedDefineStatement.replace(/(Version:\s*)(\d+\.\d+\.\d+)/i, ( match, prefix ) => {
		return `${prefix}${newVersion}`;
	});

	// Write the updated contents back to the PHP file
	fs.writeFileSync( wcCalypsoBridgePath, updatedWcCalypsoBridge );

	success(`Successfully updated version number in wc-calypso-bridge.php to ${ newVersion }`);
}

/**
 * Prompts the user to select the degree of the change.
 *
 * @returns {string} The deegree of the change that can be:
 * - Patch (bug fixes)
 * - Minor (new features, backwards compatible)
 * - Major (breaking changes)
 */
export async function promptChangeDegree() {
	const answers = await inquirer.prompt({
		type: 'list',
		name: 'changeDegree',
		message: 'What is the degree of the change?',
		choices: [
			CHANGE_DEGREE_PATCH,
			CHANGE_DEGREE_MINOR,
			CHANGE_DEGREE_MAJOR,
		],
	});
	return answers.changeDegree;
}

/**
 * Gets the type of the change based on the degree of the change.
 *
 * @param {string} changeDegree The degree of the change
 * @returns {string} The type of the change degree that can be:
 * - patch
 * - minor
 * - major
 */
export function getChangeDegreeType( changeDegree ) {
	if ( changeDegree === CHANGE_DEGREE_PATCH ) {
		return 'patch';
	}
	if ( changeDegree === CHANGE_DEGREE_MINOR ) {
		return 'minor';
	}
	if ( changeDegree === CHANGE_DEGREE_MAJOR ) {
		return 'major';
	}
}

// Performs a git status on the project.
export async function getStatus() {
	try {
		const status = await git.status();

		return status;
	} catch ( err ) {
		console.error( err );
		console.log(
			'Invalid path. Usage: node script.js /path/to/repository'
		);
	}
}

export async function tagExists( tagName ) {
	const tags = await git.tag( [ '-l', tagName.replace( /[^\d.]/g, '' ) ] );
	const tag = tags.replace( /\n/g, '' );

	return tag && tag.length > 0;
}

export async function getCurrentBranchName() {
	const currentBranch = await git.branch();
	return currentBranch.current;
}

export async function switchToBranchWithMessage( branchName ) {
	if ( branchName !== 'master' ) {
		info( `Switching back to branch '${ branchName }'` );
		await git.checkout( branchName );
	}
}

// Check to see if we're running a dev build.
// We currently do this by checking for the existence of a .map file in the build directory.
export function isDevBuild() {
	try {
		const files = fs.readdirSync( 'build' );

		return files.some( ( file ) => file.endsWith( '.map' ) );
	} catch ( err ) {
		// If the build directory doesn't exist, we're definitely not in a dev build.
		return false;
	}
}

// Verify that `npm run build` finished successfully.
export async function verifyBuild() {
	const errors = [];
	const files = fs.readdirSync( 'build' );

	// Webpack should create at least one file that is named <n><n><n>.js.
	if ( ! files.some( ( file ) => file && file.match( /\d+\.js/ ) ) ) {
		errors.push( '\tBuild files may be missing.' );
	}

	// Make sure there are no empty files.
	for ( const file of files ) {
		const stats = await fsPromises.stat( `build/${ file }` );

		if ( stats.size <= 0 ) {
			errors.push( `\tBuild file ${ file } is empty.` );
		}
	}

	// TODO - How else can we verify the build finished successfully?

	return errors;
<<<<<<< HEAD
}

export async function updateChangelog( newChangelog ) {
	const readmeFile = `${ __dirname }/../readme.txt`;
	const fileContents = fs.readFileSync( readmeFile, 'utf8' );
	const changelogMarker = '== Changelog ==';
	const changelogIndex = fileContents.indexOf( changelogMarker );
	const startOfText = fileContents.substring(
		0,
		changelogIndex + changelogMarker.length
	);
	const restOfText = fileContents.substring(
		changelogIndex + changelogMarker.length
	);

	fs.writeFileSync(
		readmeFile,
		startOfText + '\n\n' + newChangelog.trim() + restOfText,
		'utf8'
	);
}
=======
}
>>>>>>> f777aa0 (remove the readme update function as it is being addressed on #1098)
