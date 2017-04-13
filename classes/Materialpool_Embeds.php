<?php

/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */

class Materialpool_Embeds
{

    /**
     *
     * @since 0.0.1
     * @access	public
     * @param $html
     * @return string
     */
    static public function site_title_html( $html ) {
        return "<img src='". Materialpool::$plugin_url ."/assets/rpi-logo-100-100.jpg'>";
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @param $output
     * @return string
     */
    static public function the_excerpt_embed( $output ) {
        global $post;

        if ( $post->post_type == 'material' ) {
            $output= " <div><p style='valign: top;'><img style='width:20%; padding-right: 10px; padding-bottom: 10px;  align: left; float: left;' src='". Materialpool_Material::get_picture_url() ."'>". Materialpool_Material::get_shortdescription() ."</p></div><div style='clear: both;'></div>";
        }
        if ( $post->post_type == 'autor' ) {
            $output= " <div><p style='valign: top;'><img style='width:20%; padding-right: 10px; padding-bottom: 10px;  align: left; float: left;' src='". Materialpool_Autor::get_picture() ."'><strong>Das neuste Material</strong><br><br>". Materialpool_Autor::get_materialien_html( 5 ) ."</p></div><div style='clear: both;'></div>";
        }

        return $output;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     */
    static public function embed_content() {
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