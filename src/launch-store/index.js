/**
 * External dependencies
 */
import classNames from 'classnames';
import {
	Card,
	CardBody,
	CardHeader,
	CardFooter,
	Button,
	Spinner,
} from '@wordpress/components';
import {
	createInterpolateElement,
	useState,
	Fragment,
} from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { Text } from '@woocommerce/experimental';
import { Link } from '@woocommerce/components';
import { getAdminLink } from '@woocommerce/settings';
import { ONBOARDING_STORE_NAME } from '@woocommerce/data';

/**
 * Internal dependencies
 */
import escape from '../utils/escape';
import Confetti from './images/confetti';
import CardIcon from './images/card_icon';
import GlobeIcon from './images/globe_icon';
import ProductsIcon from './images/products_icon';
import LockIcon from './images/lock';
import CopyIcon from './images/copy';
import './style.scss';

const Loader = () => {
	return (
		<div className="wpcom-wc-admin-loader">
			<svg
				className="wpcom-site__logo"
				height="72"
				width="72"
				viewBox="0 0 72 72"
			>
				<path d="M36,0C16.1,0,0,16.1,0,36c0,19.9,16.1,36,36,36c19.9,0,36-16.2,36-36C72,16.1,55.8,0,36,0z M3.6,36 c0-4.7,1-9.1,2.8-13.2l15.4,42.3C11.1,59.9,3.6,48.8,3.6,36z M36,68.4c-3.2,0-6.2-0.5-9.1-1.3l9.7-28.2l9.9,27.3 c0.1,0.2,0.1,0.3,0.2,0.4C43.4,67.7,39.8,68.4,36,68.4z M40.5,20.8c1.9-0.1,3.7-0.3,3.7-0.3c1.7-0.2,1.5-2.8-0.2-2.7 c0,0-5.2,0.4-8.6,0.4c-3.2,0-8.5-0.4-8.5-0.4c-1.7-0.1-2,2.6-0.2,2.7c0,0,1.7,0.2,3.4,0.3l5,13.8L28,55.9L16.2,20.8 c2-0.1,3.7-0.3,3.7-0.3c1.7-0.2,1.5-2.8-0.2-2.7c0,0-5.2,0.4-8.6,0.4c-0.6,0-1.3,0-2.1,0C14.7,9.4,24.7,3.6,36,3.6 c8.4,0,16.1,3.2,21.9,8.5c-0.1,0-0.3,0-0.4,0c-3.2,0-5.4,2.8-5.4,5.7c0,2.7,1.5,4.9,3.2,7.6c1.2,2.2,2.7,4.9,2.7,8.9 c0,2.8-0.8,6.3-2.5,10.5l-3.2,10.8L40.5,20.8z M52.3,64l9.9-28.6c1.8-4.6,2.5-8.3,2.5-11.6c0-1.2-0.1-2.3-0.2-3.3 c2.5,4.6,4,9.9,4,15.5C68.4,47.9,61.9,58.4,52.3,64z"></path>
			</svg>
		</div>
	);
};

const CopyButton = ( { contentToCopy } ) => {
	const [ isFeedbackVisible, setIsFeedbackVisible ] = useState( false );

	const handleClick = () => {
		window.navigator.clipboard.writeText( contentToCopy );
		setIsFeedbackVisible( true );
		setTimeout( () => {
			setIsFeedbackVisible( false );
		}, 1000 );
	};

	const classes = classNames( 'copy-to-clipboard__feedback', {
		'copy-to-clipboard__feedback--active': isFeedbackVisible,
	} );

	return (
		<div className="copy-to-clipboard" onClick={ handleClick }>
			<CopyIcon />
			<div className={ classes }>
				{ __( 'Copied', 'wc-calypso-bridge' ) }
			</div>
		</div>
	);
};

const LaunchButton = ( {
	label,
	loadingLabel,
	successCallback = () => {},
} ) => {
	const [ loading, setLoading ] = useState( false );
	const [ errorMessage, setErrorMessage ] = useState( '' );

	const makeAjaxRequest = (
		method,
		url,
		contentType,
		body = null,
		callback = null
	) => {
		const xhr = new window.XMLHttpRequest();
		xhr.open( method, url, true );
		xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
		if ( contentType ) {
			xhr.setRequestHeader( 'Content-Type', contentType );
		}
		xhr.withCredentials = true;
		if ( callback ) {
			xhr.onreadystatechange = function () {
				callback( xhr );
			};
		}
		xhr.send( body );
	};

	const doLaunch = async () => {
		if ( loading ) {
			return;
		}

		setLoading( true );
		setErrorMessage( '' );

		makeAjaxRequest(
			'POST',
			window.ajaxurl,
			'application/x-www-form-urlencoded; charset=UTF-8',
			'action=launch_store',
			function ( xhr ) {
				try {
					if ( xhr.readyState === window.XMLHttpRequest.DONE ) {
						if ( xhr.status === 200 && xhr.responseText ) {
							successCallback();
						} else {
							const response = JSON.parse( xhr.responseText );
							setErrorMessage(
								escape( response.data[ 0 ].message )
							);
						}
					}
				} catch ( error ) {
					// Silently fail.
				}

				setLoading( false );
			}
		);
	};

	if ( ! loadingLabel ) {
		loadingLabel = __( 'Launching your store', 'wc-calypso-bridge' );
	}

	const btnClass = classNames( 'woocommerce-launch-store__button', {
		'woocommerce-launch-store__button--loading': loading,
	} );

	const btnText = loading ? loadingLabel : label;

	return (
		<Fragment>
			<Button isPrimary className={ btnClass } onClick={ doLaunch }>
				{ loading && <Spinner /> }
				{ btnText || __( 'Launch your store', 'wc-calypso-bridge' ) }
			</Button>
			{ errorMessage && (
				<p className="woocommerce-launch-store__button__error">
					{ errorMessage }
				</p>
			) }
		</Fragment>
	);
};

