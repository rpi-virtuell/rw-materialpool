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
        global $wpdb;

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
		// @todo pods abfragen und aktivierte componenten wenn möglich
        // @todo searchwp abfragen
        // @todo facetwp abfragen


        $table_name = $wpdb->prefix . "mp_stats";
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `object` int(11) NOT NULL,
          `day` char(2) NOT NULL,
          `hour` tinyint(2) NOT NULL,
          `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `month` tinyint(2) NOT NULL,
          `year` SMALLINT (6) NOT NULL,
          `posttype` char(20) NOT NULL,
          `dayofweek` tinyint(2) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `object` (`object`),
          KEY `day` (`day`),
          KEY `hour` (`hour`),
          KEY `ts` (`ts`),
          KEY `month` (`month`),
          KEY `posttype` (`posttype`),
          KEY `year` (`year`),
          KEY `dayofweek` (`dayofweek`)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $table_name = $wpdb->prefix . "mp_stats_autor";
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `object` int(11) NOT NULL,
          `day` char(2) NOT NULL,
          `hour` tinyint(2) NOT NULL,
          `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `month` tinyint(2) NOT NULL,
          `year` SMALLINT (6) NOT NULL,
          `dayofweek` tinyint(2) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `object` (`object`),
          KEY `day` (`day`),
          KEY `hour` (`hour`),
          KEY `ts` (`ts`),
          KEY `month` (`month`),
          KEY `year` (`year`),
          KEY `dayofweek` (`dayofweek`)
        ) $charset_collate;";

        dbDelta( $sql );

        $table_name = $wpdb->prefix . "mp_stats_organisation";
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `object` int(11) NOT NULL,
          `day` char(2) NOT NULL,
          `hour` tinyint(2) NOT NULL,
          `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `month` tinyint(2) NOT NULL,
          `year` SMALLINT (6) NOT NULL,
          `dayofweek` tinyint(2) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `object` (`object`),
          KEY `day` (`day`),
          KEY `hour` (`hour`),
          KEY `ts` (`ts`),
          KEY `month` (`month`),
          KEY `year` (`year`),
          KEY `dayofweek` (`dayofweek`)
        ) $charset_collate;";

        dbDelta( $sql );

        /*
         * Register CronJobs
         */

        if (! wp_next_scheduled ( 'mp_depublizierung' ) ) {
            wp_schedule_event(time(), 'hourly', 'mp_depublizierung');
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
        /*
         * Deregister CronJobs
         */
        wp_clear_scheduled_hook('mp_depublizierung');

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
