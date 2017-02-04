<?php

class PodsField_Facette extends PodsField {

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
    public static $type = 'facette';

    /**
     * Field Type Label
     *
     * @var string
     * @since 2.0
     */
    public static $label = 'Facette';

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
        global $post;

        if (  $pod->data->row[ 'pandarf_parent_post_id']  === null ) {
        ?>
         Themengruppe muss gespeichert werden, bevor Material zugeordnet werden kann.
            <?php } else { ?>

        <a href="<?php echo site_url(); ?>/facettierte-suche?thema=<?php echo $pod->data->row[ 'pandarf_parent_post_id'];?>&gruppe=<?php echo $pod->data->id; ?>" target="_blank">Material zuordnen</a>
        <h2>Material in dieser Gruppe</h2> <p><a  class="themenseite-cb-backend-update" data-gruppe="<?php echo $pod->data->id; ?>">Materialliste aktualisieren</a></p>
            <div id="material-<?php echo $pod->data->id; ?>">
            <?php
            foreach ( Materialpool_Themenseite::get_gruppen_by_groupid( $pod->data->id ) as $gruppe ) {
                 foreach ( explode(',', $gruppe[ 'auswahl'] ) as $materialid ) {
                    $post = get_post( $materialid);
                    if ( is_object( $post) ) {
                        echo "<input type='checkbox' checked='checked' class='uncheck_themenseite  themenseite-cb-backend' ";
                        echo " data-gruppe='". $pod->data->id  ."' data-post='". $post->ID ."'";
                        echo  ">";
                        echo "<a href='". get_permalink( $post ) . "' target='_new'>" . $post->post_title."</a><br>";
                    }
                }
            }

                ?>
            </div>
        <?php
        }
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
        $var = Materialpool_Themenseite::get_gruppen_by_groupid( $id );
        return $var[ 0 ][ 'auswahl' ];
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