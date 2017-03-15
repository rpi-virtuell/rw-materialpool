<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 15.03.17
 * Time: 16:45
 */
class Materialpool_Posts
{
    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_metaboxes() {
        add_meta_box('material_convert', __( 'Konvert', Materialpool::$textdomain ), array( 'Materialpool_Posts', 'convert_metabox' ), 'post', 'side', 'default');
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function convert_metabox() {
        global $post;
        echo "<button id='convert2material' data-id='". $post->ID ."' type='button'>zu Material konvertieren</button>";
    }

}