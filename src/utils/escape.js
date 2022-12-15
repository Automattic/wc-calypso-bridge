export default function escape( s ) {
	return ( '' + s ) /* Forces the conversion to string. */
		.replace( /\\/g, '\\\\' ) /* This MUST be the 1st replacement. */
		.replace( /\t/g, '\\t' ) /* These 2 replacements protect whitespaces. */
		.replace( /\n/g, '\\n' )
		.replace(
			/\u00A0/g,
			'\\u00A0'
		) /* Useful but not absolutely necessary. */
		.replace(
			/&/g,
			'\\x26'
		) /* These 5 replacements protect from HTML/XML. */
		.replace( /'/g, '\\x27' )
		.replace( /"/g, '\\x22' )
		.replace( /</g, '\\x3C' )
		.replace( />/g, '\\x3E' );
}
