<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */

class Materialpool_CMB2_CPT_Select
{
    /**
     * Render field
     *
     * @since   0.0.1
     *
     */
    static public function render_cpt_select( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
        $menu = array();
        $mypages = Materialpool_CMB2_CPT_Select::get_cpt( $menu, 0, 0, $field->args[ 'cpt']);

        $options = $field_type_object->select_option( array(
            'label'=> __ ('Please select'),
            'value' => '',
            'checked' => false
        ) );

        $savedpage = get_metadata( 'post', $field_object_id,  $field->args[ 'id'], true );

        foreach ( $mypages as $page ) {
            if ( $page[ 'ID' ] == $savedpage ) {
                $options .=
                    $field_type_object->select_option(array(
                        'label' => $page[ 'title' ],
                        'value' => $page[ 'ID' ],
                        'checked' => true
                    ));
            } else {
                $options .=
                    $field_type_object->select_option(array(
                        'label' => $page[ 'title' ],
                        'value' => $page[ 'ID' ],
                        'checked' => false
                    ));
            }
        }

        echo $field_type_object->select( array(
                'class'   => 'cmb2_select cmb2-select-page',
                'name'    => $field->args[ 'id'],
                'id'      => $field->args[ 'id'],
                'options' => $options
            )
        );
        return;
    }

    /**
     * Register the plugin dashboard page
     *
     * @since   0.0.1
     *
     */
    static public function get_cpt( &$menu = array(), $parentID = 0, $level = 0, $cpt = 'post' ) {
        $pages = get_posts( array(  'post_status' => 'publish', 'post_type' => $cpt, 'post_parent' => $parentID, 'order_by' => 'post_title', 'order' => 'asc' ) );
        foreach ($pages as $page ) {
            $menu[] = array( 'ID' => $page->ID, 'title' => str_repeat( '-', $level ) . $page->post_title );
            Materialpool_CMB2_CPT_Select::get_cpt( $menu, $page->ID, $level +1, $cpt );
        }
        return $menu;
    }

    /**
     * Sanitize cpt select
     *
     * @since   0.0.1
     *
     */
    static public function sanitize_cpt_select( $override_value, $value, $object_id, $field_args ) {
        if ( ! empty( $value ) ) {
            update_post_meta( $object_id, $field_args['id'], $value );
        }
        return $value;
    }
}