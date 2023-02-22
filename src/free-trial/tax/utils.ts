/**
 * External dependencies
 */
import { getAdminLink } from '@woocommerce/settings';
import { TaskType } from '@woocommerce/data';

/**
 * Plugins required to automate taxes.
 */
export const AUTOMATION_PLUGINS = [ 'jetpack', 'woocommerce-services' ];

/**
 * Check if a store has a complete address given general settings.
 *
 * @param {Object} generalSettings                             General settings.
 * @param {Object} generalSettings.woocommerce_store_address   Store address.
 * @param {Object} generalSettings.woocommerce_default_country Store default country.
 * @param {Object} generalSettings.woocommerce_store_postcode  Store postal code.
 */
export const hasCompleteAddress = (
	generalSettings: Record< string, string >
): boolean => {
	const {
		woocommerce_store_address: storeAddress,
		woocommerce_default_country: defaultCountry,
		woocommerce_store_postcode: storePostCode,
	} = generalSettings;
	return Boolean( storeAddress && defaultCountry && storePostCode );
};

/**
 * Redirect to the core tax settings screen.
 */
export const redirectToTaxSettings = (): void => {
	window.location.href = getAdminLink(
		'admin.php?page=wc-settings&tab=tax&section=standard&wc_onboarding_active_task=tax'
	);
};

/**
 * Types for child tax components.
 */
export type TaxChildProps = {
	isPending: boolean;
	onAutomate: () => void;
	onManual: () => void;
	onDisable: () => void;
	task: TaskType;
};
