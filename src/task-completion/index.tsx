/**
 * External dependencies
 */
import classnames from 'classnames';
import { useEffect, useState } from '@wordpress/element';
import { EllipsisMenu } from '@woocommerce/components';
import { recordEvent } from '@woocommerce/tracks';
import { useDispatch, useSelect } from '@wordpress/data';
import { OPTIONS_STORE_NAME, WCDataSelector, WEEK } from '@woocommerce/data';
import { Button, Card, CardHeader } from '@wordpress/components';
import { Text } from '@woocommerce/experimental';
import {
	CustomerFeedbackModal,
	CustomerFeedbackSimple,
} from '@woocommerce/customer-effort-score';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';
import HeaderImage from './completed-celebration-header.jsx';

const signupUrl =
	'https://wordpress.com/plans/' + window.wcCalypsoBridge.siteSlug;
type TaskListCompletedHeaderProps = {
	hideTasks: () => void;
	keepTasks: () => void;
	customerEffortScore: boolean;
};

const ADMIN_INSTALL_TIMESTAMP_OPTION_NAME =
	'woocommerce_admin_install_timestamp';
const SHOWN_FOR_ACTIONS_OPTION_NAME = 'woocommerce_ces_shown_for_actions';
const CUSTOMER_EFFORT_SCORE_ACTION = 'store_setup';
const ALLOW_TRACKING_OPTION_NAME = 'woocommerce_allow_tracking';

function getStoreAgeInWeeks( adminInstallTimestamp: number ) {
	if ( adminInstallTimestamp === 0 ) {
		return null;
	}

	// Date.now() is ms since Unix epoch, adminInstallTimestamp is in
	// seconds since Unix epoch.
	const storeAgeInMs = Date.now() - adminInstallTimestamp * 1000;
	const storeAgeInWeeks = Math.round( storeAgeInMs / WEEK );

	return storeAgeInWeeks;
}

export const TaskListCompletedHeader: React.FC<
	TaskListCompletedHeaderProps
