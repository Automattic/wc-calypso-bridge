const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const WooCommerceDependencyExtractionWebpackPlugin = require( '@woocommerce/dependency-extraction-webpack-plugin' );
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

module.exports = {
	...defaultConfig,
	plugins: [
		...defaultConfig.plugins.filter(
			( plugin ) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WooCommerceDependencyExtractionWebpackPlugin(),
	],
};
