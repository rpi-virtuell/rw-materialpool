<?php

/**
 * RW Materialpool
 *
 * @package   Materialpool
 * @author    Frank Neumann-Staude
 * @license   GPL-2.0+
 * @link      https://github.com/rpi-virtuell/rw-materialpool
 */

/*
 * Plugin Name:       RW Materialpool
 * Plugin URI:        https://github.com/rpi-virtuell/rw-materialpool
 * Description:       RPI Virtuell Materialpool
 * Version:           0.0.1
 * Author:            Frank Neumann-Staude
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
        if ( !is_admin() ) {
            add_action( 'wp_enqueue_scripts', array( 'Materialpool', 'register_frontend_plugin_styles' ) );
        }

		add_action('wp_enqueue_scripts',array( 'Materialpool','enqueue_our_required_stylesheets') );

        // Add Filter & Actions for Dashboard
		add_action( 'admin_menu', array( 'Materialpool_Dashboard', 'register_dashboard_page' ), 8 );
		//add_action( 'admin_menu', array( 'Materialpool_Dashboard', 'register_settings_page' ) );

        // Add Filter & Actions for Material
        add_filter( 'template_include', array( 'Materialpool_Material', 'load_template' ) );
        add_action( 'manage_material_posts_columns', array( 'Materialpool_Material', 'cpt_list_head') );
        add_action( 'manage_material_posts_custom_column', array( 'Materialpool_Material', 'cpt_list_column'), 10,2 );
        add_action( 'manage_edit-material_sortable_columns', array( 'Materialpool_Material', 'cpt_sort_column') );
		add_action( 'save_post', array( 'Materialpool_Material', 'generate_title') );
        add_action( 'admin_menu' , array( 'Materialpool_Material', 'remove_post_custom_fields' ) );
        add_filter( 'posts_join', array( 'Materialpool_Material', 'material_list_post_join' ) );
        add_filter( 'posts_where', array( 'Materialpool_Material', 'material_list_post_where' ) );
        add_filter( 'posts_distinct', array( 'Materialpool_Material', 'material_list_post_distinct' ), 10,1  );
        add_action( 'add_meta_boxes',  array( 'Materialpool_Material', 'add_metaboxes' ) );
        add_action( 'init', array( 'Materialpool_Material', 'custom_post_status' ) );
        add_action( 'admin_footer-post.php', array( 'Materialpool_Material', 'append_post_status_list' ) );
        add_action( 'admin_footer-post.php', array( 'Materialpool_Material', 'write_javascript' ) );
        add_action( 'admin_footer-post-new.php', array( 'Materialpool_Material', 'append_post_status_list' ) );
        add_action( 'admin_footer-post-new.php', array( 'Materialpool_Material', 'write_javascript' ) );
        add_filter( 'tl_tplc_external_files', array( 'Materialpool_Material', 'add_template_check_external_files' ) );
        add_action( 'init', array( 'Materialpool', 'get_crossdomain_viewer_url' ) );
        add_action( 'mp_depublizierung', array( 'Materialpool_Material', 'depublizierung' ) );
		add_action( 'mp_screenshot_generation', array( 'Materialpool', 'mp_screenshot_generation' ) );
        add_filter('template_redirect', array( 'Materialpool_Material', 'check_404_old_material' ) );
        add_action( 'restrict_manage_posts', array( 'Materialpool_Material', 'add_taxonomy_filters' ) );
        add_shortcode( 'material-vorschlag', array( 'Materialpool_Material', 'vorschlag_shortcode' ) );
        remove_shortcode( 'viewerjs', 'viewerjs_shortcode_handler');
        add_shortcode( 'viewerjs', array( 'Materialpool', 'viewerjs_shortcode_handler' ) );
        add_filter( 'bulk_actions-edit-material', array( 'Materialpool_Material','remove_from_bulk_actions' ) );
		add_action( 'wp_head', array( 'Materialpool_Material','add_open_graph' ) );
		add_filter( 'parse_query',  array( 'Materialpool_Material','admin_posts_filter' ));
		add_filter( 'query_vars', array( 'Materialpool_Material', 'rss_query_vars' ) );
		add_action( 'pre_get_posts', array( 'Materialpool_Material',  'rss_pre_get_posts' ) );
		add_action( 'save_post', array( 'Materialpool_Material','cleanup_themenseiten' ), 10, 3 );
		add_filter( 'default_hidden_meta_boxes', array( 'Materialpool_Material', 'default_hide_meta_box' ) ,10,2);
		add_action( 'admin_menu', array( 'Materialpool_Material', 'options_page' ) );
		//        remove_filter( 'pre_oembed_result',      'wp_filter_pre_oembed_result',    10 );
		//        add_filter( 'pre_oembed_result',      array( 'Materialpool', 'wp_filter_pre_oembed_result' ),    10, 3 );

        /*
         * Register as Class method throws an error
         */
        add_action( 'pods_meta_groups',  'materialpool_pods_material_metaboxes', 10, 2 );
		add_action( 'rest_api_init', 'register_mymaterial_rest_routes' );


        // Add Filter & Actions for Organisation
        add_filter( 'template_include', array( 'Materialpool_Organisation', 'load_template' ) );
        add_action( 'manage_organisation_posts_columns', array( 'Materialpool_Organisation', 'cpt_list_head') );
        add_action( 'manage_organisation_posts_custom_column', array( 'Materialpool_Organisation', 'cpt_list_column'), 10,2 );
        add_action( 'manage_edit-organisation_sortable_columns', array( 'Materialpool_Organisation', 'cpt_sort_column') );
		add_action( 'save_post', array( 'Materialpool_Organisation', 'generate_title') );
        add_filter( 'tl_tplc_external_files', array( 'Materialpool_Organisation', 'add_template_check_external_files' ) );
        add_filter( 'bulk_actions-edit-organisation', array( 'Materialpool_Material','remove_from_bulk_actions' ) );

        // Add Filter & Actions for Autor
        add_filter( 'template_include', array( 'Materialpool_Autor', 'load_template' ) );
        add_action( 'manage_autor_posts_columns', array( 'Materialpool_Autor', 'cpt_list_head') );
        add_action( 'manage_autor_posts_custom_column', array( 'Materialpool_Autor', 'cpt_list_column'), 10,2 );
        add_action( 'manage_edit-autor_sortable_columns', array( 'Materialpool_Autor', 'cpt_sort_column') );
        add_action( 'save_post', array( 'Materialpool_Autor', 'generate_title') );
        add_filter( 'tl_tplc_external_files', array( 'Materialpool_Autor', 'add_template_check_external_files' ) );
        add_filter( 'bulk_actions-edit-autor', array( 'Materialpool_Material','remove_from_bulk_actions' ) );
		add_shortcode( 'autor_register', array( 'Materialpool_Autor', 'shortcode_register_autor' ) );

        // Add Filter & Actions for Sprache
        add_filter( 'manage_edit-sprache_columns', array( 'Materialpool_Sprache', 'taxonomy_column' ) );
        add_filter( 'manage_sprache_custom_column', array( 'Materialpool_Sprache', 'taxonomy_column_data' ), 10, 3);

        // Add Filter & Actions for Zugänglichkeiten
        add_filter( 'manage_edit-zugaenglichkeit_columns', array( 'Materialpool_Zugaenglichkeiten', 'taxonomy_column' ) );
        add_filter( 'manage_zugaenglichkeit_custom_column', array( 'Materialpool_Zugaenglichkeiten', 'taxonomy_column_data' ), 10, 3);

        // Add Filter & Actions for Verfügbarkeiten
        add_filter( 'manage_edit-verfuegbarkeit_columns', array( 'Materialpool_Verfuegbarkeiten', 'taxonomy_column' ) );
        add_filter( 'manage_verfuegbarkeit_custom_column', array( 'Materialpool_Verfuegbarkeiten', 'taxonomy_column_data' ), 10, 3);

        // Add Filter & Actions for Schlagworte
        add_filter( 'manage_edit-schlagwort_columns', array( 'Materialpool_Schlagworte', 'taxonomy_column' ) );
        add_filter( 'manage_schlagwort_custom_column', array( 'Materialpool_Schlagworte', 'taxonomy_column_data' ), 10, 3);
		add_filter( 'pods_form_ui_field_pick_autocomplete_limit', array( 'Materialpool_Schlagworte', 'pods_form_ui_field_pick_autocomplete_limit' ), 10, 1);


        // Add Filter & Actions for Medientypem
        add_filter( 'manage_edit-medientyp_columns', array( 'Materialpool_Medientyp', 'taxonomy_column' ) );
        add_filter( 'manage_medientyp_custom_column', array( 'Materialpool_Medientyp', 'taxonomy_column_data' ), 10, 3);

        // Add Filter & Actions for Lizenzen
        add_filter( 'manage_edit-lizenz_columns', array( 'Materialpool_Lizenzen', 'taxonomy_column' ) );
        add_filter( 'manage_lizenz_custom_column', array( 'Materialpool_Lizenzen', 'taxonomy_column_data' ), 10, 3);

        // Add Filter & Actions for Konfession
        add_filter( 'manage_edit-konfession_columns', array( 'Materialpool_Konfessionen', 'taxonomy_column' ) );
        add_filter( 'manage_konfession_custom_column', array( 'Materialpool_Konfessionen', 'taxonomy_column_data' ), 10, 3);

        // Add Filter & Actions for Inklusion
        add_filter( 'manage_edit-inklusion_columns', array( 'Materialpool_Inklusionen', 'taxonomy_column' ) );
        add_filter( 'manage_inklusion_custom_column', array( 'Materialpool_Inklusionen', 'taxonomy_column_data' ), 10, 3);

        // Add Filter & Actions for Bildungsstufen
        add_filter( 'manage_edit-bildungsstufe_columns', array( 'Materialpool_Bildungsstufen', 'taxonomy_column' ) );
        add_filter( 'manage_bildungsstufe_custom_column', array( 'Materialpool_Bildungsstufen', 'taxonomy_column_data' ), 10, 3);

        // Add Filter & Actions for Altersstuden
        add_filter( 'manage_edit-altersstufe_columns', array( 'Materialpool_Altersstufen', 'taxonomy_column' ) );
        add_filter( 'manage_altersstufe_custom_column', array( 'Materialpool_Altersstufen', 'taxonomy_column_data' ), 10, 3);

        // Add Filter & Actions for Synonyme

        add_filter( 'manage_edit-synonym_columns', array( 'Materialpool_Synonyme', 'cpt_list_head' ) );
        add_action( 'manage_synonym_posts_custom_column', array( 'Materialpool_Synonyme', 'cpt_list_column'), 10,2 );
        add_action( 'manage_edit-synonym_sortable_columns', array( 'Materialpool_Synonyme', 'cpt_sort_column') );
        add_action( 'wp_ajax_mp_synonym_check_tag',  array( 'SearchWP_Materialpool_Synonyms', 'wp_ajax_mp_synonym_check_tag' ) );
        add_filter( 'searchwp_extensions',          array( 'SearchWP_Materialpool_Synonyms', 'register' ), 10 );
        add_filter( 'searchwp_term_in',             array( 'SearchWP_Materialpool_Synonyms', 'find_synonyms' ), 10, 3 );
        add_filter( 'bulk_actions-edit-synonym', array( 'Materialpool_Synonyme','remove_from_bulk_actions' ) );
		add_filter( 'posts_join', array( 'Materialpool_Synonyme', 'material_list_post_join' ) );
		add_filter( 'posts_where', array( 'Materialpool_Synonyme', 'material_list_post_where' ) );
		add_filter( 'posts_distinct', array( 'Materialpool_Synonyme', 'material_list_post_distinct' ),10 ,1 );

        // Add Filter & Actions for Themenseiten
        add_filter( 'template_include', array( 'Materialpool_Themenseite', 'load_template' ) );
        add_filter( 'tl_tplc_external_files', array( 'Materialpool_Themenseite', 'add_template_check_external_files' ) );
        add_action( 'save_post', array( 'Materialpool_Themenseite', 'generate_taxonomy') );
        add_filter( 'bulk_actions-edit-themenseite', array( 'Materialpool_Themenseite','remove_from_bulk_actions' ) );
		add_action( 'manage_themenseite_posts_columns', array( 'Materialpool_Themenseite', 'cpt_list_head') );
		add_action( 'manage_themenseite_posts_custom_column', array( 'Materialpool_Themenseite', 'cpt_list_column'), 10,2 );
		add_action( 'admin_menu' , array( 'Materialpool_Themenseite', 'remove_post_custom_fields' ) );

		// Add Filter & Actions for Kompetenz
		add_filter( 'manage_edit-kompetenz_columns', array( 'Materialpool_Kompetenzen', 'taxonomy_column' ) );
		add_filter( 'manage_kompetenz_custom_column', array( 'Materialpool_Kompetenzen', 'taxonomy_column_data' ), 10, 3);


		// Add Filter & Actions for Settingspage
        //add_action( 'admin_menu', array( 'Materialpool_Settings', 'options_page' ) );
        //add_action( 'admin_menu', array( 'Materialpool_Settings', 'settings_init' ) );

        // Add Filter  & Actions for Posts
        add_action( 'add_meta_boxes',  array( 'Materialpool_Posts', 'add_metaboxes' ) );

        // Add Filter & Actions for 3Party Stuff
        add_action( 'rate_post',                            array( 'Materialpool_FacetWP', 'reindex_post_after_ajax_rating'),10, 2 );
        add_action( 'pods_api_post_save_pod_item_material', array( 'Materialpool_FacetWP', 'reindex_post_after_pods_saveing'),10, 3 );
        add_action( 'pods_api_post_save_pod_item_organisation', array( 'Materialpool_FacetWP', 'reindex_post_after_pods_saveing'),10, 3 );
        remove_filter('manage_posts_columns', 'add_postratings_column');
        remove_filter('manage_pages_columns', 'add_postratings_column');
        add_filter( 'manage_material_posts_columns', array( 'Materialpool_Ratings', 'page_column'), 9999 );
        add_action( 'user_register', array( 'Materialpool', 'user_defaults' ), 10, 1 );

        //pods_register_field_type( 'screenshot', self::$plugin_base_dir . 'classes/Materialpool_Pods_Screenshot.php' );
        //pods_register_field_type( 'facette', self::$plugin_base_dir . 'classes/Materialpool_Pods_Facette.php' );
		//pods_register_field_type( 'synonymlist', self::$plugin_base_dir . 'classes/Materialpool_Pods_Synonymlist.php' );

        add_action( 'wp_ajax_mp_get_html',  array( 'Materialpool', 'my_action_callback_mp_get_html' ) );
		add_action( 'wp_ajax_mp_get_screenshot',  array( 'Materialpool', 'my_action_callback_mp_get_screenshot' ) );
        add_action( 'wp_ajax_mp_get_description',  array( 'Materialpool', 'my_action_callback_mp_get_description' ) );
        add_action( 'wp_ajax_mp_check_url',  array( 'Materialpool', 'my_action_callback_mp_check_url' ) );
        add_action( 'wp_ajax_mp_check_material_title',  array( 'Materialpool', 'my_action_callback_mp_check_material_title' ) );
        add_action( 'wp_ajax_mp_check_organisation_title',  array( 'Materialpool', 'my_action_callback_mp_check_organisation_title' ) );
        add_action( 'wp_ajax_mp_add_thema',  array( 'Materialpool', 'my_action_callback_mp_add_thema' ) );
        add_action( 'wp_ajax_mp_remove_thema',  array( 'Materialpool', 'my_action_callback_mp_remove_thema' ) );
        add_action( 'wp_ajax_mp_remove_thema_backend',  array( 'Materialpool', 'my_action_callback_mp_remove_thema_backend' ) );
        add_action( 'wp_ajax_mp_list_thema_backend',  array( 'Materialpool', 'my_action_callback_mp_list_thema_backend' ) );
		add_action( 'wp_ajax_mp_send_autor_mail',  array( 'Materialpool', 'my_action_callback_mp_send_autor_mail' ) );
		add_action( 'wp_ajax_mp_send_organisation_mail',  array( 'Materialpool', 'my_action_callback_mp_send_organisation_mail' ) );
		add_action( 'wp_ajax_mp_change_autor_einverstaendnis',  array( 'Materialpool', 'my_action_callback_mp_change_autor_einverstaendnis' ) );
		add_action( 'wp_ajax_mp_change_organisation_einverstaendnis',  array( 'Materialpool', 'my_action_callback_mp_change_organisation_einverstaendnis' ) );
        add_action( 'wp_ajax_nopriv_mp_add_proposal',  array( 'Materialpool', 'my_action_callback_mp_add_proposal' ) );
        add_action( 'wp_ajax_mp_add_proposal',  array( 'Materialpool', 'my_action_callback_mp_add_proposal' ) );
		add_action( 'wp_ajax_mp_synonym_list',  array( 'Materialpool', 'my_action_callback_mp_synonym_list' ) );
        add_action( 'wp_ajax_convert2material',  array( 'Materialpool', 'my_action_callback_convert2material' ) );
		add_action( 'wp_ajax_mp_edit_subscription',  array( 'Materialpool', 'my_action_callback_edit_subscription' ) );
		add_action( 'wp_ajax_mp_check_autor_request',  array( 'Materialpool', 'my_action_callback_check_autor_request' ) );
		add_action( 'wp_ajax_mp_check_autor_request2',  array( 'Materialpool', 'my_action_callback_check_autor_request2' ) );

        add_action( 'wp_ajax_mp_check_subscription',  array( 'Materialpool', 'my_action_callback_check_subscription' ) );
		add_action( 'wp_ajax_mp_check_subscription2',  array( 'Materialpool', 'my_action_callback_check_subscription2' ) );
		add_action( 'wp_ajax_mp_add_subscription',  array( 'Materialpool', 'my_action_callback_add_subscription' ) );
		add_filter( 'rest_prepare_material', array( 'Materialpool_Statistic', 'log_api_request'), 10, 3 );
		add_filter( 'cron_schedules', array( 'Materialpool', 'custom_cron_job_recurrence' ) );

        add_filter( 'facetwp_api_can_access', function() { return true;} );
        add_action( 'wp_head', array( 'Materialpool',  'promote_feeds' ) );
        remove_all_actions( 'do_feed_rss2' );
        add_action( 'do_feed_rss2', array( 'Materialpool', 'material_feed_rss2') , 10, 1 );
        add_filter( 'request', array( 'Materialpool', 'exclude_entwurf' ) );
        add_action( 'init', array( 'Materialpool', 'custom_oembed_providers' ) , 10, 1 );

        // Register New Facet for Searches in Old MP
        add_filter( 'facetwp_facet_types', function( $facet_types ) {
            $facet_types['select2'] = new Materialpool_FacetWP_OldSearch();
            return $facet_types;
        });
		add_filter( 'searchwp_posts_per_page', array( 'Materialpool', 'my_searchwp_posts_per_page' ), 99999, 4 );

        // Register ImportPlugin End Action
        add_action( 'import_end', array( 'Materialpool_Import_Check', 'check' ) );

        // Embeds
        add_filter ( 'embed_site_title_html', array( 'Materialpool_Embeds','site_title_html') );
        add_filter ( 'the_excerpt_embed', array( 'Materialpool_Embeds', 'the_excerpt_embed' ) );
        add_action( 'embed_content', array( 'Materialpool_Embeds', 'embed_content' ) );

        //
        add_filter( 'post_row_actions', 'rw_mp_row_actions', 10, 2 );

		// Materialpool Contribute APIs
		add_action( 'init',             array( 'Materialpool_Contribute', 'add_endpoint'), 0 );
		add_filter( 'query_vars',       array( 'Materialpool_Contribute', 'add_query_vars'), 0 );
		add_action( 'parse_request',    array( 'Materialpool_Contribute', 'parse_request'), 0 );
		add_action( 'edit_user_profile',array( 'Materialpool_Contribute', 'edit_user_profile' ) );
		add_action( 'show_user_profile',array( 'Materialpool_Contribute', 'edit_user_profile' ) );

		add_filter( 'rw_materialpool_contribute_cmd_parser', array( 'Materialpool_Contribute', 'cmd_list_medientypen' ) );
		add_filter( 'rw_materialpool_contribute_cmd_parser', array( 'Materialpool_Contribute', 'cmd_send_post' ) );
		add_filter( 'rw_materialpool_contribute_cmd_parser', array( 'Materialpool_Contribute', 'cmd_ping' ) );
		add_filter( 'rw_materialpool_contribute_cmd_parser', array( 'Materialpool_Contribute', 'cmd_say_hello' ) );
		add_filter( 'rw_materialpool_contribute_cmd_parser', array( 'Materialpool_Contribute', 'cmd_list_authors' ) );
		add_filter( 'rw_materialpool_contribute_cmd_parser', array( 'Materialpool_Contribute', 'cmd_list_bildungsstufen' ) );
		add_filter( 'rw_materialpool_contribute_cmd_parser', array( 'Materialpool_Contribute', 'cmd_list_altersstufen' ) );

		add_action( 'admin_menu', array( 'Materialpool_Contribute', 'options_page' ) );
		add_action( 'admin_menu', array( 'Materialpool_Contribute', 'settings_init' ) );
		add_action( 'init',             array( 'Materialpool_Contribute_Clients', 'init'), 0 );

        if ( defined ( 'WP_CLI' ) && WP_CLI ) {
            require_once( __DIR__ . '/classes/Materialpool_wp-cli_commands.php' );
        }

		do_action( 'materialpool_init' );
	}

    public static function enqueue_our_required_stylesheets(){
		wp_enqueue_style('font-awesome', '//use.fontawesome.com/releases/v5.6.3/css/all.css');
	}

	public static function my_searchwp_posts_per_page( $posts_per_page, $engine, $terms, $page ) {
		return 2000;
	}

	public static function exclude_entwurf( $qv ) {
        if (isset($qv['feed']) && $qv['post_type'] == 'material') {
            $qv['post_status'] = array('publish');
        }
        return $qv;
    }

    public static function user_defaults( $user_id ) {
        update_user_meta($user_id, 'metaboxhidden_material', array("slugdiv","trackbacksdiv","commentstatusdiv","commentsdiv") );
        update_user_meta($user_id, 'meta-box-order_material', array ( "side" => "submitdiv", "normal"=> "slugdiv,pods-meta-basis,pods-meta-eigentuemer,pods-meta-meta,pods-meta-erweiterte-metadaten,pods-meta-datum,pods-meta-verknuepfungen,pods-meta-titelbild", "advanced"=>"") );
    }

    public static function custom_oembed_providers() {
        wp_oembed_add_provider( 'http://learningapps.org/*', 'http://learningapps.org/oembed.php' );
    }


    public static function material_feed_rss2( $for_comments ) {
        if( get_query_var( 'post_type' ) == 'material' ) {
            $rss_template = Materialpool::$plugin_base_dir . 'templates/feed-material-rss2.php';
            if (file_exists($rss_template)) {
                load_template($rss_template);
            } else {
                do_feed_rss2($for_comments);
            }
        } elseif ( get_query_var( 'post_type' ) == 'organisation' ) {
            $rss_template = Materialpool::$plugin_base_dir . 'templates/feed-organisation-rss2.php';
            if (file_exists($rss_template)) {
                load_template($rss_template);
            } else {
                do_feed_rss2($for_comments);
            }
        } elseif ( get_query_var( 'post_type' ) == 'autor' ) {
            $rss_template = Materialpool::$plugin_base_dir . 'templates/feed-autor-rss2.php';
            if (file_exists($rss_template)) {
                load_template($rss_template);
            } else {
                do_feed_rss2($for_comments);
            }
        } else {
	        do_feed_rss2($for_comments);
        }

    }



    function wp_filter_pre_oembed_result( $result, $url, $args ) {

        $width = isset( $args['width'] ) ? $args['width'] : 0;

        $data = get_oembed_response_data( $post_id, $width );
        $data = _wp_oembed_get_object()->data2html( (object) $data, $url );

        if ( ! $data ) {
            return $result;
        }

        return $data;
    }


    /**
     * Load HTML from remote site
     *
     * @since   0.0.1
     * @access  public
     */
    public static function my_action_callback_mp_get_html() {
        $url =  esc_url_raw( $_POST['site'] );
        $args = array(
            'user-agent' => 'Mozilla/5.0 (compatible; Materialpool; +'.home_url().')',
            'timeout'     => 30,
            'sslverify' => false,
        );
        $response =  wp_remote_get( $url, $args );
        if ( is_wp_error( $response) ) {
            wp_die;
        }
        $body = $response['body'];
        libxml_use_internal_errors(true);
        $doc = new DomDocument();
        $doc->loadHTML($body);
        $xpath = new DOMXPath($doc);
        $query = '//*/meta[starts-with(@property, \'og:\')]';
        $metas = $xpath->query($query);
        foreach ($metas as $meta) {
            $property = $meta->getAttribute('property');
            $content = $meta->getAttribute('content');
            if ( ( $property == 'og:image' ) && ( strpos( $content, 'http') === 0 ) ) {
                echo $content; break;
            }
        }
        wp_die();
    }

	/**
	 * Create Screenshot via screenshotapi.io
	 *
	 * @since   0.0.1
	 * @access  public
	 */
	public static function my_action_callback_mp_get_screenshot() {

		$url =  esc_url_raw( $_POST['site'] );
		$apikey = "f18c29cc-d0a3-427b-ac4e-274c89273513";

		$obj = new stdClass();
		$obj->url = $url;
		$obj->viewport = "1280x1024";
		$obj->fullpage = false;
		$obj->javascript = true;
		$obj->webdriver = "firefox";
		$obj->waitSeconds = 5;
		$obj->fresh = false;

		$json = json_encode($obj);

		$ch = curl_init('https://api.screenshotapi.io/capture');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'apikey: '.$apikey,
				'Content-Type: application/json',
				'Content-Length: ' . strlen($json))
		);

		$result = curl_exec($ch);
		$result = json_decode( $result );

		$key = $result->key;

		do {
		    sleep( 3 );
			$ch2 = curl_init( 'https://api.screenshotapi.io/retrieve?key=' . $key );
			curl_setopt( $ch2, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch2, CURLOPT_HTTPHEADER, array(
					'apikey: ' . $apikey
				)
			);

			$result = curl_exec( $ch2 );
			$result = json_decode( $result );
		} while ( $result->status == 'processing' );

		// Screenshot runterladen

		$img = WP_CONTENT_DIR . '/screenshots/'. $key . '.png';

		$ch = curl_init( $result->imageUrl );
		$fp = fopen($img, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);

		echo WP_CONTENT_URL . '/screenshots/'. $key . '.png';
		wp_die();
	}


	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
	public static function my_action_callback_edit_subscription() {

		$autor_id =  (int) $_POST['autor'];
		$user =  (int) $_POST['user'];
		$cmd = $_POST['cmd'];

		if ( $user == 0 ) {     // Benutzer nicht angemeldet.
			wp_die();
		}

        if ( $cmd == 'del' ) {
	        delete_user_meta( $user, 'autor_status' );
	        delete_user_meta( $user, 'autor_hash' );
	        delete_user_meta( $user, 'autor_link' );
	        delete_post_meta( $autor_id, 'user_status' );
	        delete_post_meta( $autor_id, 'user_link' );

	        // generate Mail
	        $sendmail = get_option( 'einstellungen_autor_user_false', 0 );
	        $userObj = get_userdata( $user );
	        $email    = $userObj->user_email;
	        if ( $sendmail == 1 && $email != '' ) {
                $subject = get_option( 'einstellungen_autor_user_false_subject', false );
                $content = get_option( 'einstellungen_autor_user_false_content', false );
                if ( $subject && $content ) {
                    $headers[] = 'From: Redaktion rpi-virtuell <redaktion@rpi-virtuell.de>';
                    $headers[] = 'Reply-To: Redaktion rpi-virtuell <redaktion@rpi-virtuell.de>';
                    $headers[] = 'bcc: material@rpi-virtuell.de';
                    $mail = wp_mail( $email, $subject, $content , $headers );
                }
	        }

        }
		if ( $cmd == 'add' ) {
            update_user_meta( $user, 'autor_status', 'ok' );
			update_post_meta( $autor_id, 'user_status', 'ok' );
            $hash = password_hash( $user . '___' . $autor_id, PASSWORD_DEFAULT );
            add_user_meta( $user, 'autor_hash', $hash );

			// Wenn User Abonnent oder Mitarbeiter ist, auf Autor hochstufen.

			$user_meta = get_userdata( $user );
			$user_roles = $user_meta->roles;
			if ( in_array("subscriber", $user_roles ) || in_array("contributor", $user_roles ) ) {
				$u = new WP_User( $user );
				if ( in_array( "subscriber", $user_roles ) ) {
					$u->remove_role( "subscriber" );
				}
				if ( in_array( "contributor", $user_roles ) ) {
					$u->remove_role( "contributor" );
				}
				$u->add_role( "author" );
			}

			// generate Mail
			$sendmail = get_option( 'einstellungen_autor_user_true', 0 );
			$userObj = get_userdata( $user );
	        $email    = $userObj->user_email;
	        if ( $sendmail == 1 && $email != '' ) {
		        $subject = get_option( 'einstellungen_autor_user_true_subject', false );
		        $content = get_option( 'einstellungen_autor_user_true_content', false );
		        if ( $subject && $content ) {

			        $headers[] = 'From: Redaktion rpi-virtuell <redaktion@rpi-virtuell.de>';
			        $headers[] = 'Reply-To: Redaktion rpi-virtuell <redaktion@rpi-virtuell.de>';
			        $headers[] = 'bcc: material@rpi-virtuell.de';
			        $mail = wp_mail( $email, $subject, $content , $headers );
		        }
	        }
		}
		?>
		ok
        <?php
		wp_die();
	}



	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
    public static function my_action_callback_check_subscription() {

	    $autor_id =  (int) $_POST['autor'];
	    $user =  (int) $_POST['user'];

	    if ( $user == 0 ) {     // Benutzer nicht angemeldet.
		    wp_die();
	    }
	    // Hat User schon eine Autorenverknüpfung gestellt?
	    if ( get_user_meta( $user, 'autor_link', true ) != '' ) {
		    wp_die();
	    }
	    // Ist Autor schon mit einem User verknüpft?
	    if ( get_post_meta( $autor_id, 'user_link', true ) != '' ) {
		    wp_die();
	    }

    	?>
	    <a class="cta-button" >Ich bin dieser Autor</a>
		<?php
    	wp_die();
    }

	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
	public static function my_action_callback_check_subscription2() {

		$user =  (int) $_POST['user'];

		if ( $user == 0 ) {     // Benutzer nicht angemeldet.
			wp_die();
		}
		// Hat User schon eine Autorenverknüpfung gestellt?
		if ( get_user_meta( $user, 'autor_link', true ) != '' ) {
			wp_die();
		}

		?>
        <a href="/autor-werden" class="cta-button" >AutorIn werden</a>
		<?php
		wp_die();
	}


	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
	public static function my_action_callback_add_subscription() {
		$autor_id =  (int) $_POST['autor'];
		$user =  (int) $_POST['user'];

		if ( $user == 0 ) {     // Benutzer nicht angemeldet.
			wp_die();
		}
		// Hat User schon eine Autorenverknüpfung gestellt?
		if ( get_user_meta( $user, 'autor_link', true ) != '' ) {
			wp_die();
		}
		// Ist Autor schon mit einem User verknüpft?
		if ( get_post_meta( $autor_id, 'user_link', true ) != '' ) {
			wp_die();
		}

		add_post_meta( $autor_id, 'user_link', $user );
		add_post_meta( $autor_id, 'user_status', 'pending' );
		add_user_meta( $user, 'autor_link', $autor_id );
		add_user_meta( $user, 'autor_status', 'pending' );

		// Wenn User Abonnent oder Mitarbeiter ist, auf Autor hochstufen.

		$user_meta = get_userdata( $user );
		$user_roles = $user_meta->roles;
		if ( in_array("subscriber", $user_roles ) || in_array("contributor", $user_roles ) ) {
			$u = new WP_User( $user );
			if ( in_array( "subscriber", $user_roles ) ) {
				$u->remove_role( "subscriber" );
			}
			if ( in_array( "contributor", $user_roles ) ) {
				$u->remove_role( "contributor" );
			}
			$u->add_role( "author" );
		}

		?>
		<a class="cta-button" >Autorenverknüpfung beantragt.</a>
		<?php
		wp_die();
	}


	/**
	 *
	 */
	public static function my_action_callback_mp_synonym_list() {
    	$liste = $_POST['list'];

		$counter = 0;
		$schlagworte = explode( ',', $liste );
		foreach ($schlagworte as $schlagwort ) {
			if ( $schlagwort !== false ) {
				$term  = get_term( $schlagwort, 'schlagwort' );
				if ( ! is_wp_error( $term ) ) {
					$posts = get_posts( array(
						'post_type'   => 'synonym',
						'orderby'     => 'post_title',
						'post_status' => 'published',
						'meta_key'    => 'normwort',
						'meta_value'  => $term->name,
						'numberposts' => 0,
					) );
					foreach ( $posts as $post ) {
						if ( $counter > 0 ) {
							echo ', ';
						}
						echo $post->post_title;
						$counter ++;
					}
				}
			}
		}

    	wp_die();
    }
    /**
     * Load HTML from remote site
     *
     * @since   0.0.1
     * @access  public
     * @filters materialpool-ajax-get-description
     */
    public static function my_action_callback_mp_get_description() {
        global $wpdb;

        $url =  esc_url_raw( $_POST['site'] );
        $id =  (int) $_POST['post-id'];
        $anzahl = $wpdb->get_col( $wpdb->prepare( "SELECT count( meta_id ) as anzahl  FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s and post_id = %d", 'material_url', $url, $id) );
        if ( is_array( $anzahl ) && $anzahl[ 0 ] == 0 ) {
            $title = '';
            $description = '';
            $keywords = '';
            $image = '';

            $args = array(
                'user-agent' => 'Mozilla/5.0 (compatible; Materialpool; +' . home_url() . ')',
                'timeout' => 30,
                'sslverify' => false,
            );
            $response = wp_remote_get($url, $args);
            if (!is_wp_error($response)) {
	            $encoding = mb_detect_encoding( $response['body'] );
	            if ( $encoding != 'UTF-8' ) {
		            $body = utf8_decode( $response['body'] );
	            } else {
		            $body = $response['body'];
	            }
                libxml_use_internal_errors(true);
                $doc = new DomDocument();
                $doc->loadHTML($body);
                $xpath = new DOMXPath($doc);
                $query = '//*/meta[starts-with(@property, \'og:\')]';
                $metas = $xpath->query($query);
                foreach ($metas as $meta) {
                    $property = $meta->getAttribute('property');
                    $content = $meta->getAttribute('content');
                    if ($property == 'og:title') {
                        $title = $content;
                    }
                    if ($property == 'og:description') {
                        $description = $content;
                    }
                    if ($property == 'og:video:tag' || $property == 'video:tag') {
                        if ($keywords != '') {
                            $keywords .= ', ';
                        }
                        $keywords .= $content;
                    }
                    if ( ( $property == 'og:image' ) && ( strpos( $content, 'http') === 0 ) ) {
                        $image = $content;
                    }
                }
                $query = '//*/meta';
                $metas = $xpath->query($query);
                foreach ($metas as $meta) {
                    $name = $meta->getAttribute('name');
                    $content = $meta->getAttribute('content');
                    if ($name == 'description' && $description == '') {
                        $description = $content;
                    }
                    if ($name == 'title' && $title == '') {
                        $title = $content;
                    }
                    if ($name == 'keywords' && $keywords == '') {
                        $keywords = $content;
                    }
                }
                $titleNode = $xpath->query('//title');
                if ($title == '') {
                    $title = $titleNode->item(0)->textContent;
                }
                $data = array(
                    'title' => $title,
                    'description' => $description,
                    'keywords' => $keywords,
                    'image' => $image,
                );
            }
            echo json_encode(apply_filters('materialpool-ajax-get-description', $data, $xpath));
        }
        wp_die();
    }


    /**
     *
     * @since   0.0.1
     * @access  public
     * @filters materialpool-ajax-check-url
     */
    public static function my_action_callback_mp_check_url() {
        global $wpdb;
        $url =  esc_url_raw( $_POST['site'] ) ;
        $id =  (int) $_POST['post-id'];

        $anzahl = $wpdb->get_col( $wpdb->prepare( "SELECT count( meta_id ) as anzahl  FROM  $wpdb->postmeta pm, $wpdb->posts p  WHERE pm.meta_key = %s and pm.meta_value = %s and pm.post_id != %d  and pm.post_id= p.ID and p.post_status = 'publish' ", 'material_url', $url, $id) );
        if ( is_array( $anzahl ) && $anzahl[ 0 ] == 0 ) {
            $data = array(
                'status' => "ok"
            );
        } else {
            $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id   FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_url', $url) );
            $data = array(
                'status' => "exists",
                'material_url' => get_permalink( $post_id )
            );

        }
        echo json_encode( apply_filters( 'materialpool-ajax-check-url', $data  ) );
        wp_die();
    }


    /**
     *
     * @since   0.0.1
     * @access  public
     * @filters materialpool-ajax-check-material-title
     */
    public static function my_action_callback_mp_check_material_title() {
        global $wpdb;
        $title =  $_POST['title'];
	    $id =  (int) $_POST['post-id'];

        $anzahl = $wpdb->get_col( $wpdb->prepare( "SELECT count( meta_id ) as anzahl  FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s and post_id != %d ", 'material_titel', $title, $id ) );
        if ( is_array( $anzahl ) && $anzahl[ 0 ] == 0 ) {
            $data = array(
                'status' => "ok"
            );
        } else {
            $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id   FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_titel', $title) );
            $data = array(
                'status' => "exists",
                'material_url' => get_permalink( $post_id )
            );

        }
        echo json_encode( apply_filters( 'materialpool-ajax-check-material-title', $data  ) );
        wp_die();
    }


	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
	public static function my_action_callback_mp_send_autor_mail() {
		$id = (int) $_POST['id'];
		Materialpool_Autor::send_mail( $id );

		$email = get_metadata( 'post', $id, 'autor_email', true );
		if ( $email == '' ) {
			$data = '<div style="color: red;">Keine Email hinterlegt</div>';
		} else {
			$send = get_metadata( 'post', $id, 'autor_email_send', true );
			$read = get_metadata( 'post', $id, 'autor_email_read', true );

			if ( $send == '' ) {
				$data = '<div>Nicht versendet</div>';
				$data .= '<div class="row-actions"><span class="edit"><a  style="cursor: pointer;" data-id="'. $id .'" class="mail_autor_send">Mail versenden</a></span></div>';
			}
			if ( $send != '' && $read == '' ) {
				$data = '<div style="color: blue;">Versendet, ungelesen</div>';
			}
			if ( $send != '' && $read != '' ) {
				$data = '<div style="color: green;">Gelesen</div>';
			}

		}

		echo  $data;
		wp_die();
	}

	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
	public static function my_action_callback_mp_send_organisation_mail() {
		$id = (int) $_POST['id'];
		Materialpool_Organisation::send_mail( $id );

		$email = get_metadata( 'post', $id, 'organisation_email', true );
		if ( $email == '' ) {
			$data = '<div style="color: red;">Keine Email hinterlegt</div>';
		} else {
			$send = get_metadata( 'post', $id, 'organisation_email_send', true );
			$read = get_metadata( 'post', $id, 'organisation_email_read', true );

			if ( $send == '' ) {
				$data = '<div>Nicht versendet</div>';
				$data .= '<div class="row-actions"><span class="edit"><a style="cursor: pointer;" data-id="'. $id .'" class="mail_organisation_send">Mail versenden</a></span></div>';
			}
			if ( $send != '' && $read == '' ) {
				$data = '<div style="color: blue;">Versendet, ungelesen</div>';
			}
			if ( $send != '' && $read != '' ) {
				$data = '<div style="color: green;">Gelesen</div>';
			}
		}

		echo  $data;
		wp_die();
	}


	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
	public static function my_action_callback_mp_change_autor_einverstaendnis() {
		$id = (int) $_POST['id'];

		$einverstaendnis = get_metadata( 'post', $id, 'einverstaendnis', true );
		if ( $einverstaendnis == 1 ) {
            update_metadata( 'post', $id, 'einverstaendnis', 0 );
			$check = " checked=checked ";

		} else {
			update_metadata( 'post', $id, 'einverstaendnis', 1 );
			$check = " ";
        }
		$data = "<div><input data-id=\"". $id ."\" class=\"einverstaendnis_autor\" type='checkbox' $check ></div>";

		echo  $data;
		wp_die();
	}



	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
	public static function my_action_callback_mp_change_organisation_einverstaendnis() {
		$id = (int) $_POST['id'];

		$einverstaendnis = get_metadata( 'post', $id, 'einverstaendnis', true );
		if ( $einverstaendnis == 1 ) {
			update_metadata( 'post', $id, 'einverstaendnis', 0 );
			$check = " checked=checked ";

		} else {
			update_metadata( 'post', $id, 'einverstaendnis', 1 );
			$check = " ";
		}
		$data = "<div><input data-id=\"". $id ."\" class=\"einverstaendnis_organisation\" type='checkbox' $check ></div>";

		echo  $data;
		wp_die();
	}

	/**
     *
     * @since   0.0.1
     * @access  public
     * @filters materialpool-ajax-check-organisation-title
     */
    public static function my_action_callback_mp_check_organisation_title() {
        global $wpdb;
        $title =  $_POST['title'];
        $anzahl = $wpdb->get_col( $wpdb->prepare( "SELECT count( meta_id ) as anzahl  FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'organisation_titel', $title) );
        if ( is_array( $anzahl ) && $anzahl[ 0 ] == 0 ) {
            $data = array(
                'status' => "ok"
            );
        } else {
            $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id   FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'organisation_titel', $title) );
            $data = array(
                'status' => "exists",
                'material_url' => get_permalink( $post_id )
            );

        }
        echo json_encode( apply_filters( 'materialpool-ajax-check-organisation-title', $data  ) );
        wp_die();
    }


    /**
     *
     * @since   0.0.1
     * @access  public
     * @filters materialpool-ajax-check-organisation-title
     */
    public static function my_action_callback_mp_add_thema() {
        global $wpdb;
        $gruppe = (int) $_POST['gruppe'];
        $post = (int) $_POST['post'];

        $thema = Materialpool_Material::get_themengruppe( $gruppe );
        $auswahlArr = explode( ',', $thema[ 'auswahl'] );

        if ( !in_array( $post, $auswahlArr ) ) {
            $auswahlArr[] = $post;
            $auswahl = implode( ',', $auswahlArr );
            $query_str 		= $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'pods_themenseitengruppen`	 	  
										 SET auswahl=%s WHERE id = %s ', $auswahl, $gruppe );
            $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
        }
        wp_die();
    }


    /**
     *
     * @since   0.0.1
     * @access  public
     * @filters materialpool-ajax-check-organisation-title
     */
    public static function my_action_callback_mp_remove_thema() {
        global $wpdb;
        $gruppe = (int) $_POST['gruppe'];
        $post = (int) $_POST['post'];

        $thema = Materialpool_Material::get_themengruppe( $gruppe );
        $auswahlArr = explode( ',', $thema[ 'auswahl'] );

        if ( in_array( $post, $auswahlArr ) ) {
            $auswahlArr = array_flip($auswahlArr);
            unset($auswahlArr[ $post ]);
            $auswahlArr = array_flip($auswahlArr);
            $auswahl = implode( ',', $auswahlArr );
            $query_str 		= $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'pods_themenseitengruppen`	 	  
										 SET auswahl=%s WHERE id = %s ', $auswahl, $gruppe );
            $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
        }
        wp_die();
    }


    /**
     *
     * @since   0.0.1
     * @access  public
     * @filters materialpool-ajax-check-organisation-title
     */
    public static function my_action_callback_mp_remove_thema_backend() {
        global $wpdb;
        $gruppe = (int) $_POST['gruppe'];
        $post = (int) $_POST['post'];

        $thema = Materialpool_Material::get_themengruppe( $gruppe );
        $auswahlArr = explode( ',', $thema[ 'auswahl'] );

        if ( in_array( $post, $auswahlArr ) ) {
            $auswahlArr = array_flip($auswahlArr);
            unset($auswahlArr[ $post ]);
            $auswahlArr = array_flip($auswahlArr);
            $auswahl = implode( ',', $auswahlArr );
            $query_str 		= $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'pods_themenseitengruppen`	 	  
										 SET auswahl=%s WHERE id = %s ', $auswahl, $gruppe );
            $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
        }

        $thema = Materialpool_Material::get_themengruppe( $gruppe );
        foreach ( explode(',', $thema[ 'auswahl'] ) as $materialid ) {
            $post = get_post( $materialid);
            if ( is_object( $post) ) {
                echo "<input type='checkbox' checked='checked' class='uncheck_themenseite  themenseite-cb-backend' ";
                echo " data-gruppe='". $gruppe  ."' data-post='". $post->ID ."'";
                echo  ">";
                echo "<a href='". get_permalink( $post ) . "' target='_new'>" . $post->post_title."</a><br>";
            }
        }

        wp_die();
    }



    /**
     *
     * @since   0.0.1
     * @access  public
     */
    public static function my_action_callback_mp_add_proposal() {
        global $wpdb;
        $url = esc_url_raw( $_POST['url'] );
        $description = sanitize_textarea_field( $_POST['description'] );
        $user = sanitize_textarea_field( $_POST['user'] );
        $email = sanitize_email( $_POST['email'] );
	    $description .= "\n\n";
        $anzahl = $wpdb->get_col( $wpdb->prepare( "SELECT count( meta_id ) as anzahl  FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_url', $url) );
        if ( is_array( $anzahl ) && $anzahl[ 0 ] == 0 ) {
            remove_action( 'save_post', array( 'Materialpool_Material', 'generate_title') );
	        $title = '';
	        $keywords = '';
	        $image = '';

	        $args = array(
		        'user-agent' => 'Mozilla/5.0 (compatible; Materialpool; +' . home_url() . ')',
		        'timeout' => 30,
		        'sslverify' => false,
	        );
	        $response = wp_remote_get($url, $args);
	        if (!is_wp_error($response)) {
		        $body = utf8_decode($response['body']);

		        libxml_use_internal_errors(true);
		        $doc = new DomDocument();
		        $doc->loadHTML(mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8'));
		        $xpath = new DOMXPath($doc);
		        $query = '//*/meta[starts-with(@property, \'og:\')]';
		        $metas = $xpath->query($query);

		        foreach ($metas as $meta) {
			        $property = $meta->getAttribute('property');
			        $content = $meta->getAttribute('content');
			        if ($property == 'og:title') {
				        $title = $content;
			        }
			        if ($property == 'og:description') {
				        $description .=  $content;
			        }
			        if ($property == 'og:video:tag' || $property == 'video:tag') {
				        if ($keywords != '') {
					        $keywords .= ', ';
				        }
				        $keywords .= $content;
			        }
			        if ( ( $property == 'og:image' ) && ( strpos( $content, 'http') === 0 ) ) {
				        $image = $content;
			        }
		        }
		        $query = '//*/meta';
		        $metas = $xpath->query($query);
		        foreach ($metas as $meta) {
			        $name = $meta->getAttribute('name');
			        $content = $meta->getAttribute('content');
			        if ($name == 'description' && $description == '') {
				        $description .= $content;
			        }
			        if ($name == 'title' && $title == '') {
				        $title = $content;
			        }
			        if ($name == 'keywords' && $keywords == '') {
				        $keywords = $content;
			        }
		        }
		        $titleNode = $xpath->query('//title');
		        if ($title == '') {
			        $title = $titleNode->item(0)->textContent;
		        }
		        if ( $title == '' ) {
		            $title = __( 'Der Titel der Webseite konnte nicht automatisch ermittelt werden', Materialpool::$textdomain );
                }
		        $data = array(
			        'title' => $title,
			        'description' => $description,
			        'keywords' => $keywords,
			        'image' => $image,
		        );
	        } else {
	            echo "wperror";
            }

            $back = wp_insert_post(  array(
                'post_status'   => 'vorschlag',
                'post_type'     => 'material',
                'post_title'    => Materialpool::char_replace ( $data[ 'title'] ) ,
                'post_author'   => 1,
                'meta_input'    => array (
                    'material_url'  => $url,
                    'material_titel' => Materialpool::char_replace ( $data[ 'title'] ),
                    'material_beschreibung' => Materialpool::char_replace ( $data[ 'description'] ),
	                'material_von_name' => $user,
	                'material_von_email' => $email
                )
            ) );

            $data = "Vielen Dank f&uuml;r ihren Vorschlag. Ihr Materialvorschlag wird nun Redaktionell geprüft.";

        } else {
            $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id   FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_url', $url) );
            $data = "Vielen Dank f&uuml;r ihren Vorschlag. Das <a href='";
            $data .= get_permalink( $post_id );
            $data .= "'>Material</a> befindet sich bereits im Materialpool.";
        }
        echo ( apply_filters( 'materialpool-ajax-add-proposal', $data  ) );
        wp_die();
    }

    public static function char_replace( $string ) {
 
	    $string = strtr($string, array(
		    '\u00A0'    => ' ',
		    '\u0026'    => '&',
		    '\u003C'    => '<',
		    '\u003E'    => '>',
		    '\u00E4'    => 'ä',
		    '\u00C4'    => 'Ä',
		    '\u00F6'    => 'ö',
		    '\u00D6'    => 'Ö',
		    '\u00FC'    => 'ü',
		    '\u00DC'    => 'Ü',
		    '\u00DF'    => 'ß',
		    '\u20AC'    => '€',
		    '\u0024'    => '$',
		    '\u00A3'    => '£',

		    '\u00a0'    => ' ',
		    '\u003c'    => '<',
		    '\u003e'    => '>',
		    '\u00e4'    => 'ä',
		    '\u00c4'    => 'Ä',
		    '\u00f6'    => 'ö',
		    '\u00d6'    => 'Ö',
		    '\u00fc'    => 'ü',
		    '\u00dc'    => 'Ü',
		    '\u00df'    => 'ß',
		    '\u20ac'    => '€',
		    '\u00a3'    => '£',
	    ));
	    $string = utf8_decode( $string );

	    return $string;
    }
    /**
     *
     * @since   0.0.1
     * @access  public
     * @filters materialpool-ajax-check-organisation-title
     */
    public static function my_action_callback_mp_list_thema_backend() {
        global $wpdb;
        $gruppe = (int) $_POST['gruppe'];

        $thema = Materialpool_Material::get_themengruppe( $gruppe );
        foreach ( explode(',', $thema[ 'auswahl'] ) as $materialid ) {
            $post = get_post( $materialid);
            if ( is_object( $post) ) {
                echo "<input type='checkbox' checked='checked' class='uncheck_themenseite  themenseite-cb-backend' ";
                echo " data-gruppe='". $gruppe  ."' data-post='". $post->ID ."'";
                echo  ">";
                echo "<a href='". get_permalink( $post ) . "' target='_new'>" . $post->post_title."</a><br>";
            }
        }

        wp_die();
    }

    /**
     *
     * @since   0.0.1
     * @access  public
     * @filters materialpool-ajax-check-organisation-title
     */
    public static function my_action_callback_convert2material() {
        global $wpdb;
        $post_id = (int) $_POST['post'];

        $pod = pods( 'material' );
        $postdata = get_post( $post_id );
        $sprache = get_post_meta( $post_id, '_pods_material_sprache', false );
        $bildungsstufe = get_post_meta( $post_id, '_pods_material_bildungsstufe', false );
        $altersstufe = get_post_meta( $post_id, '_pods_material_altersstufe', false );
        $medientype = get_post_meta( $post_id, '_pods_material_medientyp', false  );
        $title = get_post_meta( $post_id, 'material_titel', true );
        $beschreibung = $postdata->post_content;
        $data = array(
            'material_special' => 0,
            'material_titel' => $title,
            'material_kurzbeschreibung' => get_post_meta( $post_id, 'material_kurzbeschreibung', true ),
            'material_beschreibung' => $beschreibung,
            'material_autor_interim' => get_post_meta( $post_id, 'material_autor_interim', true ),
            'material_organisation_interim' => get_post_meta( $post_id, 'material_organisation_interim', true ),
            'material_schlagworte_interim' => get_post_meta( $post_id, 'material_schlagworte_interim', true ),
            'material_url' => get_post_meta( $post_id, 'material_url', true ),
            'material_veroeffentlichungsdatum' => get_post_meta( $post_id, 'material_veroeffentlichungsdatum', true ),
            'material_verfuegbarkeit' => get_post_meta( $post_id, 'material_verfuegbarkeit', true ),
            'old_slug' => get_post_meta( $post_id, 'old_slug', true ),
        );

        wp_delete_post($post_id );
        $material_id = $pod->add( $data );
        $pod = pods( 'material' , $material_id );
        $pod->add_to( 'material_sprache', implode( ',', $sprache[ 0 ] ) );
        $pod->add_to( 'material_medientyp', implode( ',', $medientype[ 0 ] ) );
        $pod->add_to( 'material_bildungsstufe', implode( ',', $bildungsstufe[ 0 ] ) );
        $pod->add_to( 'material_altersstufe', implode( ',', $altersstufe[ 0 ] ) );
        // Zu Handverlesen hinzufügen
        $pod->add_to( 'material_vorauswahl', 2206 );

        $post_type = get_post_type($material_id);
        $post_parent = wp_get_post_parent_id( $material_id );
        $post_name = wp_unique_post_slug( sanitize_title( $title ), $material_id, 'publish', $post_type, $post_parent );

        wp_publish_post( $material_id);

        $x = $wpdb->update(
            $wpdb->posts,
            array(
                'post_title' => stripslashes( $title ),
                'post_name' => $post_name,
                'post_content' => $beschreibung,
            ),
            array( 'ID' => $material_id ),
            array(
                '%s',
                '%s'
            ),
            array( '%d' )
        );

        // Altersstufen des Materials in term_rel speichern
        wp_delete_object_term_relationships( $material_id, 'altersstufe' );
        $cats = $altersstufe[ 0 ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( is_int( $cats ) ) {
            $cat_ids[] = $cats;
        }
        wp_set_object_terms( $material_id, $cat_ids, 'altersstufe', true );

        // Bildungsstufen des Materials in term_rel speichern
        wp_delete_object_term_relationships( $material_id, 'bildungsstufe' );
        $cats =  $bildungsstufe[ 0 ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( is_int( $cats ) ) {
            $cat_ids[] = $cats;
        }
        wp_set_object_terms( $material_id, $cat_ids, 'bildungsstufe', true );

        // Medientyp des Materials in term_rel speichern
        wp_delete_object_term_relationships( $material_id, 'medientyp' );
        $cats = $medientype;
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $material_id, $cat_ids, 'medientyp', true );


        // Sprachen des Materials in term_rel speichern
        wp_delete_object_term_relationships( $material_id, 'sprache' );
        $cats = $sprache;
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $material_id, $cat_ids, 'sprache', true );


        // Vorauswahl des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'vorauswahl' );
        $cats = 2206;
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'vorauswahl', true );


	    Materialpool_Material::set_createdate( $material_id  );

        echo json_encode( get_edit_post_link( $material_id, 'use' ) );
        wp_die();
    }


	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
    public static function my_action_callback_check_autor_request() {
	    global $wpdb;

	    $vorname = sanitize_text_field( $_REQUEST['vorname'] );
	    $name    = sanitize_text_field( $_REQUEST['name'] );
	    $userID  = (int) $_REQUEST['user'];
	    $email   = sanitize_email( $_REQUEST['email'] );

	    // check if user avaliable
        if ( $userID == 0 ) {
            echo "Fehler: kein gültiger Benutzer";
            wp_die();
        }

        $autorlink = get_user_meta( $userID, 'autor_link', true );
        if ( $autorlink != '' ) {
            echo "Fehler: Benutzer ist schon mit AutorIn verknüpft.";
            wp_die();
        }

        // check if possible that author exists
	    $query = new WP_Query(
	            array(
	                    'post_type' => 'autor',
	                    'meta_query' => array(
		                    'relation' => 'AND',
		                    array(
			                    'key'     => 'autor_vorname',
			                    'value'   => $vorname,
			                    'compare' => 'LIKE',
		                    ),
		                    array(
			                    'key'     => 'autor_nachname',
			                    'value'   => $name,
			                    'compare' => 'LIKE',
		                    ),
	                    ),
                    )
        );

	    if ( $query->have_posts() ) {
	        echo '<br>Eventuell sind sie schon als AutorIn erfasst. Bitte Überprüfen sie folgende Autoreneinträge.<br>';
		    echo '<ul>';
		    while ( $query->have_posts() ) {
			    $query->the_post();
			    echo '<li><a href="'. get_permalink().'" target="_new">' . get_the_title() . '</a></li>';
		    }
		    echo '</ul>';
		    echo '<br>';
		    echo '<a href="#" class="materialpoolautorregister2">Ich bin noch nicht erfasst, bitte neue/n AutorIn anlegen</a>';
	    } else {
		    // Check Gravatar
		    $hash = md5(strtolower(trim($email)));
		    $uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
		    $headers = @get_headers($uri);
		    if (!preg_match("|200|", $headers[0])) {
			    $gravatar = '';
		    } else {
			    $gravatar = $uri;
		    }
		    $pod = pods( 'autor' );
		    $title = $vorname . ' ' . $name;
		    $data = array(
			    'autor_vorname' => $vorname,
			    'autor_nachname' => $name,
			    'autor_email' => $email,
			    'autor_bild_url' => $gravatar,
		    );
		    $autor_id = $pod->add( $data );
		    $post_type = get_post_type($autor_id);
		    $post_parent = wp_get_post_parent_id( $autor_id );
		    $post_name = wp_unique_post_slug( sanitize_title( $title ), $autor_id, 'publish', $post_type, $post_parent );

		    wp_publish_post( $autor_id);

		    $x = $wpdb->update(
			    $wpdb->posts,
			    array(
				    'post_title' => stripslashes( $title ),
				    'post_name' => $post_name,
				    'post_content' => $title,
			    ),
			    array( 'ID' => $autor_id ),
			    array(
				    '%s',
				    '%s'
			    ),
			    array( '%d' )
		    );

		    // connect author to user
		    add_post_meta( $autor_id, 'user_link', $userID );
		    add_post_meta( $autor_id, 'user_status', 'ok' );
		    add_user_meta( $userID, 'autor_link', $autor_id );
		    add_user_meta( $userID, 'autor_status', 'ok' );
		    $hash = password_hash( $userID . '___' . $autor_id, PASSWORD_DEFAULT );
		    add_user_meta( $userID, 'autor_hash', $hash );

		    if ( is_object( FWP() ) ) {
			    FWP()->indexer->save_post( $autor_id );
		    }

		    echo 'AutorIn wurde angelegt und mit dem/der BenutzerIn verknüpft.';
        }

	    wp_die();
    }


	/**
	 *
	 * @since   0.0.1
	 * @access  public
	 */
	public static function my_action_callback_check_autor_request2() {
		global $wpdb;

		$vorname = sanitize_text_field( $_REQUEST['vorname'] );
		$name    = sanitize_text_field( $_REQUEST['name'] );
		$userID  = (int) $_REQUEST['user'];
		$email   = sanitize_email( $_REQUEST['email'] );

		// check if user avaliable
		if ( $userID == 0 ) {
			echo "Fehler: kein gültiger Benutzer";
			wp_die();
		}

		$autorlink = get_user_meta( $userID, 'autor_link', true );
		if ( $autorlink != '' ) {
			echo "Fehler: Benutzer ist schon mit AutorIn verknüpft.";
            wp_die();
		}

		// Check Gravatar
		$hash = md5(strtolower(trim($email)));
		$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
		$headers = @get_headers($uri);
		if (!preg_match("|200|", $headers[0])) {
			$gravatar = '';
		} else {
			$gravatar = $uri;
		}
		$pod = pods( 'autor' );
		$title = $vorname . ' ' . $name;
		$data = array(
		    'autor_vorname' => $vorname,
            'autor_nachname' => $name,
            'autor_email' => $email,
            'autor_bild_url' => $gravatar,
        );
		$autor_id = $pod->add( $data );
		$post_type = get_post_type($autor_id);
		$post_parent = wp_get_post_parent_id( $autor_id );
		$post_name = wp_unique_post_slug( sanitize_title( $title ), $autor_id, 'publish', $post_type, $post_parent );

		wp_publish_post( $autor_id);

		$x = $wpdb->update(
			$wpdb->posts,
			array(
				'post_title' => stripslashes( $title ),
				'post_name' => $post_name,
				'post_content' => $title,
			),
			array( 'ID' => $autor_id ),
			array(
				'%s',
				'%s'
			),
			array( '%d' )
		);

		// connect author to user
		add_post_meta( $autor_id, 'user_link', $userID );
		add_post_meta( $autor_id, 'user_status', 'ok' );
		add_user_meta( $userID, 'autor_link', $autor_id );
		add_user_meta( $userID, 'autor_status', 'ok' );
		$hash = password_hash( $userID . '___' . $autor_id, PASSWORD_DEFAULT );
		add_user_meta( $userID, 'autor_hash', $hash );

		if ( is_object( FWP() ) ) {
			FWP()->indexer->save_post( $autor_id );
		}

		echo 'AutorIn wurde angelegt und mit dem/der BenutzerIn verknüpft.';
		wp_die();
	}

	/**
	 * Creates an Instance of this Class
	 *
	 * @since   0.0.1
	 * @access  public
	 * @return  Materialpool
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
     *
     * @since   0.0.1
     * @access  public
     *
     * Register and enqueue style sheet.
     */
    public static function register_admin_plugin_styles() {
        wp_register_style( 'rw-materialpool', Materialpool::$plugin_url . 'css/backend.css' );
        wp_enqueue_style( 'rw-materialpool' );
        wp_enqueue_script( 'rw-materialpool-js', Materialpool::$plugin_url . 'js/materialpool.js' );
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style("wp-jquery-ui-dialog");
    }


    /**
     *
     * @since   0.0.1
     * @access  public
     *
     * Register and enqueue style sheet.
     */
    public static function register_frontend_plugin_styles() {
        wp_enqueue_script( 'rw-materialpool-js', Materialpool::$plugin_url . 'js/materialpool-frontend.js' );
	    wp_enqueue_script( 'rw-materialpool-js-jq-loading', Materialpool::$plugin_url . 'js/jquery.loading.min.js' );
	    wp_enqueue_script( 'rw-materialpool-js-loading', Materialpool::$plugin_url . 'js/loading.min.js' );
	    wp_register_style( 'rw-materialpool-loading', Materialpool::$plugin_url . 'css/loading.min.css' );
	    wp_enqueue_style( 'rw-materialpool-loading' );
    }

    /**
     *
     * @since   0.0.1
     * @access  public
     */
    public static function promote_feeds() {
        $post_types = array('material', 'organisation', 'autor');
        foreach( $post_types as $post_type ) {
            $feed = get_post_type_archive_feed_link( $post_type );
            if ( $feed === '' || !is_string( $feed ) ) {
                $feed =  get_bloginfo( 'rss2_url' ) . "?post_type=$post_type";
            }
            printf(__('<link rel="%1$s" type="%2$s" href="%3$s" />'),"alternate","application/rss+xml",$feed);
        }
    }

    /**
     *
     * @since   0.0.1
     * @access  public
     */
    public static function get_crossdomain_viewer_url(){
        global $wp_version;

        if(isset($_GET['vsviewer_url'])){
            $url = urldecode( $_GET['vsviewer_url'] );

            //@todo check url in materialpool

            $file_name=substr (strrchr ($url, "/"), 1);

            $args = array(
                'user-agent' => 'Mozilla/5.0 (compatible; Materialpool; )',
                'timeout' => 30,
                'sslverify' => false,
            );
            $response = wp_remote_get( $url, $args );
            if( is_array($response) ) {
                header("Content-type:application/pdf");
                header("Content-Disposition:inline;filename='$file_name'");
                print $response['body'];
                die();
            }
        }
    }

    public static function custom_cron_job_recurrence( $schedules ) {
		$schedules['fivemin'] = array(
			'display' => __( '5 Minutes', Materialpool::$textdomain ),
			'interval' => 300,
		);
		return $schedules;
	}

    /**
     *
     * @since   0.0.1
     * @access  public
     */
    public static function viewerjs_shortcode_handler($args) {
        global $viewerjs_plugin_url;

        $uri = parse_url(urldecode($args[0]));
        $host = $uri['host'];
        $doc = substr(strrchr($uri['path'], '/'),1);

        $document_url = home_url().'/?vsviewer_url='.urlencode( $args[0] );
        $options = get_option('ViewerJS_PluginSettings');
        $iframe_width = $options['width'];
        $iframe_height = $options['height'];
	    $script = "<script>
					jQuery(document).ready(function($){
	    				$('iframe.viewerjs-frame').contents().find('#documentName').css('display','none');
	    				$('iframe.viewerjs-frame').contents().find('#documentName').html('');
					});
					</script>";


        return "<iframe class=\"viewerjs-frame\" src=\"$viewerjs_plugin_url" .
            '#' . $document_url .'" '.
            "width=\"$iframe_width\" ".
            "height=\"$iframe_height\" ".
            'style="border: 1px solid black; border-radius: 5px;" '.
            'allowfullscreen="true" '.
            '></iframe><p class="viewerjsurlmeta">Quelle: <span class="viewerjsurl"><a href="'.$args[0].'">'.$host.' : '.$doc.'</a></span></p>'.$script;

    }

    public static function mp_screenshot_generation() {
         global $wpdb;

	    $secret =  get_option( 'einstellungen_urlbox_secret') ;
	    $key = get_option( 'einstellungen_urlbox_key') ;
        $screenshotapi_key = get_option( 'einstellungen_screenshotapi_key') ;
	    $screenshotlayersecret =  get_option( 'einstellungen_screenshot_layer_secret') ;
	    $screenshotlayerkey = get_option( 'einstellungen_screenshot_layer_key') ;

        if ( $secret == '' || $key == '' || $screenshotapi_key == '' ) return;


	    $count = 0;
	    $result = $wpdb->get_results("
        SELECT distinct($wpdb->posts.ID)   FROM 
	$wpdb->posts, $wpdb->postmeta 
WHERE 
	$wpdb->posts.ID = $wpdb->postmeta.post_id AND  
	$wpdb->posts.post_type = 'material' AND
	( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' )  AND
(
	( 
	(
	   not exists( select * from wp_postmeta where meta_key='material_v2_screesnhot_gen' and post_id = wp_posts.ID )
	 OR  
		( 
			wp_postmeta.meta_key = 'material_v2_screesnhot_gen' AND 
			wp_postmeta.meta_value = ''  
		)
		) 
	)
)	AND 
( 
			wp_postmeta.meta_key = 'material_special' AND 
			wp_postmeta.meta_value = 0
		)
order by wp_posts.ID  desc  limit 0, 10 ") ;
	    foreach ( $result as $obj ) {
            $id = $obj->ID;
		    $post = get_post( $id );

		    $url = get_metadata( 'post', $id, 'material_url', true );

		    if ( Materialpool::endsWith( $url, '.pdf' )  || Materialpool::endsWith( $url, '.odt' ) || Materialpool::endsWith( $url, '.doc' ) || Materialpool::endsWith( $url, '.docx' ) ) {
                $params['url'] =  urlencode($url);
                $params['viewport']  = '1280x1024';
                foreach($params as $key => $value) { $parts[] = "$key=$value"; }
                $query = implode("&", $parts);
                $secret_key = md5($url . $screenshotlayersecret);

                $requesturl = "https://api.screenshotlayer.com/api/capture?access_key=$screenshotlayerkey&secret_key=$secret_key&$query";

			    $ch = curl_init($requesturl );
			    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


			    $result = curl_exec($ch);
			    $result = json_decode( $result );
			    $key = $result->key;
			    add_post_meta( $id, 'material_v2_screesnhot_key', $key );
			    add_post_meta( $id, 'material_v2_screesnhot_gen', 'working' );
            } else {

                $urlbox = Urlbox::fromCredentials($key, $secret );
                $options['url'] = $url;
                $options['width'] = 1280;
                $options['height'] = 1024;
                $options['delay'] = 5000;
                $options['flash'] = true;
                $options['cookie'] = "rw-dsgvo=yes";

                $urlboxUrl = $urlbox->generateUrl($options);
                $ch = curl_init($urlboxUrl );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);

                add_post_meta( $id, 'material_v2_screesnhot_url', $urlboxUrl );
                add_post_meta( $id, 'material_v2_screesnhot_gen', 'generating' );
		    }
	    }

	    // Bilder holen
	    $result = $wpdb->get_results("
        SELECT distinct($wpdb->posts.ID)   FROM 
	$wpdb->posts, $wpdb->postmeta 
WHERE 
	$wpdb->posts.ID = $wpdb->postmeta.post_id AND  
	$wpdb->posts.post_type = 'material' AND
	( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' )  AND (

  
		( 
			wp_postmeta.meta_key = 'material_v2_screesnhot_gen' AND 
			wp_postmeta.meta_value = 'generating'  
		)
  OR 
		( 
			wp_postmeta.meta_key = 'material_v2_screesnhot_gen' AND 
			wp_postmeta.meta_value = 'working'  
		)

)

order by wp_posts.ID  desc   limit 0, 20") ;
	        foreach ( $result as $obj ) {
                $id = $obj->ID;
                $bildurl = get_metadata( 'post', $id, 'material_v2_screesnhot_url', true );
		        $url = get_metadata( 'post', $id, 'material_url', true );
		        if ( Materialpool::endsWith( $url, '.pdf' )  || Materialpool::endsWith( $url, '.odt' ) || Materialpool::endsWith( $url, '.doc' ) || Materialpool::endsWith( $url, '.docx' ) ) {

			        $key = get_metadata( 'post', $id, 'material_v2_screesnhot_key', true );
			        $ch2 = curl_init( 'https://api.screenshotapi.io/retrieve?key=' . $key );
			        curl_setopt( $ch2, CURLOPT_RETURNTRANSFER, true );
			        curl_setopt( $ch2, CURLOPT_HTTPHEADER, array(
					        'apikey: ' . $screenshotapi_key
				        )
			        );
			        $result = curl_exec( $ch2 );
			        $result = json_decode( $result );
			        if  ( $result->status == 'ready' ) {
				        // Bild runterladen
				        $img = WP_CONTENT_DIR . '/screenshots/'. $id . '.png';
				        $ch = curl_init( $result->imageUrl );
				        $fp = fopen($img, 'wb');
				        curl_setopt($ch, CURLOPT_FILE, $fp);
				        curl_setopt($ch, CURLOPT_HEADER, 0);
				        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				        curl_exec($ch);
				        curl_close($ch);
				        fclose($fp);
				        update_post_meta( $id, 'material_v2_screesnhot_gen', 'ready');
				        update_post_meta( $id, 'material_screenshot', WP_CONTENT_URL . '/screenshots/'. $id . '.png' );
				        // Caches verwerfen
				        // Transients für Backendliste löschen
				        delete_transient( 'mp-cpt-list-material-autor-'.$id );
				        delete_transient( 'mp-cpt-list-material-medientyp-'.$id );
				        delete_transient( 'mp-cpt-list-material-medientyp-'.$id );
				        delete_transient( 'mp-cpt-list-material-schlagworte-'.$id );
				        delete_transient( 'mp-cpt-list-material-organisation-'.$id );
				        // Transients für Frontendcache löschen
				        delete_transient( 'facet_serach2_entry-'.$id );
				        delete_transient( 'rss_material_entry-'.$id );
				        delete_transient( 'facet_autor_entry-'.$id );
				        delete_transient( 'facet_themenseite_entry-'.$id );
				        delete_transient( 'facet_organisation_entry-'.$id );
			        }

		        } else {
			        // Bild runterladen
			        $img = WP_CONTENT_DIR . '/screenshots/' . $id . '.png';


			        $ch = curl_init( $bildurl );
			        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
			        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			        $result = curl_exec( $ch );
			        //if ( Materialpool::isJson( $result ) ) return;

			        $ch = curl_init( $bildurl );
			        $fp = fopen( $img, 'wb' );
			        curl_setopt( $ch, CURLOPT_FILE, $fp );
			        curl_setopt( $ch, CURLOPT_HEADER, 0 );
			        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			        curl_exec( $ch );
			        curl_close( $ch );
			        fclose( $fp );
			        update_post_meta( $id, 'material_v2_screesnhot_gen', 'ready' );
			        update_post_meta( $id, 'material_screenshot', WP_CONTENT_URL . '/screenshots/' . $id . '.png' );

			        // Caches verwerfen

			        // Transients für Backendliste löschen
			        delete_transient( 'mp-cpt-list-material-autor-' . $id );
			        delete_transient( 'mp-cpt-list-material-medientyp-' . $id );
			        delete_transient( 'mp-cpt-list-material-medientyp-' . $id );
			        delete_transient( 'mp-cpt-list-material-schlagworte-' . $id );
			        delete_transient( 'mp-cpt-list-material-organisation-' . $id );

			        // Transients für Frontendcache löschen
			        delete_transient( 'facet_serach2_entry-' . $id );
			        delete_transient( 'rss_material_entry-' . $id );
			        delete_transient( 'facet_autor_entry-' . $id );
			        delete_transient( 'facet_themenseite_entry-' . $id );
			        delete_transient( 'facet_organisation_entry-' . $id );
		        }
            }
	    }

	public static  function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	public static function endsWith($check, $endStr) {
		if (!is_string($check) || !is_string($endStr) || strlen($check)<strlen($endStr)) {
			return false;
		}

		return (substr($check, strlen($check)-strlen($endStr), strlen($endStr)) === $endStr);
	}

} // end Class


if ( class_exists( 'Materialpool' ) ) {

    global $SearchWP_Materialpool_Synonyms_Flag;
    $SearchWP_Materialpool_Synonyms_Flag= false;
	add_action( 'plugins_loaded', array( 'Materialpool', 'get_instance' ) );

	require_once 'classes/Materialpool_Autoloader.php';

	Materialpool_Autoloader::register();

	register_activation_hook( __FILE__, array( 'Materialpool_Installation', 'on_activate' ) );
	register_uninstall_hook(  __FILE__,	array( 'Materialpool_Installation', 'on_uninstall' ) );
	register_deactivation_hook( __FILE__, array( 'Materialpool_Installation', 'on_deactivation' ) );
}

/**
 *
 * @since 0.0.1
 * @access	public
 *
 */
function rw_mp_row_actions( $actions, $post )
{
    if ( 'themenseite' === $post->post_type ) {
        unset ( $actions[ "inline hide-if-no-js" ] );
    }

    if ( 'synonym' === $post->post_type ) {
        unset ( $actions[ "inline hide-if-no-js" ] );
    }
    if ( 'material' === $post->post_type ) {
        unset ( $actions[ "inline hide-if-no-js" ] );
    }
    if ( 'autor' === $post->post_type ) {
        unset ( $actions[ "inline hide-if-no-js" ] );
    }
    if ( 'organisation' === $post->post_type ) {
        unset ( $actions[ "inline hide-if-no-js" ] );
    }
    return $actions;
}

/**
 *
 *
 * Register as Class method throws an error
 *
 * @since   0.0.1
 * @param $type
 * @param $name
 */
function materialpool_pods_material_metaboxes ( $type, $name ) {
    pods_group_add( 'material', __( 'Base', Materialpool::get_textdomain() ), 'material_url,material_no_viewer,material_special, material_titel,material_kurzbeschreibung,material_beschreibung,material_von_name,material_von_email' );
    pods_group_add( 'material', __( 'Owner', Materialpool::get_textdomain() ), 'material_autoren,material_autor_interim,material_organisation,material_organisation_interim' );
    pods_group_add( 'material', __( 'Meta', Materialpool::get_textdomain() ), 'material_schlagworte,schlagwortsynonyme, material_schlagworte_interim,material_bildungsstufe,material_kompetenz,material_altersstufe,material_medientyp,material_sprache,material_vorauswahl' );
    pods_group_add( 'material', __( 'Advanced Meta', Materialpool::get_textdomain() ), 'material_inklusion,material_verfuegbarkeit,material_zugaenglichkeit,material_lizenz, material_werkzeug' );
	pods_group_add( 'material', __( 'Additional Meta', Materialpool::get_textdomain() ), 'material_rubrik' );
    pods_group_add( 'material', __( 'Date', Materialpool::get_textdomain() ), 'material_veroeffentlichungsdatum,material_jahr, material_erstellungsdatum,material_depublizierungsdatum,material_wiedervorlagedatum' );
    pods_group_add( 'material', __( 'Relationships', Materialpool::get_textdomain() ), 'material_werk,material_band,material_verweise' );
    pods_group_add( 'material', __( 'Image', Materialpool::get_textdomain() ), 'material_cover,material_cover_url,material_cover_quelle,material_screenshot' );
}


function mb_endsWith($check, $endStr) {
    if (!is_string($check) || !is_string($endStr) || mb_strlen($check)<mb_strlen($endStr)) {
        return false;
    }

    return (mb_substr($check, mb_strlen($check)-mb_strlen($endStr), mb_strlen($endStr)) === $endStr);
}


//add_action( 'rest_api_init', 'rest_api_filter_add_filters' );
/**
 * Add the necessary filter to each post type
 **/
function rest_api_filter_add_filters() {
	foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
		add_filter( 'rest_' . $post_type->name . '_query', 'rest_api_filter_add_filter_param', 10, 2 );
	}
}
/**
 * Add the filter parameter
 *
 * @param  array           $args    The query arguments.
 * @param  WP_REST_Request $request Full details about the request.
 * @return array $args.
 **/
function rest_api_filter_add_filter_param( $args, $request ) {
	// Bail out if no filter parameter is set.
	if ( empty( $request['filter'] ) || ! is_array( $request['filter'] ) ) {
		return $args;
	}
	$filter = $request['filter'];
	if ( isset( $filter['posts_per_page'] ) && ( (int) $filter['posts_per_page'] >= 1 && (int) $filter['posts_per_page'] <= 100 ) ) {
		$args['posts_per_page'] = $filter['posts_per_page'];
	}
	global $wp;
	$vars = apply_filters( 'query_vars', $wp->public_query_vars );
	foreach ( $vars as $var ) {
		if ( isset( $filter[ $var ] ) ) {
			$args[ $var ] = $filter[ $var ];
		}
	}
	return $args;
}

// Function to register our new routes from the controller.
function register_mymaterial_rest_routes() {
	$controller = new Materialpool_REST_MyMaterial();
	$controller->register_routes();
}



add_filter('acf/load_value/key=field_5dbc8eedaf43e', 'set_verfuegbarkeit_default', 20, 3);
function set_verfuegbarkeit_default( $value, $post_id, $field ) {
	if ($value === null && get_post_status($post_id) == 'auto-draft' && get_post_status($post_id) != 'publish') {
		$value = 51;
	}
	return $value;
}


remove_action('wp_ajax_acf/fields/taxonomy/add_term', array('acf_field_taxonomy', 'ajax_add_term')) ;

add_action('wp_ajax_acf/fields/taxonomy/add_term', 'ajax_add_term', 1) ;
function ajax_add_term() {
	global $wpdb;
	// vars
	$args = wp_parse_args($_POST, array(
		'nonce'				=> '',
		'field_key'			=> '',
		'term_name'			=> '',
		'term_parent'		=> ''
	));

	// verify nonce
	if( !acf_verify_ajax() ) {
		die();
	}

	// load field
	$field = acf_get_field( $args['field_key'] );
	if( !$field ) {
		die();
	}

	if ( $args['field_key'] != 'field_5dbc888798a2f' ) {
	    return;
    }
	// vars
	$taxonomy_obj = get_taxonomy($field['taxonomy']);
	$taxonomy_label = $taxonomy_obj->labels->singular_name;

	// validate cap
	// note: this situation should never occur due to condition of the add new button
	if( !current_user_can( $taxonomy_obj->cap->manage_terms) ) {
		wp_send_json_error(array(
			'error'	=> sprintf( __('User unable to add new %s', 'acf'), $taxonomy_label )
		));
	}

	// save?
	if( $args['term_name'] ) {
		// exists
		if( term_exists($args['term_name'], $field['taxonomy'], $args['term_parent']) ) {
			wp_send_json_error(array(
				'error'	=>  sprintf( __('%s already exists', 'acf'), $taxonomy_label )
			));
		} else {

            // Synonyme prüfen.
            // 2. SynonymDB prüfen
            $postids=$wpdb->get_col( $wpdb->prepare(
                "
                    SELECT      p.ID
                    FROM        {$wpdb->posts} p
                    WHERE       p.post_title = %s 
                    ",
                $args['term_name']
            ) );

            if ( count( $postids) != 0 ) {
	            foreach ( $postids as $id ) {
		            $post     = get_post( $id );
		            $normwort = get_post_meta( $id, "normwort", true );
		            // Prüfen ob Normwort als Schlagwort existiert.
		            $keyword = get_term_by( 'name', $normwort, 'schlagwort' );
		            if ( $keyword !== false ) {
			            $output['id'] = $keyword->term_id;
		            }
	            }
	            // load term
	            $term = get_term( $output['id'] );

	            $prefix    = '';
	            $ancestors = get_ancestors( $term->term_id, $term->taxonomy );
	            if ( ! empty( $ancestors ) ) {
		            $prefix = str_repeat( '- ', count( $ancestors ) );
	            }

	            // success
	            wp_send_json_success( array(
		            'message'     => sprintf( __( '%s added', 'acf' ), $taxonomy_label ),
		            'term_id'     => $term->term_id,
		            'term_name'   => $term->name,
		            'term_label'  => $prefix . $term->name,
		            'term_parent' => $term->parent
	            ) );
            } else {
                // Bibliothek prüfen und Normwort und Synonyme ermittln.
	            $gnd = wp_remote_get( "https://xgnd.bsz-bw.de/Anfrage?suchfeld=pica.swr&suchwort=" . $args['term_name'] );
	            $gndObj = json_decode( $gnd[ 'body'] );
	            if ( is_array( $gndObj )) {
		            $treffer = 0;
		            foreach ( $gndObj as $obj ) {
			            if ( $obj->Typ == 'Sachschlagwort' ) {
				            $treffer = 1;
				            $normwort = $obj->Ansetzung;
				            foreach ( $obj->Synonyme  as $key => $value ) {
					            // Prüfen ob Synonym noch nicht gespeichert ist
					            $postids=$wpdb->get_col( $wpdb->prepare(
						            "
                                SELECT      p.ID
                                FROM        $wpdb->posts p
                                WHERE       p.post_title = %s 
                                ",
						            $value
					            ) );
					            if ( sizeof( $postids ) == 0 ) {
						            // Synonym speichern
						            $my_post = array(
							            'post_title'    => wp_strip_all_tags( $value ),
							            'post_status'   => 'publish',
							            'post_type'     => 'synonym',
						            );
						            $back = wp_insert_post( $my_post );
						            if ( is_int( $back ) ) {
							            $dummy = add_post_meta( $back, "normwort", $normwort, true );
						            }
					            }
				            }
			            }
			            // Normwort noch als Schlagwort speichern
			            $newterm = wp_insert_term( $normwort, 'schlagwort' );

			            $output[ 'id' ] = $newterm[ 'term_id' ];
			            $term = get_term( $output[ 'id' ] );
			            wp_send_json_success( array(
				            'message'     => sprintf( __( '%s added', 'acf' ), $taxonomy_label ),
				            'term_id'     => $term->term_id,
				            'term_name'   => $term->name,
				            'term_label'  => $term->name,
				            'term_parent' => $term->parent
			            ) );
		            }
	            } else {
		            wp_send_json_error( array(
			            'error'     => sprintf( __( 'Das Normwort konnte nicht ermittlet werden. Bitte recherchiere <a  target=\'_blank\' href=\'https://xgnd.bsz-bw.de/Anfrage\'>selbst</a>, ob das Schlagwort korrekt ist.', 'rw-materialpool' )),
		            ) );
	            }
            }
		}
	}

	?><form method="post"><?php

	acf_render_field_wrap(array(
		'label'			=> __('Name', 'acf'),
		'name'			=> 'term_name',
		'type'			=> 'text'
	));


	if( is_taxonomy_hierarchical( $field['taxonomy'] ) ) {

		$choices = array();
		$response = $this->get_ajax_query($args);

		if( $response ) {

			foreach( $response['results'] as $v ) {

				$choices[ $v['id'] ] = $v['text'];

			}

		}

		acf_render_field_wrap(array(
			'label'			=> __('Parent', 'acf'),
			'name'			=> 'term_parent',
			'type'			=> 'select',
			'allow_null'	=> 1,
			'ui'			=> 0,
			'choices'		=> $choices
		));

	}


	?><p class="acf-submit">
    <button class="acf-submit-button button button-primary" type="submit"><?php _e("Add", 'acf'); ?></button>
    </p>
    </form><?php


	// die
	wp_die();

}



function acf_save_werk( $post_id ) {
    global $wpdb;

	$werk = get_field('material_werk', $post_id);
    if ( $werk != '' ) {
	    $wpdb->update($wpdb->posts, array('post_parent'=> $werk ), array('ID' => $post_id));
	    // Dem Werk nun dieses Material als (weiteren) Band zuordnen
	    $band = get_field( "material_band", $werk  );
	    if ( $band === null || $band === '' ) {
		    update_field('material_band', $post_id, $werk);
	    } else {
		    $band = (array) $band;
		    $band[] = $post_id;
		    update_field('material_band', $band, $werk);
	    }
    }

	$band = get_field('material_band', $post_id);
	if ( $band != '' ) {
		if ( is_array( $band ) ) {
			foreach ( $band as $key ) {
				$wpdb->update($wpdb->posts, array('post_parent'=> $post_id ), array('ID' => $key));
				$a = update_field( 'material_werk', $key, $post_id );
			}
		} else {
			$wpdb->update($wpdb->posts, array('post_parent'=> $post_id ), array('ID' => $band));
			$a = update_field( 'material_werk', $band, $post_id );
		}
	}
    return;
}

add_filter('acf/post2post/update_relationships/key=field_5dbc96653f1ea', '__return_false');  // Werk
add_filter('acf/post2post/update_relationships/key=field_5dbc968c8d64a', '__return_false');  // Band
add_action('acf/save_post', 'acf_save_werk' );


function themenseiten_query( $args, $field, $post_id ) {
    if ( isset( $args['s'] ) ) {
        // Material URL?
	    $id = url_to_postid( $args['s'] );
	    if ( $id != 0 ) {
		    $args['page_id'] = $id;
		    $args['s'] = '';
		    return $args;
        }
	    // Object ID
        if ( is_numeric(  $args['s'] ) ) {
            $p = get_post( (int) $args['s'] );
            if (is_object( $p ) ) {
	            $args['page_id'] = $p->ID;
	            $args['s'] = '';
	            return $args;
            }
        }
        // Comma separated list with Object IDs
	    if (strpos( $args['s'], ',') !== false) {
		    $exploded = explode(',', $args['s']);
		    // Check all values are numeric
            $check = true;
            foreach ( $exploded as $item ) {
                if ( ! is_numeric( $item ) ) {
                    $check = false;
                }
            }
            if ( $check === false ) {
                return $args;
            }
		    $args['post__in'] = $exploded;
		    $args['s'] = '';
		    return $args;
	    }
	    // check remote url
        if ( preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $args['s'] ) ) {
	        $args['meta_key'] = 'material_url';
	        $args['meta_value'] = $args['s'];
	        $args['meta_compare'] = '=';
            $args['s'] = '';
            return $args;
        }
    }
	// return
	return $args;
}
add_filter('acf/fields/relationship/query/key=field_5dcbda53857d4', 'themenseiten_query', 10, 3);

function autoren_query( $args, $field, $post_id ) {
	if ( isset( $args['s'] ) ) {
		$id = url_to_postid( $args['s'] );
		if ( $id != 0 ) {
			$args['page_id'] = $id;
			$args['s'] = '';
		}
		// Object ID
		if ( is_numeric(  $args['s'] ) ) {
			$p = get_post( (int) $args['s'] );
			if (is_object( $p ) ) {
				$args['page_id'] = $p->ID;
				$args['s'] = '';
				return $args;
			}
		}
		// Comma separated list with Object IDs
		if (strpos( $args['s'], ',') !== false) {
			$exploded = explode(',', $args['s']);
			// Check all values are numeric
			$check = true;
			foreach ( $exploded as $item ) {
				if ( ! is_numeric( $item ) ) {
					$check = false;
				}
			}
			if ( $check === false ) {
				return $args;
			}
			$args['post__in'] = $exploded;
			$args['s'] = '';
			return $args;
		}
	}
	// return
	return $args;
}
add_filter('acf/fields/relationship/query/key=field_5dbc83e609b8b', 'autoren_query', 10, 3);
add_filter('acf/fields/relationship/query/key=field_5dcd86fc4f69b', 'autoren_query', 10, 3);

function organisation_query( $args, $field, $post_id ) {
	if ( isset( $args['s'] ) ) {
		$id = url_to_postid( $args['s'] );
		if ( $id != 0 ) {
			$args['page_id'] = $id;
			$args['s'] = '';
		}
		// Object ID
		if ( is_numeric(  $args['s'] ) ) {
			$p = get_post( (int) $args['s'] );
			if (is_object( $p ) ) {
				$args['page_id'] = $p->ID;
				$args['s'] = '';
				return $args;
			}
		}
		// Comma separated list with Object IDs
		if (strpos( $args['s'], ',') !== false) {
			$exploded = explode(',', $args['s']);
			// Check all values are numeric
			$check = true;
			foreach ( $exploded as $item ) {
				if ( ! is_numeric( $item ) ) {
					$check = false;
				}
			}
			if ( $check === false ) {
				return $args;
			}
			$args['post__in'] = $exploded;
			$args['s'] = '';
			return $args;
		}
	}
	// return
	return $args;
}
add_filter('acf/fields/relationship/query/key=field_5dbc87636419f', 'organisation_query', 10, 3);
add_filter('acf/fields/relationship/query/key=field_5db183394c9c0', 'organisation_query', 10, 3);

function material_query( $args, $field, $post_id ) {
	if ( isset( $args['s'] ) ) {
		$id = url_to_postid( $args['s'] );
		if ( $id != 0 ) {
			$args['page_id'] = $id;
			$args['s'] = '';
		}
		// Object ID
		if ( is_numeric(  $args['s'] ) ) {
			$p = get_post( (int) $args['s'] );
			if (is_object( $p ) ) {
				$args['page_id'] = $p->ID;
				$args['s'] = '';
				return $args;
			}
		}
		// Comma separated list with Object IDs
		if (strpos( $args['s'], ',') !== false) {
			$exploded = explode(',', $args['s']);
			// Check all values are numeric
			$check = true;
			foreach ( $exploded as $item ) {
				if ( ! is_numeric( $item ) ) {
					$check = false;
				}
			}
			if ( $check === false ) {
				return $args;
			}
			$args['post__in'] = $exploded;
			$args['s'] = '';
			return $args;
		}
		// check remote url
		if ( preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $args['s'] ) ) {
			$args['meta_key'] = 'material_url';
			$args['meta_value'] = $args['s'];
			$args['meta_compare'] = '=';
			$args['s'] = '';
			return $args;
		}

	}
	// return
	return $args;
}
add_filter('acf/fields/relationship/query/key=field_5db183a04c9c1', 'material_query', 10, 3);
add_filter('acf/fields/relationship/query/key=field_5dcd87474f69c', 'material_query', 10, 3);

