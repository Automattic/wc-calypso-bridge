import { exec } from 'child_process';
import { promisify } from 'util';
import https from 'https';
import fs from 'fs';
import { error, success, info, warning, gitFactory } from '../utils.js';

const execPromise = promisify( exec );
const REQUIRED_TRANSLATION_PERCENTAGE = 85;
const POT_FILE_PATH = 'languages/wc-calypso-bridge.pot';

async function downloadTranslations( url, outputPath ) {
	return new Promise( ( resolve, reject ) => {
		const file = fs.createWriteStream( outputPath );

		https
			.get( url, ( response ) => {
				response.pipe( file );
				file.on( 'finish', () => {
					file.close();
					resolve();
				} );
			} )
			.on( 'error', ( err ) => {
				reject( err );
			} );
	} );
}

async function verifyLangData( langData ) {
	try {
		const parsedData = JSON.parse( langData );
		if (
			! parsedData.translation_sets ||
			! Array.isArray( parsedData.translation_sets )
		) {
			error( 'Invalid language data format.' );
			process.exit( 1 );
		}
	} catch ( err ) {
		error( 'Invalid language data format.' );
		return false;
	}
}

export default async function updateTranslations() {
	const git = gitFactory();
	const wpPath = 'vendor/bin/wp';

	try {
		if ( ! fs.existsSync( wpPath ) ) {
			warning( 'wp not found, running composer install...' );
			await execPromise( 'composer install' );
		}

		info( 'Executing wp i18n make-pot command...' );

		// If the `wp i18n make-pot` command generates an error, an exception is thrown
		await execPromise(
			`${ wpPath } i18n make-pot . ${ POT_FILE_PATH } --ignore-domain --exclude=src`
		);
		success( 'wp i18n make-pot command executed successfully.' );

		const status = await git.status();

		const isPotFileUpdated =
			status.modified.includes( POT_FILE_PATH ) ||
			status.created.includes( POT_FILE_PATH );

		if ( isPotFileUpdated ) {
			info(
				`${ POT_FILE_PATH } updated. Changes will be added to the repository...`
			);
		}

		info( 'Retrieving language data from translate.wordpress.com...' );
		const langData = await new Promise( ( resolve, reject ) => {
			https
				.get(
					'https://translate.wordpress.com/api/projects/woocommerce/wc-calypso-bridge',
					( response ) => {
						let data = '';
						response.on( 'data', ( chunk ) => {
							data += chunk;
						} );
						response.on( 'end', () => {
							resolve( data );
						} );
					}
				)
				.on( 'error', ( err ) => {
					reject( err );
				} );
		} );

		if ( ! verifyLangData( langData ) ) {
			return false;
		}

		success( 'Language data retrieved successfully.' );

		const { translation_sets: translationSets } = JSON.parse( langData );

		const filteredSets = translationSets.filter(
			( set ) => set.percent_translated > REQUIRED_TRANSLATION_PERCENTAGE
		);

		for ( const lang of filteredSets ) {
			const {
				name: LANG_NAME,
				locale: LANG_LOCALE,
				slug: LANG_SLUG,
				wp_locale: LANG_WP_LOCALE,
			} = lang;

			if (
				LANG_NAME === undefined ||
				LANG_LOCALE === undefined ||
				LANG_SLUG === undefined ||
				LANG_WP_LOCALE === undefined
			) {
				error(
					'Missing required properties in language data. Skipping...'
				);
				continue;
			}

			const LANG_FILENAME = `languages/wc-calypso-bridge-${
				LANG_WP_LOCALE || LANG_LOCALE
			}.po`;
			const poUrl = `https://translate.wordpress.com/projects/woocommerce/wc-calypso-bridge/${ LANG_LOCALE }/${ LANG_SLUG }/export-translations/?format=po`;

			info(
				`Downloading ${ LANG_NAME } (${ LANG_LOCALE }/${ LANG_SLUG } → ${
					LANG_WP_LOCALE || LANG_LOCALE
				})...`
			);

			try {
				await downloadTranslations( poUrl, LANG_FILENAME );
				success( `${ LANG_FILENAME } downloaded successfully.` );

				await execPromise(
					`msgfmt ${ LANG_FILENAME } -o ${ LANG_FILENAME.replace(
						'.po',
						'.mo'
					) }`
				);

				await execPromise(
					`${ wpPath } i18n make-json ${ LANG_FILENAME }`
				);

				success( `${ LANG_FILENAME } compiled successfully.` );
			} catch ( err ) {
				error(
					`Error downloading or compiling ${ LANG_FILENAME }: ${ err.message }`
				);

				continue;
			}
		}

		info( 'Staging language files...' );
		await git.add( 'languages/*' );
		success( 'Language files staged successfully.' );

		info( 'Committing language files...' );
		await git.commit( 'Add new translation files' );
		success( 'Language files committed successfully.' );

		return success( 'Script execution completed.' );
	} catch ( err ) {
		if ( err.stdout ) {
			info( err.stdout );
		}
		if ( err.stderr ) {
			warning( err.stderr );
		}
		return error( 'Error executing the script: ' + err.message );
	}
}