> = ( { hideTasks, keepTasks, customerEffortScore } ) => {
	const { updateOptions } = useDispatch( OPTIONS_STORE_NAME );
	const [ showCesModal, setShowCesModal ] = useState( false );
	const [ hasSubmittedScore, setHasSubmittedScore ] = useState( false );
	const [ score, setScore ] = useState( NaN );
	const [ hideCustomerEffortScore, setHideCustomerEffortScore ] =
		useState( false );
	const { storeAgeInWeeks, cesShownForActions, canShowCustomerEffortScore } =
		useSelect( ( select: WCDataSelector ) => {
			const { getOption, hasFinishedResolution } =
				select( OPTIONS_STORE_NAME );

			if ( customerEffortScore ) {
				const allowTracking = getOption( ALLOW_TRACKING_OPTION_NAME );
				const adminInstallTimestamp: number =
					getOption( ADMIN_INSTALL_TIMESTAMP_OPTION_NAME ) || 0;
				const cesActions = getOption< string[] >(
					SHOWN_FOR_ACTIONS_OPTION_NAME
				);
				const loadingOptions =
					! hasFinishedResolution( 'getOption', [
						SHOWN_FOR_ACTIONS_OPTION_NAME,
					] ) ||
					! hasFinishedResolution( 'getOption', [
						ADMIN_INSTALL_TIMESTAMP_OPTION_NAME,
					] );
				return {
					storeAgeInWeeks: getStoreAgeInWeeks(
						adminInstallTimestamp
					),
					cesShownForActions: cesActions,
					canShowCustomerEffortScore:
						! loadingOptions &&
						allowTracking &&
						! ( cesActions || [] ).includes( 'store_setup' ),
					loading: loadingOptions,
				};
			}
			return {};
		} );

	useEffect( () => {
		if ( hasSubmittedScore ) {
			setTimeout( () => {
				setHideCustomerEffortScore( true );
			}, 1200 );
		}
	}, [ hasSubmittedScore ] );

	const submitScore = ( {
		firstScore,
		secondScore,
		comments,
	}: {
		firstScore: number;
		secondScore?: number;
		comments?: string;
	} ) => {
		recordEvent( 'ces_feedback', {
			action: CUSTOMER_EFFORT_SCORE_ACTION,
			score: firstScore,
			score_second_question: secondScore ?? null,
			score_combined: firstScore + ( secondScore ?? 0 ),
			comments: comments || '',
			store_age: storeAgeInWeeks,
		} );
		updateOptions( {
			[ SHOWN_FOR_ACTIONS_OPTION_NAME ]: [
				CUSTOMER_EFFORT_SCORE_ACTION,
				...( cesShownForActions || [] ),
			],
		} );
		setHasSubmittedScore( true );
	};

	const recordScore = ( recordedScore: number ) => {
		if ( recordedScore > 2 ) {
			setScore( recordedScore );
			submitScore( { firstScore: recordedScore } );
		} else {
			setScore( recordedScore );
			setShowCesModal( true );
			recordEvent( 'ces_view', {
				action: CUSTOMER_EFFORT_SCORE_ACTION,
				store_age: storeAgeInWeeks,
			} );
		}
	};

	const recordModalScore = (
		firstScore: number,
		secondScore: number,
		comments: string
	) => {
		setShowCesModal( false );
		submitScore( { firstScore, secondScore, comments } );
	};

	return (
		<>
			<div
				className={ classnames(
					'woocommerce-task-dashboard__container two-column-experiment'
				) }
			>
				<Card
					size="large"
					className="woocommerce-task-card woocommerce-homescreen-card completed"
				>
					<CardHeader size="medium">
						<div className="wooocommerce-task-card__header">
							<HeaderImage
								className="wooocommerce-task-card__finished-header-image"
								alt="Completed"
							/>

							<Text size="title" as="h2" lineHeight={ 1.4 }>
								{ __(
									'Woohoo! Your trial store has been set up!',
									'wc-calypso-bridge'
								) }
							</Text>
							<Text
								variant="subtitle.small"
								as="p"
								size="13"
								lineHeight="16px"
								className="wooocommerce-task-card__header-subtitle"
							>
								{ __(
									"Congratulations! Take a moment to celebrate. And once you're ready to launch your store, all you have to do is upgrade to a paid plan. This will also unlock the two upcoming tasks in the task list.",
									'wc-calypso-bridge'
								) }
							</Text>
							<Button href={ signupUrl } variant="primary">
								{ __(
									'Upgrade now',
									'wc-calypso-bridge'
								) }
							</Button>
							
							<div className="woocommerce-task-card__header-menu">
								{ /* @ts-expect-error: type def. is not up to date. Ignoring for now. */ }
								<EllipsisMenu
									label={ __(
										'Task List Options',
										'wc-calypso-bridge'
									) }
									renderContent={ () => (
										<div className="woocommerce-task-card__section-controls">
											<Button
												onClick={ () => keepTasks() }
											>
												{ __(
													'Show setup task list',
													'wc-calypso-bridge'
												) }
											</Button>
											<Button
												onClick={ () => hideTasks() }
											>
												{ __(
													'Hide this',
													'wc-calypso-bridge'
												) }
											</Button>
										</div>
									) }
								/>
							</div>
						</div>
					</CardHeader>
					{ canShowCustomerEffortScore &&
						! hideCustomerEffortScore &&
						! hasSubmittedScore && (
							<CustomerFeedbackSimple
								label={ __(
									'How was your experience?',
									'wc-calypso-bridge'
								) }
								onSelect={ recordScore }
							/>
						) }
					{ hasSubmittedScore && ! hideCustomerEffortScore && (
						<div className="wooocommerce-task-card__header-ces-feedback">
							<Text
								variant="subtitle.small"
								as="p"
								size="13"
								lineHeight="16px"
							>
								🙌{ ' ' }
								{ __(
									'We appreciate your feedback!',
									'wc-calypso-bridge'
								) }
							</Text>
						</div>
					) }
				</Card>
			</div>
			{ showCesModal ? (
				<CustomerFeedbackModal
					title={ __(
						'How was your experience?',
						'wc-calypso-bridge'
					) }
					firstQuestion={ __(
						'The store setup is easy to complete.',
						'wc-calypso-bridge'
					) }
					secondQuestion={ __(
						'The store setup process meets my needs.',
						'wc-calypso-bridge'
					) }
					defaultScore={ score }
					/*
					// @ts-expect-error: package isn't up to date, but external package is; ignoring for now */
					recordScoreCallback={ recordModalScore }
					onCloseModal={ () => {
						setScore( NaN );
						setShowCesModal( false );
					} }
				/>
			) : null }
		</>
	);
};