const Congratulations = () => {
	const siteSlug = escape( window.wcCalypsoBridge.siteSlug );
	const siteUrl = escape( window.wcCalypsoBridge.homeUrl );

	return (
		<Card className="woocommerce-task-card woocommerce-task-card__congratulations">
			<CardBody>
				<div className="woocommerce-task-card__congratulations__confetti">
					<Confetti />
				</div>
				<div className="woocommerce-task-card__congratulations__text">
					<Text
						variant="title.large"
						as="h2"
						className="woocommerce-task-card__title"
					>
						{ __( 'Woo! You did it!', 'wc-calypso-bridge' ) }
					</Text>

					<Text as="span">
						{ __(
							'Congratulations on launching your WooCommerce store. Take a moment to celebrate and share the news!',
							'wc-calypso-bridge'
						) }
					</Text>
				</div>
				<div className="woocommerce-task-card__congratulations__address-bar">
					<LockIcon />
					<span>{ siteUrl }</span>
					<div className="woocommerce-task-card__congratulations__address-bar__actions">
						<CopyButton contentToCopy={ siteUrl } />
					</div>
				</div>
				<div className="woocommerce-task-card__congratulations__links">
					<Button
						isPrimary
						onClick={ () => {
							window.location = siteUrl;
						} }
					>
						{ __( 'View your store', 'wc-calypso-bridge' ) }
					</Button>
				</div>
				<div className="woocommerce-task-card__congratulations__text woocommerce-task-card__congratulations__text--footer">
					<Text as="span">
						{ createInterpolateElement(
							__(
								'Changed your mind? You can make your store private again by updating your <a>Privacy</a> settings.',
								'wc-calypso-bridge'
							),
							{
								a: (
									// eslint-disable-next-line jsx-a11y/anchor-has-content
									<a
										href={
											'https://wordpress.com/settings/general/' +
											siteSlug +
											'#site-privacy-settings'
										}
									/>
								),
							}
						) }
					</Text>
				</div>
			</CardBody>
		</Card>
	);
};

const ReadyToLaunch = ( { launchHandler } ) => {
	const siteSlug = escape( window.wcCalypsoBridge.siteSlug );

	return (
		<Card className="woocommerce-task-card woocommerce-task-card__ready-to-launch">
			<CardBody>
				<Text
					variant="title.large"
					as="h2"
					className="woocommerce-task-card__title"
				>
					{ __( 'Ready to launch your store?', 'wc-calypso-bridge' ) }
				</Text>
				<Text as="span">
					{ __(
						"It's time to celebrate – you're ready to launch your store! Woo!",
						'wc-calypso-bridge'
					) }
				</Text>
				<div className="woocommerce-task-card__ready-to-launch__links">
					<LaunchButton successCallback={ launchHandler } />
				</div>
			</CardBody>
			<CardFooter>
				<Text as="span">
					{ createInterpolateElement(
						__(
							'You can always revert this under <a>Settings</a>.',
							'wc-calypso-bridge'
						),
						{
							a: (
								// eslint-disable-next-line jsx-a11y/anchor-has-content
								<a
									href={
										'https://wordpress.com/settings/general/' +
										siteSlug +
										'#site-privacy-settings'
									}
								/>
							),
						}
					) }
				</Text>
			</CardFooter>
		</Card>
	);
};

