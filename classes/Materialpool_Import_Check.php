<?php
/**
 * Some Checks and Helperfunktions after importing stuff from old materialpool
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 */

class Materialpool_Import_Check
{
    public static function check() {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare( "SELECT  distinct($wpdb->posts.ID) FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s   AND $wpdb->postmeta.meta_value = 1  AND $wpdb->posts.post_type = 'post' " , 'materialpool_import' ) );
        if ( is_array( $result ) ) {
            foreach ( $result as $obj ) {
                if ( self::url_exists( $obj->ID ) ) {
                    wp_delete_post( $obj->ID );
                } else {
                    self::special_handling( $obj->ID );
                }

                self::cleanup( $obj->ID );
            }
        }
    }

    /**
     * @param $post_id
     */
    public static function url_exists($post_id ) {
        global $wpdb;
        $url = get_post_meta( $post_id, 'material_url', true  );
        
        $anzahl = $wpdb->get_col( $wpdb->prepare( "SELECT count( meta_id ) as anzahl  FROM  $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = %s and $wpdb->postmeta.meta_value = %s and post_id != %d", 'material_url', $url, $post_id) );
        if ( is_array( $anzahl ) && $anzahl[ 0 ] == 0 ) {
            $back = false;
        } else {
            $back = true;
        }
        return $back;
    }

    /**
     * @param $post_id
     */
    public static function special_handling($post_id ) {
        global $wpdb;
        $back = false;
        wp_cache_flush();
        $medientype = get_post_meta( $post_id, 'material_medientyp', true  );
        if ( $medientype == 73 ) {
            $pod = pods( 'material' );
            $postdata = get_post( $post_id );
            $sprache = get_post_meta( $post_id, '_pods_material_sprache', false );
            $bildungsstufe = get_post_meta( $post_id, '_pods_material_bildungsstufe', false );
            $altersstufe = get_post_meta( $post_id, '_pods_material_altersstufe', false );
            $title = get_post_meta( $post_id, 'material_titel', true );
            $beschreibung = $postdata->post_content;
            $data = array(
                'material_special' => 1,
                'material_titel' => $title,
                'material_kurzbeschreibung' => get_post_meta( $post_id, 'material_kurzbeschreibung', true ),
                'material_beschreibung' => $beschreibung,
                'material_autor_interim' => get_post_meta( $post_id, 'material_autor_interim', true ),
                'material_organisation_interim' => get_post_meta( $post_id, 'material_organisation_interim', true ),
                'material_schlagworte_interim' => get_post_meta( $post_id, 'material_schlagworte_interim', true ),
            );

            wp_delete_post($post_id );
            $material_id = $pod->add( $data );
            $pod = pods( 'material' , $material_id );
            $pod->add_to( 'material_sprache', implode( ',', $sprache[ 0 ] ) );
            $pod->add_to( 'material_medientyp', $medientype );
            $pod->add_to( 'material_bildungsstufe', implode( ',', $bildungsstufe[ 0 ] ) );
            $pod->add_to( 'material_altersstufe', implode( ',', $altersstufe[ 0 ] ) );


            $post_type = get_post_type($material_id);
            $post_parent = wp_get_post_parent_id( $material_id );
            $post_name = wp_unique_post_slug( sanitize_title( $title ), $material_id, 'publish', $post_type, $post_parent );

            wp_publish_post( $material_id);

            $x = $wpdb->update(
                $wpdb->posts,
                array(
                    'post_title' => stripslashes( $title ),
                    'post_name' => $post_name,
                    'post_content' => $beschreibung,
                ),
                array( 'ID' => $material_id ),
                array(
                    '%s',
                    '%s'
                ),
                array( '%d' )
            );

            // Altersstufen des Materials in term_rel speichern
            wp_delete_object_term_relationships( $material_id, 'altersstufe' );
            $cats = $altersstufe[ 0 ];
            if ( is_array( $cats ) ) {
                foreach ( $cats as $key => $val ) {
                    $cat_ids[] = (int) $val;
                }
            }
            if ( is_int( $cats ) ) {
                $cat_ids[] = $cats;
            }
             wp_set_object_terms( $material_id, $cat_ids, 'altersstufe', true );

            // Bildungsstufen des Materials in term_rel speichern
            wp_delete_object_term_relationships( $material_id, 'bildungsstufe' );
            $cats =  $bildungsstufe[ 0 ];
            if ( is_array( $cats ) ) {
                foreach ( $cats as $key => $val ) {
                    $cat_ids[] = (int) $val;
                }
            }
            if ( is_int( $cats ) ) {
                $cat_ids[] = $cats;
            }
            wp_set_object_terms( $material_id, $cat_ids, 'bildungsstufe', true );

            // Medientyp des Materials in term_rel speichern
            wp_delete_object_term_relationships( $material_id, 'medientyp' );
            $cats = $medientype;
            if ( is_array( $cats ) ) {
                foreach ( $cats as $key => $val ) {
                    $cat_ids[] = (int) $val;
                }
            }
            if ( $cats!== null  ) {
                $cat_ids[] = (int) $cats;
            }
            wp_set_object_terms( $material_id, $cat_ids, 'medientyp', true );


            // Sprachen des Materials in term_rel speichern
            wp_delete_object_term_relationships( $material_id, 'sprache' );
            $cats = $sprache;
            if ( is_array( $cats ) ) {
                foreach ( $cats as $key => $val ) {
                    $cat_ids[] = (int) $val;
                }
            }
            if ( $cats!== null  ) {
                $cat_ids[] = (int) $cats;
            }
            wp_set_object_terms( $material_id, $cat_ids, 'sprache', true );

            // Wenn Special, dann MaterialURL auf das Material selbst zeigen lassen.
            clean_post_cache( $material_id );
            $p = get_post( $material_id );
            $url = get_permalink( $p );
            update_post_meta( $material_id, 'material_url', $url  );

        }
        return $back;
    }

    /**
     * @param $post_id
     */
    public static function cleanup($post_id ) {
        delete_post_meta( $post_id, 'materialpool_import' );
    }
}