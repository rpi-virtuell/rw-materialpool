<?php

/**
 * RW Materialpool
 *
 * @package   Materialpool
 * @author    Frank Staude
 * @license   GPL-2.0+
 * @link      https://github.com/rpi-virtuell/rw-materialpool
 */

/*
 * Plugin Name:       RW Materialpool
 * Plugin URI:        https://github.com/rpi-virtuell/rw-materialpool
 * Description:       RPI Virtuell Materialpool
 * Version:           0.0.1
 * Author:            Frank Staude
 * Author URI:        https://staude.net
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 * Text Domain:       rw-materialpool
 * GitHub Plugin URI: https://github.com/rpi-virtuell/rw-materialpool
 * GitHub Branch:     master
 * Requires WP:       4.0
 * Requires PHP:      5.3
 */

class Materialpool {
	/**
	 * Plugin version
	 *
	 * @var     string
	 * @since   0.0.1
	 * @access  public
	 */
	static public $version = "0.0.1";

	/**
	 * Singleton object holder
	 *
	 * @var     mixed
	 * @since   0.0.1
	 * @access  private
	 */
	static private $instance = NULL;

	/**
	 * @var     mixed
	 * @since   0.0.1
	 * @access  public
	 */
	static public $plugin_name = NULL;

	/**
	 * @var     mixed
	 * @since   0.0.1
	 * @access  public
	 */
	static public $textdomain = NULL;

	/**
	 * @var     mixed
	 * @since   0.0.1
	 * @access  public
	 */
	static public $plugin_base_name = NULL;

    /**
     * @var     mixed
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_base_dir = NULL;

	/**
	 * @var     mixed
	 * @since   0.0.1
	 * @access  public
	 */
	static public $plugin_url = NULL;

	/**
	 * @var     string
	 * @since   0.0.1
	 * @access  public
	 */
	static public $plugin_filename = __FILE__;

	/**
	 * @var     string
	 * @since   0.0.1
	 * @access  public
	 */
	static public $plugin_version = '';

    /**
     * Plugin version
     *
     * @var     string
     * @since   0.0.1
     * @access  public
     */
    static public $buddypress_member_url = "http://gruppen.rpi-virtuell.de/members/";

