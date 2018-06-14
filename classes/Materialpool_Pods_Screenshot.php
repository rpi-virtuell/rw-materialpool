<?php

class PodsField_Screenshot extends PodsField {

	/**
	 * Field Type Group
	 *
	 * @var string
	 * @since 2.0
	 */
	public static $group = 'Text';

	/**
	 * Field Type Identifier
	 *
	 * @var string
	 * @since 2.0
	 */
	public static $type = 'screenshot';

	/**
	 * Field Type Label
	 *
	 * @var string
	 * @since 2.0
	 */
	public static $label = 'Screenshot';

	/**
	 * Field Type Preparation
	 *
	 * @var string
	 * @since 2.0
	 */
	public static $prepare = '%s';

	/**
	 * Do things like register/enqueue scripts and stylesheets
	 *
	 * @since 2.0
	 */
	public function __construct () {
		parent::__construct();
	}

	/**
	 * Add options and set defaults to
	 *
	 *
	 * @return array
	 * @since 2.0
	 */
	public function options () {
		$options = array();

		return $options;
	}

	/**
	 * Define the current field's schema for DB table storage
	 *
	 * @param array $options
	 *
	 * @return array
	 * @since 2.0
	 */
	public function schema ( $options = null ) {
 		$schema = 'LONGTEXT';
		return $schema;
	}

	/**
	 * Change the way the value of the field is displayed with Pods::get
	 *
	 * @param mixed $value
	 * @param string $name
	 * @param array $options
	 * @param array $pod
	 * @param int $id
	 *
	 * @return mixed|null|string
	 * @since 2.0
	 */
	public function display ( $value = null, $name = null, $options = null, $pod = null, $id = null ) {


		return $value;
	}

	/**
	 * Customize output of the form field
	 *
	 * @param string $name
	 * @param mixed $value
	 * @param array $options
	 * @param array $pod
	 * @param int $id
	 *
	 * @since 2.0
	 */
	public function input ( $name, $value = null, $options = null, $pod = null, $id = null ) {

        $screenshot = trim( get_metadata( 'post', $id, 'material_screenshot', true ) );

        ?>
        <div id="material-screenshot">
        <?php if( $screenshot != '' ) { ?>
            <img style="max-width: 400px;" src="<?php echo $screenshot; ?>">
        <?php } ?>
        </div>
        <div id="generate-screenshot-container">
       <!-- <a class="button"  id="generate-screenshot">Screenshot erzeugen</a> <a class="button"  id="delete-screenshot">Screenshot löschen</a> -->
        </div>
        <input id="pods-form-ui-pods-meta-material-screenshot"  name="pods_meta_material_screenshot"  value="<?php if( $screenshot != '' ) { echo $screenshot; } ?>"  type="hidden">
        <?php
	}

	/**
	 * Validate a value before it's saved
	 *
	 * @param mixed $value
	 * @param string $name
	 * @param array $options
	 * @param array $fields
	 * @param array $pod
	 * @param int $id
	 *
	 * @param null $params
	 * @return array|bool
	 * @since 2.0
	 */
	public function validate ( $value, $name = null, $options = null, $fields = null, $pod = null, $id = null, $params = null ) {
		return true;
	}

	/**
	 * Change the value or perform actions after validation but before saving to the DB
	 *
	 * @param mixed $value
	 * @param int $id
	 * @param string $name
	 * @param array $options
	 * @param array $fields
	 * @param array $pod
	 * @param object $params
	 *
	 * @return mixed|string
	 * @since 2.0
	 */
	public function pre_save ( $value, $id = null, $name = null, $options = null, $fields = null, $pod = null, $params = null ) {

		return $value;
	}

	/**
	 * Customize the Pods UI manage table column output
	 *
	 * @param int $id
	 * @param mixed $value
	 * @param string $name
	 * @param array $options
	 * @param array $fields
	 * @param array $pod
	 *
	 * @return mixed|string
	 * @since 2.0
	 */
	public function ui ( $id, $value, $name = null, $options = null, $fields = null, $pod = null ) {

		return $value;
	}

	/**
	 * Strip HTML based on options
	 *
	 * @param string $value
	 * @param array $options
	 *
	 * @return string
	 */
	public function strip_html ( $value, $options = null ) {
		if ( is_array( $value ) )
			$value = @implode( ' ', $value );

		$value = trim( $value );

		if ( empty( $value ) )
			return $value;

		$options = (array) $options;

		if ( 1 == pods_var( self::$type . '_allow_html', $options, 0, null, true ) ) {
			$allowed_html_tags = '';

			if ( 0 < strlen( pods_var( self::$type . '_allowed_html_tags', $options ) ) ) {
				$allowed_html_tags = explode( ' ', trim( pods_var( self::$type . '_allowed_html_tags', $options ) ) );
				$allowed_html_tags = '<' . implode( '><', $allowed_html_tags ) . '>';
			}

			if ( !empty( $allowed_html_tags ) && '<>' != $allowed_html_tags )
				$value = strip_tags( $value, $allowed_html_tags );
		}
		else
			$value = strip_tags( $value );

		return $value;
	}


}