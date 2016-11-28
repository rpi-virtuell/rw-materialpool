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

}

