<?php
/**
 * Plugin Name: CPT Locations
 * Plugin URI: https://github.com/VLSLgithub/cpt-facilities
 * Description: Creates the Locations CPT
 * Version: 0.1.0
 * Text Domain: cpt-facilities
 * Author: Eric Defore
 * Author URI: https://realbigmarketing.com/
 * Contributors: d4mation
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CPT_Facilities' ) ) {

	/**
	 * Main CPT_Facilities class
	 *
	 * @since	  1.0.0
	 */
	class CPT_Facilities {
		
		/**
		 * @var			CPT_Facilities $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;
		
		/**
		 * @var			CPT_Facilities $admin_errors Stores all our Admin Errors to fire at once
		 * @since		1.0.0
		 */
		private $admin_errors;

		/**
		 * Get active instance
		 *
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  object self::$instance The one true CPT_Facilities
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			if ( version_compare( get_bloginfo( 'version' ), '4.4' ) < 0 ) {
				
				$this->admin_errors[] = sprintf( _x( '%s requires v%s of %s or higher to be installed!', 'Outdated Dependency Error', 'cpt-facilities' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '4.4', '<a href="' . admin_url( 'update-core.php' ) . '"><strong>WordPress</strong></a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			if ( ! class_exists( 'RBM_CPTS' ) ||
				! class_exists( 'RBM_FieldHelpers' ) ) {
				
				$this->admin_errors[] = sprintf( _x( 'To use the %s Plugin, both %s and %s must be active as either a Plugin or a Must Use Plugin!', 'Missing Dependency Error', 'cpt-facilities' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '<a href="//github.com/realbig/rbm-field-helpers-wrapper/" target="_blank">' . __( 'RBM Field Helpers', 'cpt-facilities' ) . '</a>', '<a href="//github.com/realbig/rbm-cpts/" target="_blank">' . __( 'RBM Custom Post Types', 'cpt-facilities' ) . '</a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			$this->require_necessities();
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );

			if ( ! defined( 'CPT_Facilities_VER' ) ) {
				// Plugin version
				define( 'CPT_Facilities_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'CPT_Facilities_DIR' ) ) {
				// Plugin path
				define( 'CPT_Facilities_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'CPT_Facilities_URL' ) ) {
				// Plugin URL
				define( 'CPT_Facilities_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'CPT_Facilities_FILE' ) ) {
				// Plugin File
				define( 'CPT_Facilities_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language directory
			$lang_dir = CPT_Facilities_DIR . '/languages/';
			$lang_dir = apply_filters( 'cpt_facilities_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'cpt-facilities' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'cpt-facilities', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/cpt-facilities/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/cpt-facilities/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( 'cpt-facilities', $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/cpt-facilities/languages/ folder
				load_textdomain( 'cpt-facilities', $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( 'cpt-facilities', false, $lang_dir );
			}

		}
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function require_necessities() {
			
			require_once CPT_Facilities_DIR . '/core/cpt/class-cpt-facilities.php';
			
		}
		
		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  HTML
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  void
		 */
		public function register_scripts() {
			
			wp_register_style(
				'cpt-facilities',
				CPT_Facilities_URL . 'assets/css/style.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Facilities_VER
			);
			
			wp_register_script(
				'cpt-facilities',
				CPT_Facilities_URL . 'assets/js/script.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Facilities_VER,
				true
			);
			
			wp_localize_script( 
				'cpt-facilities',
				'cPTFacilities',
				apply_filters( 'cpt_facilities_localize_script', array() )
			);
			
			wp_register_style(
				'cpt-facilities-admin',
				CPT_Facilities_URL . 'assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Facilities_VER
			);
			
			wp_register_script(
				'cpt-facilities-admin',
				CPT_Facilities_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Facilities_VER,
				true
			);
			
			wp_localize_script( 
				'cpt-facilities-admin',
				'cPTFacilities',
				apply_filters( 'cpt_facilities_localize_admin_script', array() )
			);
			
		}
		
	}
	
} // End Class Exists Check

/**
 * The main function responsible for returning the one true CPT_Facilities
 * instance to functions everywhere
 *
 * @since	  1.0.0
 * @return	  \CPT_Facilities The one true CPT_Facilities
 */
add_action( 'plugins_loaded', 'cpt_facilities_load', 11 );
function cpt_facilities_load() {

	require_once __DIR__ . '/core/cpt-facilities-functions.php';
	CPTFACILITIES();

}
