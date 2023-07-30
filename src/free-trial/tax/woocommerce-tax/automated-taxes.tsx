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
				{ __( 'Yes please', 'woocommerce' ) }
			</Button>
			<Button
				disabled={ isPending }
				isTertiary
				onClick={ () => {
					recordEvent( 'tasklist_tax_setup_automated_proceed', {
						setup_automatically: false,
					} );
					onManual();
				} }
			>
				{ __( "No thanks, I'll set up manually", 'woocommerce' ) }
			</Button>
			<Button disabled={ isPending } isTertiary onClick={ onDisable }>
				{ __( "I don't charge sales tax", 'woocommerce' ) }
			</Button>
		</div>
	);
};
