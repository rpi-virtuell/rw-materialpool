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
        add_filter( 'posts_distinct', array( 'Materialpool_Material', 'material_list_post_distinct' ) );
        add_action( 'add_meta_boxes',  array( 'Materialpool_Material', 'add_metaboxes' ) );

        add_filter( 'tl_tplc_external_files', array( 'Materialpool_Material', 'add_template_check_external_files' ) );
        add_action( 'init', array( 'Materialpool', 'get_crossdomain_viewer_url' ) );

        remove_shortcode( 'viewerjs', 'viewerjs_shortcode_handler');
        add_shortcode( 'viewerjs', array( 'Materialpool', 'viewerjs_shortcode_handler' ) );
        /*
         * Register as Class method throws an error
         */
        add_action( 'pods_meta_groups',  'materialpool_pods_material_metaboxes', 10, 2 );

        // Add Filter & Actions for Organisation
        add_filter( 'template_include', array( 'Materialpool_Organisation', 'load_template' ) );
        add_action( 'manage_organisation_posts_columns', array( 'Materialpool_Organisation', 'cpt_list_head') );
        add_action( 'manage_organisation_posts_custom_column', array( 'Materialpool_Organisation', 'cpt_list_column'), 10,2 );
        add_action( 'manage_edit-organisation_sortable_columns', array( 'Materialpool_Organisation', 'cpt_sort_column') );
		add_action( 'save_post', array( 'Materialpool_Organisation', 'generate_title') );
        add_filter( 'tl_tplc_external_files', array( 'Materialpool_Organisation', 'add_template_check_external_files' ) );

        // Add Filter & Actions for Autor
        add_filter( 'template_include', array( 'Materialpool_Autor', 'load_template' ) );
        add_action( 'manage_autor_posts_columns', array( 'Materialpool_Autor', 'cpt_list_head') );
        add_action( 'manage_autor_posts_custom_column', array( 'Materialpool_Autor', 'cpt_list_column'), 10,2 );
        add_action( 'manage_edit-autor_sortable_columns', array( 'Materialpool_Autor', 'cpt_sort_column') );
        add_action( 'save_post', array( 'Materialpool_Autor', 'generate_title') );
        add_filter( 'tl_tplc_external_files', array( 'Materialpool_Autor', 'add_template_check_external_files' ) );

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


        // Add Filter & Actions for 3Party Stuff
        add_action( 'rate_post',                            array( 'Materialpool_FacetWP', 'reindex_post_after_ajax_rating'),10, 2 );
        add_action( 'pods_api_post_save_pod_item_material', array( 'Materialpool_FacetWP', 'reindex_post_after_pods_saveing'),10, 3 );
        add_action( 'pods_api_post_save_pod_item_organisation', array( 'Materialpool_FacetWP', 'reindex_post_after_pods_saveing'),10, 3 );
        remove_filter('manage_posts_columns', 'add_postratings_column');
        remove_filter('manage_pages_columns', 'add_postratings_column');
        add_filter( 'manage_material_posts_columns', array( 'Materialpool_Ratings', 'page_column'), 9999 );


        pods_register_field_type( 'screenshot', self::$plugin_base_dir . 'classes/Materialpool_Pods_Screenshot.php' );

        add_action( 'wp_ajax_mp_get_html',  array( 'Materialpool', 'my_action_callback_mp_get_html' ) );
        add_action( 'wp_ajax_mp_get_description',  array( 'Materialpool', 'my_action_callback_mp_get_description' ) );
        add_action( 'wp_ajax_mp_check_url',  array( 'Materialpool', 'my_action_callback_mp_check_url' ) );
        add_action( 'wp_head', array( 'Materialpool',  'promote_feeds' ) );
        remove_all_actions( 'do_feed_rss2' );
        add_action( 'do_feed_rss2', array( 'Materialpool', 'material_feed_rss2') , 10, 1 );

        add_action( 'init', array( 'Materialpool', 'custom_oembed_providers' ) , 10, 1 );

        do_action( 'materialpool_init' );
	}



    function custom_oembed_providers() {
        wp_oembed_add_provider( 'http://learningapps.org/*', 'http://learningapps.org/oembed.php' );
    }


    function material_feed_rss2( $for_comments ) {
        if( get_query_var( 'post_type' ) == 'material' ) {
            $rss_template = Materialpool::$plugin_base_dir . 'templates/feed-material-rss2.php';
            if (file_exists($rss_template)) {
                load_template($rss_template);
            } else {
                do_feed_rss2($for_comments);
            }
        }
        if( get_query_var( 'post_type' ) == 'organisation' ) {
            $rss_template = Materialpool::$plugin_base_dir . 'templates/feed-organisation-rss2.php';
            if (file_exists($rss_template)) {
                load_template($rss_template);
            } else {
                do_feed_rss2($for_comments);
            }
        }
        if( get_query_var( 'post_type' ) == 'autor' ) {
            $rss_template = Materialpool::$plugin_base_dir . 'templates/feed-autor-rss2.php';
            if (file_exists($rss_template)) {
                load_template($rss_template);
            } else {
                do_feed_rss2($for_comments);
            }
        }

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
            if ( $property == 'og:image' ) {
                echo $content;
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
        $url =  esc_url_raw( $_POST['site'] );
        $title = '';
        $description = '';
        $keywords = '';
        $image = '';

        $args = array(
            'user-agent' => 'Mozilla/5.0 (compatible; Materialpool; +'.home_url().')',
            'timeout'     => 30,
        );
        $response =  wp_remote_get( $url, $args );
        if (  ! is_wp_error( $response ) ) {
             $body = utf8_decode( $response['body'] );
            libxml_use_internal_errors(true);
            $doc = new DomDocument();
            $doc->loadHTML($body);
            $xpath = new DOMXPath($doc);
            $query = '//*/meta[starts-with(@property, \'og:\')]';
            $metas = $xpath->query($query);
            foreach ($metas as $meta) {
                $property = $meta->getAttribute('property');
                $content = $meta->getAttribute('content');
                if ( $property == 'og:title' ) {
                    $title = $content;
                }
                if ( $property == 'og:description' ) {
                    $description = $content;
                }
                if ( $property == 'og:video:tag' || $property == 'video:tag'  ) {
                    if ( $keywords != '' ) {
                        $keywords .= ', ';
                    }
                    $keywords .= $content;
                }
                if ( $property == 'og:image' ) {
                    $image = $content;
                }
            }
            $query = '//*/meta';
            $metas = $xpath->query($query);
            foreach ($metas as $meta) {
                $name = $meta->getAttribute('name');
                $content = $meta->getAttribute('content');
                if ( $name == 'description' && $description == '' ) {
                    $description = $content;
                }
                if ( $name == 'title' && $title == '' ) {
                    $title = $content;
                }
                if ( $name == 'keywords' && $keywords == '' ) {
                    $keywords = $content;
                }
            }
            $titleNode = $xpath->query('//title');
            if ( $title == '' ) {
                $title = $titleNode->item(0)->textContent;
            }
            $data = array(
                'title' => $title,
                'description' => $description,
                'keywords' => $keywords,
                'image' => $image,
            );
        }
        echo json_encode( apply_filters( 'materialpool-ajax-get-description', $data, $xpath ) );
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

        $anzahl = $wpdb->get_col( $wpdb->prepare( "SELECT count( meta_id ) as anzahl  FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_url', $url) );
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


    function promote_feeds() {
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
     */
    function get_crossdomain_viewer_url(){
        global $wp_version;

        if(isset($_GET['vsviewer_url'])){
            $url = $_GET['vsviewer_url'];


            //@todo check url in materialpool

            $file_name=substr (strrchr ($url, "/"), 1);

            $args = array(
                'user-agent'  => 'rpi-virtuell/' . $wp_version . '; ' . home_url(),
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

    function viewerjs_shortcode_handler($args) {
        global $viewerjs_plugin_url;
        $document_url = home_url().'/?vsviewer_url='.$args[0];
        $options = get_option('ViewerJS_PluginSettings');
        $iframe_width = $options['width'];
        $iframe_height = $options['height'];
        return "<iframe src=\"$viewerjs_plugin_url" .
            '#' . $document_url .'" '.
            "width=\"$iframe_width\" ".
            "height=\"$iframe_height\" ".
            'style="border: 1px solid black; border-radius: 5px;" '.
            'webkitallowfullscreen="true" '.
            'mozallowfullscreen="true"></iframe>
			<p class="viewerjsurlmeta">Quelle: <span class="viewerjsurl">'.$args[0].'</span></p>';
    }


}


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
 *
 * Register as Class method throws an error
 *
 * @since   0.0.1
 * @param $type
 * @param $name
 */
function materialpool_pods_material_metaboxes ( $type, $name ) {
    pods_group_add( 'material', __( 'Base', Materialpool::get_textdomain() ), 'material_url,material_special, material_titel,material_kurzbeschreibung,material_beschreibung' );
    pods_group_add( 'material', __( 'Owner', Materialpool::get_textdomain() ), 'material_autoren,material_autor_interim,material_organisation,material_organisation_interim' );
    pods_group_add( 'material', __( 'Meta', Materialpool::get_textdomain() ), 'material_schlagworte,material_schlagworte_interim,material_bildungsstufe,material_medientyp,material_sprache' );
    pods_group_add( 'material', __( 'Advanced Meta', Materialpool::get_textdomain() ), 'material_inklusion,material_verfuegbarkeit,material_zugaenglichkeit,material_lizenz,material_altersstufe' );
    pods_group_add( 'material', __( 'Date', Materialpool::get_textdomain() ), 'material_jahr, material_veroeffentlichungsdatum,material_erstellungsdatum,material_depublizierungsdatum,material_wiedervorlagedatum' );
    pods_group_add( 'material', __( 'Relationships', Materialpool::get_textdomain() ), 'material_werk,material_band,material_verweise' );
    pods_group_add( 'material', __( 'Image', Materialpool::get_textdomain() ), 'material_cover,material_cover_url,material_cover_quelle,material_screenshot' );
}


function mb_endsWith($check, $endStr) {
    if (!is_string($check) || !is_string($endStr) || mb_strlen($check)<mb_strlen($endStr)) {
        return false;
    }

    return (mb_substr($check, mb_strlen($check)-mb_strlen($endStr), mb_strlen($endStr)) === $endStr);
}
