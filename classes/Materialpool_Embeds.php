<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 12.04.17
 * Time: 16:19
 */
class Materialpool_Embeds
{

    /**
     * @param $html
     * @return string
     */
    function site_title_html( $html ) {
        return "<img src='". Materialpool::$plugin_url ."/assets/rpi-logo-100-100.jpg'>";
    }


    function the_excerpt_embed( $output ) {
        global $post;

        if ( $post->post_type == 'material' ) {

            $output= " <div><p style='valign: top;'><img style='width:20%; padding-right: 10px; padding-bottom: 10px;  align: left; float: left;' src='". Materialpool_Material::get_picture_url() ."'>". Materialpool_Material::get_shortdescription() ."</p></div><div style='clear: both;'></div>";
        }

        return $output;
    }

    function embed_content() {
        global $post;

        if ( $post->post_type == 'material' ) {
            $medientypen = Materialpool_Material::get_mediatyps_root();
            if (is_array( $medientypen ) ) {
                echo "<strong>Medientype(n):</strong> ";
                $counter = 0;
                foreach ($medientypen as $medientyp) {
                    if ( $counter > 0 ) {
                        echo ", ";
                    }
                    echo $medientyp[ 'name' ];
                }
                echo "&nbsp;&nbsp;&nbsp;&nbsp;";
            }

            $bildungsstufen = Materialpool_Material::get_bildungsstufen();
            echo "<strong>Bildungsstufe(n):</strong> ";
            echo $bildungsstufen;
        }
    }

}