	/**
	 * Plugin constructor.
	 *
	 * @since   0.0.1
	 * @access  public
	 * @uses    plugin_basename
	 * @action  materialpool_init
	 */
	public function __construct () {
		// set the textdomain variable
		self::$textdomain = self::get_textdomain();

        self::$plugin_url = plugin_dir_url( __FILE__ );

		// The Plugins Name
		self::$plugin_name = $this->get_plugin_header( 'Name' );

		// The Plugins Basename
		self::$plugin_base_name = plugin_basename( __FILE__ );

        // The Plugin Dir Path
        self::$plugin_base_dir = plugin_dir_path( __FILE__ );

		// The Plugins Version
		self::$plugin_version = $this->get_plugin_header( 'Version' );

		// Load the textdomain
		$this->load_plugin_textdomain();

		// Register Stylesheets
        add_action( 'admin_enqueue_scripts', array( 'Materialpool', 'register_admin_plugin_styles' ) );

        // Add Filter & Actions for Dashboard
		add_action( 'admin_menu', array( 'Materialpool_Dashboard', 'register_dashboard_page' ), 8 );
		add_action( 'admin_menu', array( 'Materialpool_Dashboard', 'register_settings_page' ) );

        // Add Filter & Actions for Material
        add_action( 'init', array( 'Materialpool_Material', 'register_post_type' ) );
        add_action( 'cmb2_admin_init', array( 'Materialpool_Material', 'register_meta_fields' ) );
        add_filter( 'template_include', array( 'Materialpool_Material', 'load_template' ) );
        add_action( 'manage_material_posts_columns', array( 'Materialpool_Material', 'cpt_list_head') );
        add_action( 'manage_material_posts_custom_column', array( 'Materialpool_Material', 'cpt_list_column'), 10,2 );
        add_action( 'manage_edit-material_sortable_columns', array( 'Materialpool_Material', 'cpt_sort_column') );

        // Add Filter & Actions for Organisation
        add_action( 'init', array( 'Materialpool_Organisation', 'register_post_type' ) );
        add_action( 'cmb2_admin_init', array( 'Materialpool_Organisation', 'register_meta_fields' ) );
        add_filter( 'template_include', array( 'Materialpool_Organisation', 'load_template' ) );
        add_action( 'manage_organisation_posts_columns', array( 'Materialpool_Organisation', 'cpt_list_head') );
        add_action( 'manage_organisation_posts_custom_column', array( 'Materialpool_Organisation', 'cpt_list_column'), 10,2 );
        add_action( 'manage_edit-organisation_sortable_columns', array( 'Materialpool_Organisation', 'cpt_sort_column') );

        // Add Filter & Actions for Autor
        add_action( 'init', array( 'Materialpool_Autor', 'register_post_type' ) );
        add_action( 'cmb2_admin_init', array( 'Materialpool_Autor', 'register_meta_fields' ) );
        add_filter( 'template_include', array( 'Materialpool_Autor', 'load_template' ) );
        add_action( 'manage_autor_posts_columns', array( 'Materialpool_Autor', 'cpt_list_head') );
        add_action( 'manage_autor_posts_custom_column', array( 'Materialpool_Autor', 'cpt_list_column'), 10,2 );
        add_action( 'manage_edit-autor_sortable_columns', array( 'Materialpool_Autor', 'cpt_sort_column') );
        add_action( 'save_post', array( 'Materialpool_Autor', 'generate_title') );

        // Add Filter & Actions for Konfession
		add_action( 'init', array( 'Materialpool_Konfession', 'register_taxonomy' ) );

        // Add Filter & Actions for Inklusives Material
        add_action( 'init', array( 'Materialpool_Inklusives_Material', 'register_taxonomy' ) );

        // Add Filter & Actions for Lizenz
        add_action( 'init', array( 'Materialpool_Lizenz', 'register_taxonomy' ) );
        add_action( 'cmb2_admin_init', array( 'Materialpool_Lizenz', 'add_taxonomy_metadata' ) );
        add_filter( 'manage_edit-lizenz_columns', array( 'Materialpool_Lizenz', 'taxonomy_column' ) );
        add_filter( 'manage_lizenz_custom_column', array( 'Materialpool_Lizenz', 'taxonomy_column_data' ), 10, 3);
        add_filter( 'manage_edit-lizenz_sortable_columns', array( 'Materialpool_Lizenz', 'taxonomy_sort_column' ) );

        // Add Filter & Actions for Zugänglichkeit
        add_action( 'init', array( 'Materialpool_Zugaenglichkeit', 'register_taxonomy' ) );
        add_action( 'cmb2_admin_init', array( 'Materialpool_Zugaenglichkeit', 'add_taxonomy_metadata' ) );
        add_filter( 'manage_edit-zugaenglichkeit_columns', array( 'Materialpool_Zugaenglichkeit', 'taxonomy_column' ) );
        add_filter( 'manage_zugaenglichkeit_custom_column', array( 'Materialpool_Zugaenglichkeit', 'taxonomy_column_data' ), 10, 3);
        add_filter( 'manage_edit-zugaenglichkeit_sortable_columns', array( 'Materialpool_Zugaenglichkeit', 'taxonomy_sort_column' ) );

        // Add Filter & Actions for Verfügbarkeit
        add_action( 'init', array( 'Materialpool_Verfuegbarkeit', 'register_taxonomy' ) );
        add_action( 'cmb2_admin_init', array( 'Materialpool_Verfuegbarkeit', 'add_taxonomy_metadata' ) );
        add_filter( 'manage_edit-verfuegbarkeit_columns', array( 'Materialpool_Verfuegbarkeit', 'taxonomy_column' ) );
        add_filter( 'manage_verfuegbarkeit_custom_column', array( 'Materialpool_Verfuegbarkeit', 'taxonomy_column_data' ), 10, 3);
        add_filter( 'manage_edit-verfuegbarkeit_sortable_columns', array( 'Materialpool_Verfuegbarkeit', 'taxonomy_sort_column' ) );

        // Add Filter & Actions for Altersstufe
        add_action( 'init', array( 'Materialpool_Altersstufe', 'register_taxonomy' ) );
        add_action( 'cmb2_admin_init', array( 'Materialpool_Altersstufe', 'add_taxonomy_metadata' ) );
        add_filter( 'manage_edit-altersstufe_columns', array( 'Materialpool_Altersstufe', 'taxonomy_column' ) );
        add_filter( 'manage_altersstufe_custom_column', array( 'Materialpool_Altersstufe', 'taxonomy_column_data' ), 10, 3);
        add_filter( 'manage_edit-altersstufe_sortable_columns', array( 'Materialpool_Altersstufe', 'taxonomy_sort_column' ) );

        // Add Filter & Actions for Medientype
        add_action( 'init', array( 'Materialpool_Medientyp', 'register_taxonomy' ) );
        add_action( 'cmb2_admin_init', array( 'Materialpool_Medientyp', 'add_taxonomy_metadata' ) );
        add_filter( 'manage_edit-medientyp_columns', array( 'Materialpool_Medientyp', 'taxonomy_column' ) );
        add_filter( 'manage_medientyp_custom_column', array( 'Materialpool_Medientyp', 'taxonomy_column_data' ), 10, 3);
        add_filter( 'manage_edit-medientyp_sortable_columns', array( 'Materialpool_Medientyp', 'taxonomy_sort_column' ) );

        // Add Filter & Actions for Bildungsstufe
        add_action( 'init', array( 'Materialpool_Bildungsstufe', 'register_taxonomy' ) );
        add_action( 'cmb2_admin_init', array( 'Materialpool_Bildungsstufe', 'add_taxonomy_metadata' ) );
        add_filter( 'manage_edit-bildungsstufe_columns', array( 'Materialpool_Bildungsstufe', 'taxonomy_column' ) );
        add_filter( 'manage_bildungsstufe_custom_column', array( 'Materialpool_Bildungsstufe', 'taxonomy_column_data' ), 10, 3);
        add_filter( 'manage_edit-bildungsstufe_sortable_columns', array( 'Materialpool_Bildungsstufe', 'taxonomy_sort_column' ) );

        // Add Filter & Actions for Sprache
        add_action( 'init', array( 'Materialpool_Sprache', 'register_taxonomy' ) );

        // Add Filter & Actions for Keywords
        add_action( 'init', array( 'Materialpool_Keywords', 'register_taxonomy' ) );

        // CMB2 Enhancement
        add_filter( 'cmb2_render_cpt_select', array( 'Materialpool_CMB2_CPT_Select', 'render_cpt_select' ), 10, 5 );
        add_filter( 'cmb2_sanitize_cpt_select', array( 'Materialpool_CMB2_CPT_Select', 'sanitize_cpt_select' ), 10, 4 );

        add_image_size( 'materialpool-autor-size', 110, 90 );

        do_action( 'materialpool_init' );
	}

