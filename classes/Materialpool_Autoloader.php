<?php

/**
 * Class Materialpool_Autoloader
 *
 * Autoloader for the plugin
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 */

class Materialpool_Autoloader {
	/**
	 * Registers autoloader function to spl_autoload
	 *
	 * @since   0.0.1
	 * @access  public
	 * @static
	 * @action  materialpool_autoload_register
	 * @return  void
	 */
	public static function register() {
		spl_autoload_register( 'Materialpool_Autoloader::load' );
		do_action( 'materialpool_autoload_register' );
	}

	/**
	 * Unregisters autoloader function with spl_autoload
	 *
	 * @ince    0.0.1
	 * @access  public
	 * @static
	 * @action  materialpool_autoload_unregister
	 * @return  void
	 */
	public static function unregister() {
		spl_autoload_unregister( 'Emergency_Report_Autoloader::load' );
		do_action( 'materialpool_autoload_unregister' );
	}

	/**
	 * Autoloading function
	 *
	 * @since   0.0.1
	 * @param   string  $classname
	 * @access  public
	 * @static
	 * @return  void
	 */
	public static function load( $classname ) {
		// only PHP 5.3, use now __DIR__ as equivalent to dirname(__FILE__).
		$file =  __DIR__ . DIRECTORY_SEPARATOR . ucfirst( $classname ) . '.php';
		if( file_exists( $file ) ) {
			require_once $file;
		}
	}
}
