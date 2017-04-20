<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


class Materialpool_FacetWP {

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function reindex_post_after_ajax_rating( $rate_userid, $post_id ) {
        // Transients für Frontendcache löschen
        delete_transient( 'facet_serach_entry-'.$post_id );
        delete_transient( 'facet_autor_entry-'.$post_id );
        delete_transient( 'facet_themenseite_entry-'.$post_id );
        delete_transient( 'facet_organisation_entry-'.$post_id );


        if ( class_exists( 'FacetWP_Indexer' ) ) {
            FWP()->indexer->save_post( $post_id );
        }
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function reindex_post_after_pods_saveing( $pieces, $is_new_item, $post_id ) {
        // Transients für Frontendcache löschen
        delete_transient( 'facet_serach_entry-'.$post_id );
        delete_transient( 'facet_autor_entry-'.$post_id );
        delete_transient( 'facet_themenseite_entry-'.$post_id );
        delete_transient( 'facet_organisation_entry-'.$post_id );

        if ( class_exists( 'FacetWP_Indexer' ) ) {
            FWP()->indexer->save_post( $post_id );
        }
    }

}
