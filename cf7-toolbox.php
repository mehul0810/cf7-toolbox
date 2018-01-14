<?php
/**
 * Plugin Name: Contact Form 7 ToolBox
 * Plugin URI: https://www.mehulgohil.in/plugins/contact-form-7-toolbox/
 * Version: 1.0
 * Description: An Intuitive ToolBox containing awesome functionality of contact form 7.
 * Author: Mehul Gohil
 * Author URI: https://www.mehulgohil.in/
 * License: GPLv3
 * TextDomain: cf7-toolbox
 * GitHub Plugin URI: https://github.com/mehul0810/cf7-toolbox/
 *
 * Contact Form 7 ToolBox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Contact Form 7 ToolBox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Contact Form 7 ToolBox. If not, see <https://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CF7_Toolbox' ) ) :

	class CF7_Toolbox {

		/** Singleton *************************************************************/

		/**
		 * CF7 ToolBox Instance
		 *
		 * @since  1.0
		 * @access protected
		 *
		 * @var    CF7_Toolbox()
		 */
		protected static $_instance;

		/**
		 * Main CF7 ToolBox Instance
		 *
		 * Ensures that only one instance exists.
		 *
		 * @since     1.0
		 * @access    public
		 *
		 * @static
		 * @see       CF7_Toolbox()
		 *
		 * @return    CF7_Toolbox
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Contact Form 7 - ToolBox Constructor.
		 *
		 * @since 1.0
		 */
		public function __construct() {

			$this->setup_constants();

			// Display Minimum PHP Version Notice, if PHP version not supported.
			if ( function_exists( 'phpversion' ) && version_compare( CF7_TOOLBOX_REQUIRED_PHP_VERSION, phpversion(), '>' ) ) {
				add_action( 'admin_notices', array( $this, 'minimum_phpversion_notice' ) );
				return;
			}

			$this->includes();
			$this->initialize_hooks();
			$this->load_textdomain();

		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object, therefore we don't want the object to be cloned.
		 *
		 * @since  1.0
		 * @access protected
		 *
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'cf7-toolbox' ), '1.0' );
		}

		/**
		 * Disable un-serializing of the class
		 *
		 * @since  1.0
		 * @access protected
		 *
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'cf7-toolbox' ), '1.0' );
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.0
		 */
		private function initialize_hooks() {
			//register_activation_hook( CF7_TOOLBOX_PLUGIN_FILE, array( $this, 'activate' ) );
			add_action( 'admin_init', array( $this, '__is_parent_plugin_active' ) );
		}

		/**
		 * Setup plugin constants
		 *
		 * @since  1.0
		 * @access private
		 *
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version.
			if ( ! defined( 'CF7_TOOLBOX_VERSION' ) ) {
				define( 'CF7_TOOLBOX_VERSION', '1.0' );
			}

			// Minimum PHP version.
			if ( ! defined( 'CF7_TOOLBOX_REQUIRED_PHP_VERSION' ) ) {
				define( 'CF7_TOOLBOX_REQUIRED_PHP_VERSION', '5.3' );
			}

			// Plugin Root File.
			if ( ! defined( 'CF7_TOOLBOX_PLUGIN_FILE' ) ) {
				define( 'CF7_TOOLBOX_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Folder Path.
			if ( ! defined( 'CF7_TOOLBOX_PLUGIN_DIR' ) ) {
				define( 'CF7_TOOLBOX_PLUGIN_DIR', plugin_dir_path( CF7_TOOLBOX_PLUGIN_FILE ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'CF7_TOOLBOX_PLUGIN_URL' ) ) {
				define( 'CF7_TOOLBOX_PLUGIN_URL', plugin_dir_url( CF7_TOOLBOX_PLUGIN_FILE ) );
			}

			// Plugin Basename.
			if ( ! defined( 'CF7_TOOLBOX_PLUGIN_BASENAME' ) ) {
				define( 'CF7_TOOLBOX_PLUGIN_BASENAME', plugin_basename( CF7_TOOLBOX_PLUGIN_FILE ) );
			}

		}

		/**
		 * Include required files
		 *
		 * @since  1.0
		 * @access private
		 *
		 * @return void
		 */
		private function includes() {

			require_once CF7_TOOLBOX_PLUGIN_DIR . 'modules/cf7-widget.php';

			// If Gutenberg is activated, then display gutenberg block for contact form 7.
			if(
				current_user_can( 'activate_plugins' ) &&
				defined( 'GUTENBERG_VERSION' )
			) {
				require_once CF7_TOOLBOX_PLUGIN_DIR . 'modules/gutenberg/index.php';
			}
		}

		/**
		 * Check whether the parent plugin is active or not.
		 *
		 * Note: This function is for internal purposes only.
		 *
		 * @since 1.0
		 */
		public function __is_parent_plugin_active() {

			if (
				current_user_can( 'activate_plugins' ) &&
				! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )
			) {

				// Display Plugin Activation Error Notice.
				add_action( 'admin_notices', function() {
					echo sprintf(
						'<div class="notice notice-error"><p>%1$s</p></div>',
						sprintf(
							__( '<a href="%1$s" target="_blank">Contact Form 7</a> Plugin must be activated before activating CF7 - ToolBox Plugin.', 'cf7-toolbox' ),
							esc_url( 'https://wordpress.org/plugins/contact-form-7/' )
						)
					);
				});

				// Deactivate CF7 - ToolBox.
				deactivate_plugins( CF7_TOOLBOX_PLUGIN_BASENAME );

				// Remove Plugin Activation Notice.
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}

				return false;

			}

		}

		/**
		 * Loads the plugin language files.
		 *
		 * @since  1.0
		 * @access public
		 *
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for CF7 Toolbox's languages directory.
			$lang_dir = CF7_TOOLBOX_PLUGIN_BASENAME . '/languages/';
			$lang_dir = apply_filters( 'cf7_toolbox_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'cf7-toolbox' );

			unload_textdomain( 'cf7-toolbox' );
			load_textdomain( 'cf7-toolbox', WP_LANG_DIR . '/cf7-toolbox/cf7-toolbox-' . $locale . '.mo' );
			load_plugin_textdomain( 'cf7-toolbox', false, $lang_dir );

		}


		/**
		 * Display Minimum PHP Version Notice.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function minimum_phpversion_notice() {

			// Bailout.
			if ( ! is_admin() ) {
				return;
			}

			$notice = '<p><strong>' . __( 'Your site could be faster and more secure with a newer PHP version.', 'cf7-toolbox' ) . '</strong></p>';
			$notice .= '<p>' . __( 'Hey, we\'ve noticed that you\'re running an outdated version of PHP. PHP is the programming language that WordPress and Contact Form 7 - ToolBox are built on. The version that is currently used for your site is no longer supported. Newer versions of PHP are both faster and more secure. In fact, your version of PHP no longer receives security updates, which is why we\'re sending you this notice.', 'cf7-toolbox' ) . '</p>';
			$notice .= '<p>' . __( 'Hosts have the ability to update your PHP version, but sometimes they don\'t dare to do that because they\'re afraid they\'ll break your site.', 'cf7-toolbox' ) . '</p>';
			$notice .= '<p><strong>' . __( 'To which version should I update?', 'cf7-toolbox' ) . '</strong></p>';
			$notice .= '<p>' . __( 'You should update your PHP version to either 5.6 or to 7.0 or 7.1. On a normal WordPress site, switching to PHP 5.6 should never cause issues. We would however actually recommend you switch to PHP7. There are some plugins that are not ready for PHP7 though, so do some testing first. PHP7 is much faster than PHP 5.6. It\'s also the only PHP version still in active development and therefore the better option for your site in the long run.', 'cf7-toolbox' ) . '</p>';
			$notice .= '<p><strong>' . __( 'Can\'t update? Ask your host!', 'cf7-toolbox' ) . '</strong></p>';
			$notice .= '<p>' . sprintf( __( 'If you cannot upgrade your PHP version yourself, you can send an email to your host. If they don\'t want to upgrade your PHP version, we would suggest you switch hosts. Have a look at one of the recommended %1$sWordPress hosting partners%2$s.', 'cf7-toolbox' ), sprintf( '<a href="%1$s" target="_blank">', esc_url( 'https://wordpress.org/hosting/' ) ), '</a>' ) . '</p>';

			echo sprintf(
				'<div class="notice notice-error">%1$s</div>',
				$notice
			);
		}

	}

endif;

function initialize_cf7_toolbox() {
	return CF7_ToolBox::instance();
}

add_action( 'plugins_loaded', 'initialize_cf7_toolbox' );