<?php

/**
 * Class Materialpool_Installation
 *
 * Contains some helper code for plugin installation
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 */

class Materialpool_Installation {
	/**
	 * Check some things on plugin activation
	 *
	 * @since   0.0.1
	 * @access  public
	 * @static
	 * @return  void
	 */
	public static function on_activate() {

		// check WordPress version
		if ( ! version_compare( $GLOBALS[ 'wp_version' ], '4.0', '>=' ) ) {
			deactivate_plugins( Materialpool::$plugin_filename );
			die(
			wp_sprintf(
				'<strong>%s:</strong> ' .
				__( 'This plugin requires WordPress 4.0 or newer to work', Materialpool::get_textdomain() )
				, Materialpool::get_plugin_data( 'Name' )
			)
			);
		}


		// check php version
		if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
			deactivate_plugins( Materialpool::$plugin_filename );
			die(
			wp_sprintf(
				'<strong>%1s:</strong> ' .
				__( 'This plugin requires PHP 5.3 or newer to work. Your current PHP version is %1s, please update.', Materialpool::get_textdomain() )
				, Materialpool::get_plugin_data( 'Name' ), PHP_VERSION
			)
			);
		}
	}

	/**
	 * Clean up after deactivation
	 *
	 * Clean up after deactivation the plugin
	 *
	 * @since   0.0.1
	 * @access  public
	 * @static
	 * @return  void
	 */
	public static function on_deactivation() {

	}

	/**
	 * Clean up after uninstall
	 *
	 * Clean up after uninstall the plugin.
	 * Delete options and other stuff.
	 *
	 * @since   0.0.1
	 * @access  public
	 * @static
	 * @return  void
	 *
	 */
	public static function on_uninstall() {

	}
}
