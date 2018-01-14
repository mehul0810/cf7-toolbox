<?php
/**
 * CF7 Toolbox - Widget
 *
 * Widget Module for placing Contact Form 7 in widget area.
 *
 * @package CF7 - ToolBox
 * @license https://opensource.org/licenses/gpl-license GNU Public License
 * @since   1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if( ! class_exists( 'CF7_Toolbox_Widget' ) ) :

	class CF7_Toolbox_Widget extends WP_Widget {

		/**
		 * CF7_Toolbox_Widget constructor.
		 *
		 * @since 1.0
		 */
		public function __construct() {

			add_action( 'widgets_init', array( $this, 'initialize_widget' ) );

			parent::__construct(
				'cf7_toolbox_widget',
				__( 'CF7 ToolBox - Widget', 'cf7-toolbox' ),
				array(
					'description' => __( 'Display Contact Form 7 Form in Widget Area.', 'cf7-toolbox' ),
				)
			);
		}

		/**
		 * Initialise and Register Widget.
		 *
		 * @since 1.0
		 */
		public function initialize_widget() {
			register_widget( 'CF7_Toolbox_Widget' );
		}

		/**
		 * Create Form for Widget.
		 *
		 * @param array $instance Current Widget Instance.
		 *
		 * @since 1.0
		 *
		 * @return string|void
		 */
		public function form( $instance ) {

			$title   = ! empty( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
			$form_id = ! empty( $instance['form'] ) ? (int) $instance['form'] : '';

			$args = array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => - 1,
				'orderby'        => 'ID',
				'order'          => 'DESC'
			);

			$forms = new WP_Query( $args );
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title', 'cf7-toolbox' ); ?>
				</label>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'title' ); ?>"
				       name="<?php echo $this->get_field_name( 'title' ); ?>"
				       type="text"
				       value="<?php echo $title; ?>"
				/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'form' ); ?>">
					<?php _e( 'Select Contact Form', 'cf7-toolbox' ); ?>
				</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'form' ); ?>" name="<?php echo $this->get_field_name( 'form' ); ?>">
					<?php
					if ( $forms->have_posts() ) {
						while ( $forms->have_posts() ) {
							$forms->the_post();

							echo sprintf(
								'<option %1$s value="%2$s">%3$s</option>',
								( $form_id === get_the_ID() ) ? 'selected="selected"' : '',
								get_the_ID(),
								get_the_title()
							);
						}
					} else {
						echo sprintf(
							'<option value="%1$s">%2$s</option>',
							0,
							__( 'No Contact Forms Found.', 'cf7-toolbox' )
						);
					}
					wp_reset_postdata();
					?>
				</select>
			</p>
			<?php
		}

		/**
		 * Display Contact Form in front end.
		 *
		 * @param array $args     Widgets Arguments.
		 * @param array $instance Widget Instance.
		 *
		 * @since 1.0
		 */
		public function widget( $args, $instance ) {

			$html  = '';
			$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$form  = ! empty( $instance['form'] ) ? $instance['form'] : '';

			$html .= '<div class="cf7-toolbox-widget-wrap">';
			$html .= $args['before_widget'];
			if ( $title ) {
				$html .= $args['before_title'] . $title . $args['after_title'];
			}

			if ( ! empty( $form ) && $form != 'none' ) {
				$html .= '<div class="cf7-toolbox-widget contact-form">' . do_shortcode( '[contact-form-7 id="' . $form . '"]' ) . '</div>';
			}
			$html .= $args['after_widget'];
			$html .= '</div>';

			echo $html;
		}

	}

	new CF7_Toolbox_Widget();

endif;