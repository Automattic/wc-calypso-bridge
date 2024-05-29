const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const WooCommerceDependencyExtractionWebpackPlugin = require( '@woocommerce/dependency-extraction-webpack-plugin' );
const I18nLoaderWebpackPlugin = require( '@automattic/i18n-loader-webpack-plugin' );
const path = require( 'path' );

// Import variables and mixins from the stylesheets directory so they can be used in all scss files.
defaultConfig.module.rules.push( {
	test: /\.(sc|sa)ss$/,
	use: [
		{
			loader: 'sass-loader',
			options: {
				sassOptions: {
					includePaths: [
						path.resolve( __dirname, 'src/stylesheets' ),
					],
				},
				// Prepends Sass/SCSS code before the actual entry file
				additionalData: ( content ) => {
					return (
						'@import "_variables"; ' +
						'@import "_mixins"; ' +
						content
					);
				},
			},
		},
	],
} );

// Disable exports mangling to ensure the exported i18n loader method name `loadTranslations` is preserved,
// as it's being referred by name in the I18nLoaderWebpackPlugin's runtime template.
defaultConfig.optimization.mangleExports = false;

module.exports = {
	...defaultConfig,
	plugins: [
		...defaultConfig.plugins.filter(
			( plugin ) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WooCommerceDependencyExtractionWebpackPlugin(),
		new I18nLoaderWebpackPlugin( {
			textdomain: 'wc-calypso-bridge',
			loaderModule: './src/i18n-loader',
			loaderMethod: 'loadTranslations',
		} ),
	],
};
