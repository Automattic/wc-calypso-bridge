import {exec} from 'child_process';
import {promisify} from 'util';
import https from 'https';
import fs from 'fs';
import {error, success, info, gitFactory} from '../utils.js';

const execPromise = promisify(exec);
const REQUIRED_TRANSLATION_PERCENTAGE = 85;

async function downloadTranslations(url, outputPath) {
	return new Promise((resolve, reject) => {
		const file = fs.createWriteStream(outputPath);

		https.get(url, (response) => {
			response.pipe(file);
			file.on('finish', () => {
				file.close();
				resolve();
			});
		}).on('error', (err) => {
			reject(err);
		});
	});
}

async function runScript() {
	const git = gitFactory();

	try {
		info('Executing wp i18n make-pot command...');
		await execPromise('wp i18n make-pot . languages/wc-calypso-bridge.pot --ignore-domain');
		success('wp i18n make-pot command executed successfully.');

		info('Retrieving language data from translate.wordpress.com...');
		const langData = await new Promise((resolve, reject) => {
			https.get('https://translate.wordpress.com/api/projects/woocommerce/wc-calypso-bridge', (response) => {
				let data = '';
				response.on('data', (chunk) => {
					data += chunk;
				});
				response.on('end', () => {
					resolve(data);
				});
			}).on('error', (err) => {
				reject(err);
			});
		});
		success('Language data retrieved successfully.');

		const {translation_sets: translationSets} = JSON.parse(langData);

		const filteredSets = translationSets.filter(set => set.percent_translated > REQUIRED_TRANSLATION_PERCENTAGE);

		for (const lang of filteredSets) {
			const {name: LANG_NAME, locale: LANG_LOCALE, slug: LANG_SLUG, wp_locale: LANG_WP_LOCALE} = lang;

			const LANG_FILENAME = `languages/wc-calypso-bridge-${LANG_WP_LOCALE || LANG_LOCALE}.po`;
			const poUrl = `https://translate.wordpress.com/projects/woocommerce/wc-calypso-bridge/${LANG_LOCALE}/${LANG_SLUG}/export-translations/?format=po`;

			info(`Downloading ${LANG_NAME} (${LANG_LOCALE}/${LANG_SLUG} → ${LANG_WP_LOCALE || LANG_LOCALE})...`);

			await downloadTranslations(poUrl, LANG_FILENAME);
			success(`${LANG_FILENAME} downloaded successfully.`);

			await execPromise(`msgfmt ${LANG_FILENAME} -o ${LANG_FILENAME.replace('.po', '.mo')}`);
			success(`${LANG_FILENAME} compiled successfully.`);
		}

		info('Staging language files...');
		await git.add('languages/*');
		success('Language files staged successfully.');

		info('Committing language files...');
		await git.commit('Add new translation files');
		success('Language files committed successfully.');

		console.log('Script execution completed.');
	} catch (err) {
		error('Error executing the script: ' + err.message);
		process.exit(1);
	}
}

runScript();
