/**
 * External packages
 */
const path = require( 'path' );

module.exports = {
	preset: '@wordpress/jest-preset-default',
	moduleNameMapper: {
		'@woocommerce/settings': path.resolve(
			__dirname,
			'tests/mocks/woocommerce-settings'
		),
		'\\.(jpg|jpeg|png|gif|eot|otf|webp|svg|ttf|woff|woff2|mp4|webm|wav|mp3|m4a|aac|oga)$':
			path.resolve( __dirname, 'tests/mocks/static' ),
	},
	restoreMocks: true,
	setupFiles: [
		path.resolve( __dirname, 'tests/setup/setup-window-globals.js' ),
		path.resolve( __dirname, 'tests/setup/setup-globals.js' ),
	],
	setupFilesAfterEnv: [
		path.resolve( __dirname, 'tests/setup/setup-react-testing-library.js' ),
	],
	transformIgnorePatterns: [ 'node_modules/(?!is-plain-obj)', '/build/' ],
};
