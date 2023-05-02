import fs, { promises as fsPromises } from 'fs';
import { fileURLToPath } from 'url';
import path from 'path';
import inquirer from 'inquirer';
import simpleGit from 'simple-git';
import chalk from 'chalk';

const git = simpleGit();
export const __dirname = path.dirname( fileURLToPath( import.meta.url ) );

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

		return version;
	} catch ( err ) {
		error( err );
		return null;
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

// Pull the latest master changes from origin.
export async function pullLatestChanges() {
	await git.checkout( 'master' );
	await git.pull( 'origin', 'master' );
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
}
