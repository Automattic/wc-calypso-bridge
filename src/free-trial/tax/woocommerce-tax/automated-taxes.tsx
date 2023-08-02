/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import interpolateComponents from '@automattic/interpolate-components';
import { H } from '@woocommerce/components';
import { recordEvent } from '@woocommerce/tracks';

/**
 * Internal dependencies
 */
import { SetupStepProps } from './setup';

export const AutomatedTaxes: React.FC<
	Pick<
		SetupStepProps,
		'isPending' | 'onAutomate' | 'onManual' | 'onDisable'
	>
> = ( { isPending, onAutomate, onManual, onDisable } ) => {
	return (
		<div className="woocommerce-task-tax__success">
			<Button
				isPrimary
				isBusy={ isPending }
				onClick={ () => {
					recordEvent( 'tasklist_tax_setup_automated_proceed', {
						setup_automatically: true,
					} );
					onAutomate();
				} }
			>
				{ __( 'Automate taxes', 'woocommerce' ) }
			</Button>
		</div>
	);
};
