<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


class Materialpool_Schlagworte {

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function taxonomy_column( $columns ) {
        unset( $columns[ 'posts' ] );
	    $columns[ 'synonym' ] = __( 'Synonyme', Materialpool::get_textdomain() );
	    $columns[ 'uses' ] = __( 'Anzahl', Materialpool::get_textdomain() );
        return $columns;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function taxonomy_column_data( $out, $column_name, $term_id ) {
        global $wpdb;
		$out = '';
        switch ($column_name) {
            case 'uses':
                //$anzahl = $wpdb->get_col( $wpdb->prepare( "SELECT count( meta_id ) as anzahl  FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_schlagworte', $term_id) );
                $anzahl = get_term( $term_id, 'schlagwort' );
                $url =  admin_url( 'edit.php?post_type=material&schlagwort=' . $anzahl->slug );
	            $out .=  '<a href="' . $url . '">'.$anzahl->count . '</a>';
                break;

	        case 'synonym':
		        $term = get_term( $term_id, 'schlagwort' );
	        	$posts = get_posts( array(
			        'post_type' => 'synonym',
			        'orderby' => 'post_title',
			        'post_status' => 'published',
			        'meta_key' => 'normwort',
			        'meta_value' => $term->slug,
		        ));
	        	$counter=0;
	        	foreach ( $posts as $post ) {
	        		if ( $counter > 0 ) {
	        			$out .=  ', ';
			        }
			        $out .=  $post->post_title;
	        		$counter++;
		        }

	        	break;
            default:
                break;
        }
        return $out;
    }

    static public function pods_form_ui_field_pick_autocomplete_limit( $limit ) {
    	return 50;
    }
}
