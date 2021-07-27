/**
 * External dependencies.
 */
import { Icon, help } from '@wordpress/icons';

/**
 * Internal dependencies.
 */
import strings from './strings';

const FrequentlyAskedQuestions = () => {
	return (
		<>
			<h2>{strings.faq.faqHeader}</h2>
			<h3>{strings.faq.question1}</h3>
			<p>{strings.faq.question1Answer1}</p>
			<p>{strings.faq.question1Answer2}</p>

			<h3>{strings.faq.question2}</h3>
			<p>{strings.faq.question2Answer1}</p>
			<h4>{strings.faq.question2Answer2}</h4>
			<ul>
				<li>
					{strings.faq.question2Answer3}
					<ul>
						<li>{strings.faq.question2Answer4}</li>
						<li>{strings.faq.question2Answer5}</li>
					</ul>
				</li>
				<li>{strings.faq.question2Answer6}</li>
				<li>{strings.faq.question2Answer7}</li>
			</ul>
			{strings.faq.question2Answer8}
			<h3>{strings.faq.question3}</h3>
			<p>{strings.faq.question3Answer1}</p>
			<p>{strings.faq.question3Answer2}</p>
			<p>{strings.faq.question3Answer3}</p>
			<p>{strings.faq.question3Answer4}</p>
			<p>{strings.faq.question3Answer5}</p>
			<h3>{strings.faq.question4}</h3>
			{strings.faq.question4Answer1}
			<ul>
				<li>{strings.faq.question4Answer2}</li>
				<li>{strings.faq.question4Answer3}</li>
				<li>{strings.faq.question4Answer4}</li>
				<li>{strings.faq.question4Answer5}</li>
				<li>{strings.faq.question4Answer6}</li>
				<li>{strings.faq.question4Answer7}</li>
				<li>{strings.faq.question4Answer8}</li>
				<li>{strings.faq.question4Answer9}</li>
				<li>{strings.faq.question4Answer10}</li>
			</ul>
			<p>{strings.faq.question4Answer11}</p>
			<p>{strings.faq.question4Answer12}</p>
			<p>{strings.faq.question4Answer13}</p>
			<h3>{strings.faq.question5}</h3>
			<p>{strings.faq.question5Answer1}</p>
			<div className="help-section">
				<Icon icon={help} />
				<span>{strings.faq.haveMoreQuestions}</span>
				<a
					href="https://www.woocommerce.com/my-account/tickets/"
					target="_blank"
				>
					{strings.faq.getInTouch}
				</a>
			</div>
		</>
	);
};

export default FrequentlyAskedQuestions;
