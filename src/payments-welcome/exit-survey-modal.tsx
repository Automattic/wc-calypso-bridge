/**
 * External dependencies
 */
import { useState } from '@wordpress/element';
import {
	Button,
	Modal,
	CheckboxControl,
	TextareaControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import strings from './strings';
import wcpayTracks from './tracks';

/**
 * Provides a modal requesting customer feedback.
 *
 */
function ExitSurveyModal(): JSX.Element | null {
	const [ isOpen, setOpen ] = useState( true );
	const [ isHappyChecked, setHappyChecked ] = useState(false);
	const [ isInstallChecked, setInstallChecked ] = useState(false);
	const [ isMoreInfoChecked, setMoreInfoChecked ] = useState(false);
	const [ isAnotherTimeChecked, setAnotherTimeChecked ] = useState(false);
	const [ isSomethingElseChecked, setSomethingElseChecked ] = useState(false);
	const [ comments, setComments ] = useState( '' );
	
	const closeModal = () => setOpen( false );

	const sendFeedback = () => {
		wcpayTracks.recordEvent(wcpayTracks.events.SURVEY_FEEDBACK, {
			happy: isHappyChecked ? 'Yes' : 'No',
			install: isInstallChecked ? 'Yes' : 'No',
			moreInfo: isMoreInfoChecked ? 'Yes' : 'No',
			anotherTime: isAnotherTimeChecked ? 'Yes' : 'No',
			somethingElse: isSomethingElseChecked ? 'Yes' : 'No',
			comments: comments,
		});
		setOpen( false );
	};

	if ( ! isOpen ) {
		return null;
	}

	return (
		<Modal
			className="wc-calypso-bridge-payments-welcome-survey"
			title={ __( 'Remove WooCommerce Payments', 'wc-calypso-bridge' ) }
			onRequestClose={ closeModal }
			shouldCloseOnClickOutside={ false }
		>
			<p className="wc-calypso-bridge-payments-welcome-survey__intro">{strings.surveyIntro}</p>

			<p className="wc-calypso-bridge-payments-welcome-survey__question" >{strings.surveyQuestion}</p>
			
			<div className="wc-calypso-bridge-payments-welcome-survey__selection">
				<CheckboxControl
					label={ __('I’m already happy with my payments setup') }
					checked={ isHappyChecked }
					onChange={ setHappyChecked }
				/>
				<CheckboxControl
					label={ __('I don’t want to install another plugin') }
					checked={ isInstallChecked }
					onChange={ setInstallChecked }
				/>
				<CheckboxControl
					label={ __('I need more information about WooCommerce Payments') }
					checked={ isMoreInfoChecked }
					onChange={ setMoreInfoChecked }
				/>
				<CheckboxControl
					label={ __('I’m open to installing it another time') }
					checked={ isAnotherTimeChecked }
					onChange={ setAnotherTimeChecked }
				/>
				<CheckboxControl
					label={ __('It’s something else (Please share below)') }
					checked={ isSomethingElseChecked }
					onChange={ setSomethingElseChecked }
				/>
			</div>

			<div className="wc-calypso-bridge-payments-welcome-survey__comments">
				<TextareaControl
					label={ __(
						'Comments (Optional)',
						'woocommerce-admin'
					) }
					value={ comments }
					onChange={ ( value: string ) => setComments( value ) }
					rows={ 3 }
				/>
			</div>

			<div className="wc-calypso-bridge-payments-welcome-survey__buttons">
				<Button isTertiary isDestructive onClick={ closeModal } name="cancel">
					{ __( 'Just remove WooCommerce Payments', 'wc-calypso-bridge' ) }
				</Button>
				<Button isSecondary onClick={ sendFeedback } name="send">
					{ __( 'Remove and send feedback', 'wc-calypso-bridge' ) }
				</Button>
			</div>
		</Modal>
	);
}

export default ExitSurveyModal;
