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
			title={ strings.surveyTitle }
			onRequestClose={ closeModal }
			shouldCloseOnClickOutside={ false }
		>
			<p className="wc-calypso-bridge-payments-welcome-survey__intro">{strings.surveyIntro}</p>

			<p className="wc-calypso-bridge-payments-welcome-survey__question" >{strings.surveyQuestion}</p>
			
			<div className="wc-calypso-bridge-payments-welcome-survey__selection">
				<CheckboxControl
					label={ strings.surveyHappyLabel }
					checked={ isHappyChecked }
					onChange={ setHappyChecked }
				/>
				<CheckboxControl
					label={ strings.surveyInstallLabel }
					checked={ isInstallChecked }
					onChange={ setInstallChecked }
				/>
				<CheckboxControl
					label={ strings.surveyMoreInfoLabel }
					checked={ isMoreInfoChecked }
					onChange={ setMoreInfoChecked }
				/>
				<CheckboxControl
					label={ strings.surveyAnotherTimeLabel }
					checked={ isAnotherTimeChecked }
					onChange={ setAnotherTimeChecked }
				/>
				<CheckboxControl
					label={ strings.surveySomethingElseLabel }
					checked={ isSomethingElseChecked }
					onChange={ setSomethingElseChecked }
				/>
			</div>

			<div className="wc-calypso-bridge-payments-welcome-survey__comments">
				<TextareaControl
					label={ strings.surveyCommentsLabel }
					value={ comments }
					onChange={ ( value: string ) => setComments( value ) }
					rows={ 3 }
				/>
			</div>

			<div className="wc-calypso-bridge-payments-welcome-survey__buttons">
				<Button isTertiary isDestructive onClick={ closeModal } name="cancel">
					{ strings.surveyCancelButton }
				</Button>
				<Button isSecondary onClick={ sendFeedback } name="send">
					{ strings.surveySubmitButton }
				</Button>
			</div>
		</Modal>
	);
}

export default ExitSurveyModal;
