/**
 * BLOCK: CF7 Toolbox - ShortCode.
 *
 * @package CF7 - ToolBox
 * @license https://opensource.org/licenses/gpl-license GNU Public License
 * @since   1.0
 */
( function() {
	let __ = wp.i18n.__;
	let el = wp.element.createElement;
	let registerBlockType = wp.blocks.registerBlockType;
	let withAPIData = wp.components.withAPIData;

	/**
	 * Register CF7 ToolBox Block.
	 *
	 * @since 1.0
	 */
	registerBlockType(
		'cf7-toolbox/contact-form',
		{
			title: __( 'Contact Form 7', 'cf7-toolbox' ),
			icon: 'email',
			category: 'widgets',
			edit: withAPIData( function() {
				return {
					cf7forms: '/wp/v2/cf7toolbox'
				};
			} )( function( props ) {

				let select_options = [];
				let form_id = props.attributes.form_id;

				// If data doesn't existing then show loading.
				if ( ! props.cf7forms.data ) {
					return __( 'Loading...', 'cf7-toolbox' );
				}

				// If Contact Forms doesn't exist then show appropriate message.
				if ( 0 === props.cf7forms.data.length ) {
					return __( 'No Contact Forms found.', 'cf7-toolbox' );
				}

				select_options.push(
					el(
						'option',
						{ value: 0 },
						__( 'Select Contact Form', 'cf7-toolbox' )
					)
				);

				// Loop through Contact Form 7 Data.
				for( let count in props.cf7forms.data ) {

					select_options.push(
						el(
							'option',
							{
								value: props.cf7forms.data[ count ].id
							},
							props.cf7forms.data[ count ].title.rendered
						)
					);
				}



				function getContactFormID( event ) {
					let selected = event.target.querySelector( 'option:checked' );
					props.setAttributes( { form_id: selected.value } );
					event.preventDefault();
				}

				return [
					el(
						'div',
						{ className: 'components-placeholder', icon: props.icon },
						[
							el(
								'div',
								{ className: '', align: 'center' },
								el(
									'span',
									{ className: 'dashicons dashicons-email' }
								)
							),
							el(
								'div',
								{ className: 'components-placeholder__instructions', align: 'center' },
								__( 'Select the contact form to display','cf7-toolbox' )
							),
							el(
								'div',
								{ className: props.className, align: 'center' },
								el(
									'select',
									{
										className: props.className,
										onChange: getContactFormID,
									},
									select_options
								)
							)
						]
					)
				]

			} ),
			save: function( props ) {
				return ( props.attributes.form_id );
			},
		}
	);
})();