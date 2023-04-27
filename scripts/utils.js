import fs from 'fs';
import simpleGit from 'simple-git';
import chalk from 'chalk';

const git = simpleGit();

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

export function getCurrentVersion() {
	try {
		const packageJson = fs.readFileSync( '../composer.json' );
		const { version } = JSON.parse( packageJson );

		return version;
	} catch ( error ) {
		console.error( error );
		return null;
	}
}

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