	/**
	 * Creates an Instance of this Class
	 *
	 * @since   0.0.1
	 * @access  public
	 * @return  Emergency_Report
	 */
	public static function get_instance() {

		if ( NULL === self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Load the localization
	 *
	 * @since	0.0.1
	 * @access	public
	 * @uses	load_plugin_textdomain, plugin_basename
	 * @filters materialpool_translationpath path to translations files
	 * @return	void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( self::get_textdomain(), false, apply_filters ( 'materialpool_translationpath', dirname( plugin_basename( __FILE__ )) .  self::get_textdomain_path() ) );
	}

	/**
	 * Get a value of the plugin header
	 *
	 * @since   0.0.1
	 * @access	protected
	 * @param	string $value
	 * @uses	get_plugin_data, ABSPATH
	 * @return	string The plugin header value
	 */
	protected function get_plugin_header( $value = 'TextDomain' ) {

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php');
		}

		$plugin_data = get_plugin_data( __FILE__ );
		$plugin_value = $plugin_data[ $value ];

		return $plugin_value;
	}

	/**
	 * get the textdomain
	 *
	 * @since   0.0.1
	 * @static
	 * @access	public
	 * @return	string textdomain
	 */
	public static function get_textdomain() {
		if( is_null( self::$textdomain ) )
			self::$textdomain = self::get_plugin_data( 'TextDomain' );

		return self::$textdomain;
	}

	/**
	 * get the textdomain path
	 *
	 * @since   0.0.1
	 * @static
	 * @access	public
	 * @return	string Domain Path
	 */
	public static function get_textdomain_path() {
		return self::get_plugin_data( 'DomainPath' );
	}

	/**
	 * return plugin comment data
	 *
	 * @since   0.0.1
	 * @uses    get_plugin_data
	 * @access  public
	 * @param   $value string, default = 'Version'
	 *		Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
	 * @return  string
	 */
	public static function get_plugin_data( $value = 'Version' ) {

		if ( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$plugin_data  = get_plugin_data ( __FILE__ );
		$plugin_value = $plugin_data[ $value ];

		return $plugin_value;
	}

    /**
     * Register and enqueue style sheet.
     */
    public function register_admin_plugin_styles() {
        wp_register_style( 'rw-materialpool', Materialpool::$plugin_url . 'css/backend.css' );
        wp_enqueue_style( 'rw-materialpool' );
    }
}


if ( class_exists( 'Materialpool' ) ) {
	if ( file_exists( dirname( __FILE__ ) . '/vendor/cmb2/init.php' ) ) {
		require_once dirname( __FILE__ ) . '/vendor/cmb2/init.php';
	} elseif ( file_exists( dirname( __FILE__ ) . '/vendor/CMB2/init.php' ) ) {
		require_once dirname( __FILE__ ) . '/vendor/CMB2/init.php';
	}

	add_action( 'plugins_loaded', array( 'Materialpool', 'get_instance' ) );

	require_once 'classes/Materialpool_Autoloader.php';
	Materialpool_Autoloader::register();

	register_activation_hook( __FILE__, array( 'Materialpool_Installation', 'on_activate' ) );
	register_uninstall_hook(  __FILE__,	array( 'Materialpool_Installation', 'on_uninstall' ) );
	register_deactivation_hook( __FILE__, array( 'Materialpool_Installation', 'on_deactivation' ) );
}
