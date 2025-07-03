module.exports = {
	extends: [ 'plugin:@woocommerce/eslint-plugin/recommended' ],
	overrides: [
		{
			files: [ '**/*.js', '**/*.jsx', '**/*.ts', '**/*.tsx' ],
			rules: {
				'react/react-in-jsx-scope': 'off',
				'@wordpress/i18n-text-domain': [
					'error',
					{
						allowedTextDomain: [
							'wc-calypso-bridge',
							'woocommerce',
						],
					},
				],
			},
		},
	],
	settings: {
		'import/core-modules': [
			'@woocommerce/admin-layout',
			'@woocommerce/block-templates',
			'@woocommerce/components',
			'@woocommerce/customer-effort-score',
			'@woocommerce/currency',
			'@woocommerce/data',
			'@woocommerce/experimental',
			'@woocommerce/expression-evaluation',
			'@woocommerce/navigation',
			'@woocommerce/number',
			'@woocommerce/settings',
			'@woocommerce/tracks',
			'@testing-library/react',
			'react',
			'react-router-dom',
			'prop-types',
			'lodash',
		],
		'import/resolver': {
			node: {
				extensions: [ '.js', '.jsx', '.ts', '.tsx' ],
			},
		},
	},
};
