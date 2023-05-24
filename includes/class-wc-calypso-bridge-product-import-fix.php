<?php
/**
 * Product CSV import fix.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.1.2
 * @version 2.1.2
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Notes Class.
 */
class WC_Calypso_Bridge_Product_Import_Fix {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Patch for sample product import issue with WooCommerce < 7.8.0.
	 * Issue: https://github.com/woocommerce/woocommerce/issues/38069
	 */
	public function init() {
		if ( defined( 'WC_VERSION' ) &&	version_compare( WC_VERSION, '7.8.0', '<' ) ) {
			add_filter( 'woocommerce_product_csv_importer_args', array( $this, 'replace_csv_importer_args' ) );
		}
	}

	/**
	 * Replace CSV importer args.
	 */
	public function replace_csv_importer_args( $args ) {
		// The defining symptom is that the product type key has a value 'Type' instead of 'type'.
		if ( isset( $args['mapping']['Type'] ) && 'Type' === $args['mapping']['Type'] ) {
			$args['mapping'] = $this->get_header_mappings( array_keys( $args['mapping'] ) );
		}
		return $args;
	}

	/**
	 * Get fixed CSV header columns.
	 *
	 * @internal
	 * @param array $raw_headers column keys from imported CSV
	 * @return array Mapped headers.
	 */
	public function get_header_mappings( $raw_headers ) {
		$default_columns = $this->get_default_english_mappings();
		$special_columns = $this->get_default_english_special_mappings();

		$headers = array();
		foreach ( $raw_headers as $key => $field ) {
			$index             = $field;
			$headers[ $index ] = $field;

			if ( isset( $default_columns[ $field ] ) ) {
				$headers[ $index ] = $default_columns[ $field ];
			} else {
				foreach ( $special_columns as $regex => $special_key ) {
					if ( preg_match( self::sanitize_special_column_name_regex( $regex ), $field, $matches ) ) {
						$headers[ $index ] = $special_key . $matches[1];
						break;
					}
				}
			}
		}

		return $headers;
	}

	/**
	 * Sanitize special column name regex.
	 *
	 * @internal
	 * @param  string $value Raw special column name.
	 * @return string
	 */
	public static function sanitize_special_column_name_regex( $value ) {
		return '/' . str_replace( array( '%d', '%s' ), '(.*)', trim( quotemeta( $value ) ) ) . '/';
	}


	/**
	 * Copied from default mapping.
	 * https://github.com/woocommerce/woocommerce/blob/0a45cbfc83177c6f870c4eaf508919567b3d516e/plugins/woocommerce/includes/admin/importers/mappings/default.php#L1
	 */
	public function get_default_english_mappings() {
		$weight_unit    = get_option( 'woocommerce_weight_unit' );
		$dimension_unit = get_option( 'woocommerce_dimension_unit' );
		return array(
		'ID'                                      => 'id',
		'Type'                                    => 'type',
		'SKU'                                     => 'sku',
		'Name'                                    => 'name',
		'Published'                               => 'published',
		'Is featured?'                            => 'featured',
		'Visibility in catalog'                   => 'catalog_visibility',
		'Short description'                       => 'short_description',
		'Description'                             => 'description',
		'Date sale price starts'                  => 'date_on_sale_from',
		'Date sale price ends'                    => 'date_on_sale_to',
		'Tax status'                              => 'tax_status',
		'Tax class'                               => 'tax_class',
		'In stock?'                               => 'stock_status',
		'Stock'                                   => 'stock_quantity',
		'Backorders allowed?'                     => 'backorders',
		'Low stock amount'                        => 'low_stock_amount',
		'Sold individually?'                      => 'sold_individually',
		sprintf( 'Weight (%s)', $weight_unit )    => 'weight',
		sprintf( 'Length (%s)', $dimension_unit ) => 'length',
		sprintf( 'Width (%s)', $dimension_unit )  => 'width',
		sprintf( 'Height (%s)', $dimension_unit ) => 'height',
		'Allow customer reviews?'                 => 'reviews_allowed',
		'Purchase note'                           => 'purchase_note',
		'Sale price'                              => 'sale_price',
		'Regular price'                           => 'regular_price',
		'Categories'                              => 'category_ids',
		'Tags'                                    => 'tag_ids',
		'Shipping class'                          => 'shipping_class_id',
		'Images'                                  => 'images',
		'Download limit'                          => 'download_limit',
		'Download expiry days'                    => 'download_expiry',
		'Parent'                                  => 'parent_id',
		'Upsells'                                 => 'upsell_ids',
		'Cross-sells'                             => 'cross_sell_ids',
		'Grouped products'                        => 'grouped_products',
		'External URL'                            => 'product_url',
		'Button text'                             => 'button_text',
		'Position'                                => 'menu_order',
		);
	}

	/**
	 * Copied from default mapping.
	 * https://github.com/woocommerce/woocommerce/blob/0a45cbfc83177c6f870c4eaf508919567b3d516e/plugins/woocommerce/includes/admin/importers/mappings/default.php#L1
	 */
	public function get_default_english_special_mappings() {
		return array(
		'Attribute %d name'     => 'attributes:name',
		'Attribute %d value(s)' => 'attributes:value',
		'Attribute %d visible'  => 'attributes:visible',
		'Attribute %d global'   => 'attributes:taxonomy',
		'Attribute %d default'  => 'attributes:default',
		'Download %d ID'        => 'downloads:id',
		'Download %d name'      => 'downloads:name',
		'Download %d URL'       => 'downloads:url',
		'Meta: %s'              => 'meta:',
		);
	}
}

WC_Calypso_Bridge_Product_Import_Fix::get_instance();
