<?php
/**
 * The plugin dashboard class.
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 */


class Materialpool_Dashboard {

	/**
	 * Register the plugin dashboard page
	 *
	 * @since   0.0.1
	 * @filter    materialpool_dashboard_page_args
	 *
	 */
	static public function register_dashboard_page() {
		$args = array(
			'page_title' => _x( 'Materialpool', 'Page title', Materialpool::get_textdomain() ),
			'menu_title' => _x( 'Materialpool', 'Menu title', Materialpool::get_textdomain() ),
			'capability' => 'edit_posts',
			'menu_slug' => 'materialpool',
			'callback' => array(
				'Materialpool_Dashboard',
				'dashboard_page_content'
			),
			'icon' => 'dashicons-images-alt2',
			'menu_position' => 25
		);
		$args = apply_filters( 'materialpool_dashboard_page_args', $args );
		add_menu_page(
			$args[ 'page_title' ],
			$args[ 'menu_title' ],
			$args[ 'capability' ],
			$args[ 'menu_slug' ],
			$args[ 'callback' ],
			$args[ 'icon' ],
			$args[ 'menu_position' ]
		);
	}

	/**
	 * Plugin Dashboard Content
	 *
	 * @since   0.0.1
	 */
	static public function dashboard_page_content() {
		echo "Inhalt";
	}

	/**
	 * Register Plugin settingspage
	 *
	 * Register the plugin settingspage
	 *
	 * @since   0.0.1
	 * @hook    materialpool_dashboard_submenu_args
	 *
	 */
	static public function register_settings_page() {
		$args = array(
			'slug'  => 'materialpool',
			'page_title' => _x( 'Settings', 'Page title', Materialpool::$textdomain ),
			'menu_title' => _x( 'Settings', 'Menu title', Materialpool::$textdomain ),
			'capability' => 'manage_options',
			'menu_slug' => 'materialpool-settings',
			'callback' => array(
				'Materialpool_Dashboard',   // Callback class
				'settings_page_content'         // Callback method
			)
		);
		$args = apply_filters( 'materialpool_dashboard_submenu_args', $args );
		add_submenu_page(
			$args[ 'slug' ],      // slug of parentpage
			$args[ 'page_title' ], //page title
			$args[ 'menu_title' ], // menu title
			$args[ 'capability' ],                               // capability
			$args[ 'menu_slug' ],                       // menu slug
			$args[ 'callback' ]
		);
	}

	/**
	 * Register the Pluginsetings
	 *
	 * @since   0.0.1
	 */
	static public function register_settings() {

	}

	/**
	 * Output the plugin settingspage content
	 *
	 * @since   0.0.1
	 *
	 */
	static public function settings_page_content() {

		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
		echo '<h2>My Custom Settings Page</h2>';
		echo '</div>';

	}
}