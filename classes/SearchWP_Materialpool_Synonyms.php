<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


class SearchWP_Materialpool_Synonyms {

    public static 	function register( $extensions ) {
        $extensions['_Materialpool_Synonyms'] = __FILE__;

        return $extensions;
    }



    public static function find_synonyms( $processed_term, $engine, $term ) {
        global $wpdb;
        global $SearchWP_Materialpool_Synonyms_Flag;

        if ( $SearchWP_Materialpool_Synonyms_Flag == true ) {
            return $processed_term;
        }
        $SearchWP_Materialpool_Synonyms_Flag = true;
        // Fall 1
        // Suchwort ist als Normwort gespeichert.
        // Alle Synonyme holen

        $meta_key		= 'normwort';
        $meta_key_value	= $term;

        $postids=$wpdb->get_col( $wpdb->prepare(
            "
            SELECT      k.post_id
            FROM        $wpdb->postmeta k
            WHERE       k.meta_key = %s 
                        AND k.meta_value = %s
            ",
            $meta_key,
            $meta_key_value
        ) );

        foreach ( $postids as $id ) {
            $post = get_post( $id );
            $processed_term[] = strtolower( $post->post_title );
        }

        // Fall 2
        // Suchwort ist ein Synonym.
        // Normwort holen und alle weiteren Synonyme

        $postids=$wpdb->get_col( $wpdb->prepare(
            "
            SELECT      p.ID
            FROM        $wpdb->posts p
            WHERE       p.post_title = %s 
            ",
            $term
        ) );

        foreach ( $postids as $id ) {
            $normwort = get_post_meta( $id, "normwort", true);
            $processed_term[] = strtolower( $normwort );
        }
        $postids=$wpdb->get_col( $wpdb->prepare(
            "
            SELECT      k.post_id
            FROM        $wpdb->postmeta k
            WHERE       k.meta_key = %s 
                        AND k.meta_value = %s
            ",
            $meta_key,
            $normwort
        ) );
        foreach ( $postids as $id ) {
            $post = get_post( $id );
            $processed_term[] = strtolower( $post->post_title );
        }

        $processed_term = array_unique( $processed_term );

        return $processed_term;
    }


    static public function wp_ajax_mp_synonym_check_tag() {
        global $wpdb;

        $output = array();
        $tag =  $_POST['tag'];
        // 1. ist als Schlagwort im System, dann alles ok.
        $term = get_term_by( 'name', $tag, 'schlagwort' );
        if ( $term != false ) {
            $output[ 'status' ] = 'ok';
            $output[ 'orig' ] = $tag;
        } else {
            // 2. SynonymDB prüfen
            $postids=$wpdb->get_col( $wpdb->prepare(
                "
                SELECT      p.ID
                FROM        $wpdb->posts p
                WHERE       p.post_title = %s 
                ",
                    $tag
                ) );
            foreach ( $postids as $id ) {
                $post = get_post( $id );
                $normwort = get_post_meta( $id, "normwort", true);
                // Prüfen ob Normwort als Schlagwort existiert.
                $keyword = get_term_by( 'name', $normwort, 'schlagwort' );
                if ( $keyword !== false ) {
                    $output[ 'status' ] = 'replace-exist';
                    $output[ 'name' ] = $normwort;
                    $output[ 'id' ] = $keyword->term_id;
                    $output[ 'orig' ] = $tag;
                } else {
                    // schlagwort anlegen
                    $newterm = wp_insert_term( $normwort, 'schlagwort' );

                    $output[ 'status' ] = 'replace-new';
                    $output[ 'name' ] = $normwort;
                    $output[ 'id' ] = $newterm[ 'term_id' ];
                    $output[ 'orig' ] = $tag;
                }
            }
        }

        if ( $output == array() ) {
            // Gibt es noch nicht, nun Nationalbibliothek befragen.

            $gnd = wp_remote_get( "https://xgnd.bsz-bw.de/Anfrage?suchfeld=pica.swr&suchwort=" . $tag );
            $gndObj = json_decode( $gnd[ 'body'] );
            if ( is_array( $gndObj )) {
                $treffer = 0;
                foreach ( $gndObj as $obj ) {
                    if ( $obj->Typ == 'Sachschlagwort' ) {
                        $treffer = 1;
                        $normwort = $obj->Ansetzung;
                        foreach ( $obj->Synonyme  as $key => $value ) {
                            // Prüfen ob Synonym noch nicht gespeichert ist
                            $postids=$wpdb->get_col( $wpdb->prepare(
                                "
                                SELECT      p.ID
                                FROM        $wpdb->posts p
                                WHERE       p.post_title = %s 
                                ",
                                $value
                            ) );
                            if ( sizeof( $postids ) == 0 ) {
                                // Synonym speichern
                                $my_post = array(
                                    'post_title'    => wp_strip_all_tags( $value ),
                                    'post_status'   => 'publish',
                                    'post_type'     => 'synonym',
                                );
                                $back = wp_insert_post( $my_post );
                                if ( is_int( $back ) ) {
                                    $dummy = add_post_meta( $back, "normwort", $normwort, true );
                                }
                            }
                        }
                    }
                    // Normwort noch als Schlagwort speichern
                    $newterm = wp_insert_term( $normwort, 'schlagwort' );

                    $output[ 'status' ] = 'replace-new';
                    $output[ 'name' ] = $normwort;
                    $output[ 'id' ] = $newterm[ 'term_id' ];
                    $output[ 'orig' ] = $tag;
                }
                if ( $treffer == 0 ) {
                    $output[ 'status' ] = 'error';
                    $output[ 'url' ] = "https://xgnd.bsz-bw.de/Anfrage";
                    $output[ 'tagcolor' ] = "#FF0000";
                    $output[ 'orig' ] = $tag;
                }
            } else {
                $output[ 'status' ] = 'error';
                $output[ 'url' ] = "https://xgnd.bsz-bw.de/Anfrage";
                $output[ 'tagcolor' ] = "#FF0000";
                $output[ 'orig' ] = $tag;
            }
        }
        echo json_encode( $output );
        wp_die();
    }
}