const BeforeLaunch = ( { tasks, launchHandler } ) => {
	const siteSlug = escape( window.wcCalypsoBridge.siteSlug );

	const pendingTasks = tasks.map( ( task ) => {
		const { id, title, content, actionUrl, actionLabel } = task;

		let Icon = CardIcon;
		let customTitle = title;
		let customContent = content;
		let customActionLabel = actionLabel;

		// Create custom copy for each task.
		switch ( id ) {
			case 'payments':
				Icon = CardIcon;
				customTitle = __( 'Get paid', 'wc-calypso-bridge' );
				customContent = __(
					'Give your customers an easy and convenient way to pay! Set up one (or more!) of our fast and secure online or in person payment methods.',
					'wc-calypso-bridge'
				);
				customActionLabel = __( 'Get paid', 'wc-calypso-bridge' );
				break;
			case 'products':
				Icon = ProductsIcon;
				customTitle = __( 'List your products', 'wc-calypso-bridge' );
				customContent = __(
					'Start selling by adding products or services to your store. Create your products manually, or import them from an existing store.',
					'wc-calypso-bridge'
				);
				customActionLabel = __( 'List products', 'wc-calypso-bridge' );
				break;
			case 'add_domain':
				Icon = GlobeIcon;
				customContent = __(
					'Choose an address for your new website or transfer a domain you already own.',
					'wc-calypso-bridge'
				);
				customActionLabel = __( 'Add a domain', 'wc-calypso-bridge' );
				break;
		}

		const className =
			'woocommerce-task-card__pending-tasks__task woocommerce-task-card__pending-tasks__task-' +
			id;
		return (
			<div className={ className } key={ id }>
				<div className="woocommerce-task-card__pending-tasks__task__content">
					<div className="woocommerce-task-card__pending-tasks__task__icon">
						<Icon />
					</div>
					<div className="woocommerce-task-card__pending-tasks__task__text">
						<Text variant="title.large" as="h3">
							{ customTitle }
						</Text>
						<Text as="span">{ customContent }</Text>
					</div>
				</div>
				<div className="woocommerce-task-card__pending-tasks__task__link">
					{ actionUrl && (
						<Link
							href={ actionUrl }
							className="components-button is-secondary"
							type={
								actionUrl.indexOf( 'page=wc-admin' ) !== -1
									? 'wc-admin'
									: 'wp-admin'
							}
						>
							{ customActionLabel }
						</Link>
					) }
					{ ! actionUrl && (
						<Link
							className="components-button is-secondary"
							href={ getAdminLink(
								`admin.php?page=wc-admin&task=${ id }`
							) }
						>
							{ customActionLabel }
						</Link>
					) }
				</div>
			</div>
		);
	} );

	return (
		<Card className="woocommerce-task-card woocommerce-task-card__pending-tasks">
			<CardHeader>
				<Text
					variant="title.large"
					as="h2"
					className="woocommerce-task-card__title"
				>
					{ __( 'Before you launch', 'wc-calypso-bridge' ) }
				</Text>
				<Text as="span">
					{ __(
						'A few things to check before launching your store',
						'wc-calypso-bridge'
					) }
				</Text>
			</CardHeader>
			<CardBody>{ pendingTasks }</CardBody>
			<CardFooter>
				<div className="woocommerce-task-card__pending-tasks__links">
					<LaunchButton
						label={ __( 'Launch anyway', 'wc-calypso-bridge' ) }
						successCallback={ launchHandler }
					/>
				</div>
				<Text as="span">
					{ createInterpolateElement(
						__(
							'You can always revert this under <a>Settings</a>.',
							'wc-calypso-bridge'
						),
						{
							a: (
								// eslint-disable-next-line jsx-a11y/anchor-has-content
								<a
									href={
										'https://wordpress.com/settings/general/' +
										siteSlug +
										'#site-privacy-settings'
									}
								/>
							),
						}
					) }
				</Text>
			</CardFooter>
		</Card>
	);
};

const LaunchStorePage = ( { onComplete, query } ) => {
	const { isResolving, taskLists } = useSelect( ( select ) => {
		return {
			isResolving: ! select(
				ONBOARDING_STORE_NAME
			).hasFinishedResolution( 'getTaskLists' ),
			taskLists: select( ONBOARDING_STORE_NAME ).getTaskLists(),
		};
	} );

	if ( query.status && query.status === 'success' ) {
		return (
			<div className="woocommerce-launch-store">
				<Congratulations />
			</div>
		);
	}

	if ( isResolving ) {
		return <Loader />;
	}

	const setupList = taskLists.filter( ( list ) => list.id === 'setup' ).pop();
	const crucialTasks = [ 'payments', 'products', 'add_domain' ];
	const pendingTasks = setupList.tasks.filter(
		( task ) =>
			task.canView === true &&
			task.isComplete === false &&
			crucialTasks.includes( task.id )
	);
	const hasPendingCrucialTasks = pendingTasks.length;
	const hasPendingTasks = setupList.tasks.filter(
		( task ) => task.canView === true && task.isComplete === false
	).length;

	const launchHandler = () => {
		if ( hasPendingTasks ) {
			const redirectPath =
				'admin.php?page=wc-admin&task=launch_site&status=success';
			onComplete( {
				redirectPath,
			} );
		} else {
			onComplete();
		}
	};

	return (
		<div className="woocommerce-launch-store">
			{ ! hasPendingCrucialTasks && (
				<ReadyToLaunch launchHandler={ launchHandler } />
			) }
			{ hasPendingCrucialTasks && (
				<BeforeLaunch
					tasks={ pendingTasks }
					launchHandler={ launchHandler }
				/>
			) }
		</div>
	);
};
export default LaunchStorePage;
