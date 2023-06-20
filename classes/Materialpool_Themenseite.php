<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


class Materialpool_Themenseite {


    /**
     *
     * @since 0.0.1
     * @access	public
     */
    static public function load_template($template) {
        global $post;
	    if ( !is_object( $post ) ) return $template;
        if ($post->post_type == "themenseite" && !is_embed() ){
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-themenseite.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-themenseite.php';
                }
            }
            if ( is_archive() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/archive-themenseite.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/archive-themenseite.php';
                }
            }
            Materialpool_Statistic::log( $post->ID, $post->post_type );
            return $template_path;
        }
        return $template;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_template_check_external_files ( $checkArray ) {
        $checkArray[ 'materialpool/single-themenseite.php' ] = Materialpool::$plugin_base_dir . 'templates/single-themenseite.php';
        $checkArray[ 'materialpool/archive-themenseite.php'] = Materialpool::$plugin_base_dir . 'templates/archive-themenseite.php';
        return $checkArray;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function generate_taxonomy( $post_id ) {
        $post_type = get_post_type($post_id);

        if ( "themenseite" != $post_type ) return;

	    // Transients für Frontendcache löschen
	    delete_transient( 'facet_serach2_entry-'.$post_id );

        // Schlagwort det Themenseite in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'schlagwort' );
        $cats = explode( ',', $_POST[ 'pods_meta_thema_schlagworte' ] ) ;
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'schlagwort', true );

	    // Vorauswahl des Materials in term_rel speichern
	    wp_delete_object_term_relationships( $post_id, 'vorauswahl' );
	    $cats = $_POST[ 'pods_meta_vorauswahl' ];
	    if ( is_array( $cats ) ) {
		    foreach ( $cats as $key => $val ) {
			    $cat_ids[] = (int) $val;
		    }
	    }
	    if ( $cats!== null  ) {
		    $cat_ids[] = (int) $cats;
	    }
	    wp_set_object_terms( $post_id, $cat_ids, 'vorauswahl', true );

	    // Für den Fall, das auf der Startseite Themenseiten aufgelistet werden, den Cache der Startseite ungültig machen.
	    if (  function_exists( 'rocket_clean_post' ) ) {
		    // Startseite ermittln und invalid machen.
		    $frontpage_id = get_option( 'page_on_front' );
		    rocket_clean_post( $frontpage_id );
	    }
	    if ( class_exists( 'FWP_Cache') ) {
		    FWP_Cache()->cleanup();
	    }
	    if ( is_object( FWP() ) ) {
		    FWP()->indexer->save_post( $post_id );
	    }
    }
    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_gruppen( $id = null) {
        global $post;
        if ( $id == null ) $id = $post->ID;
        global $wpdb;

		$items_arr = array();
		$anzahl = count( get_field( 'themengruppen', $id ) );
		for ( $i = 0;  $i < $anzahl ;$i++ ) {
			$items_arr[ $i ] = array(
				'gruppe' => get_field( 'themengruppen_' . $i . '_gruppe_von_materialien', $id ),
				'gruppenbeschreibung' => get_field( 'themengruppen_' . $i . '_infos' , $id ),
				'auswahl' => get_field('themengruppen_' . $i . '_material_in_dieser_gruppe' , $id )
			);
		}

        return $items_arr;
    }

    /**
     * DEPRECATED
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_gruppen_by_groupid( $id = null) {
        global $post;
        if ( $id == null ) $id = $post->ID;
        global $wpdb;
        $query_str 		= $wpdb->prepare('SELECT *  FROM `' . $wpdb->prefix . 'pods_themenseitengruppen`	 	  
										 WHERE id = %s order by pandarf_order ', $id );
        $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
        return $items_arr;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function remove_from_bulk_actions( $actions ) {
        unset( $actions[ 'edit' ] );
        return $actions;
    }


	/**
	 * Change the columns for list table
	 *
	 * @since   0.0.1
	 * @access	public
	 * @var     array    $columns    Array with columns
	 * @return  array
	 */
	static public function cpt_list_head( $columns ) {
		$columns[ 'themenseite-schlagworte' ] = _x( 'Schlagworte', 'Material list field',  Materialpool::$textdomain );
		return $columns;
	}

	/**
	 * Add content for the custom columns in list table
	 *
	 * @since   0.0.1
	 * @access	public
	 * @var     string  $column_name    name of the current column
	 * @var     int     $post_id        ID of the current post
	 */
	static public function cpt_list_column( $column_name, $post_id ) {
		global $wpdb;
		$data = '';

		if ( $column_name == 'themenseite-schlagworte' ) {
			$schlagworte = get_metadata( 'post', $post_id, 'thema_schlagworte' );
			if ( is_array( $schlagworte[0] )) {
				foreach ( $schlagworte[0] as $schlagwort ) {
					$term = get_term( $schlagwort, "schlagwort" );
					$data .=  $term->name. '<br>';
				}
			}
		}

		echo $data;
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function remove_post_custom_fields() {
		remove_meta_box( 'vorauswahldiv' , 'themenseite' , 'normal' );
	}

	static public function action_row ( $actions, $post ) {
		if ($post->post_type =="themenseite"){
			// Themenseiten -> id, titel, url
			// Themengruppen -> id,themenid, titel
			// Materialien -> id, gruppenid, titel, url
			$themenseiten = array(array(
				'id' => $post->ID,
				'titel' => $post->post_title,
				'url' => get_permalink( $post->ID ),
			));
			$themengruppen = array();
			$materialien = array();
			$anzahl = count( get_field( 'themengruppen', $post->ID ) );
			for ( $i = 0;  $i < $anzahl ;$i++ ) {
				$themengruppen[] = array(
					'id' => $i,
					'themenid' => $post->ID,
					'titel' => get_field( 'themengruppen_' . $i . '_gruppe_von_materialien', $post->ID ),
				);
				$material = get_field('themengruppen_' . $i . '_material_in_dieser_gruppe' , $post->ID );
				foreach ( $material as $item ) {
					$m = get_post( $item);
					$materialien[] = array(
						'id' => $item,
						'gruppenid' => $i,
						'titel' => $m->post_title,
						'url' => get_permalink( $item )
					);
				}
			}
			$data = array(
				'themenseiten' => $themenseiten,
				'themengruppen' => $themengruppen,
				'materialien' => $materialien,
			);
			$actions['frontendedit'] = "<a id='themenedit" . $post->ID  . "' data-themenedit='".urlencode(json_encode( $data ))." ' href='javascript:FillThemenseitenDB(" . $post->ID  . ");'>Material im Frontend zuweisen</a>";
		}
		return $actions;
	}

    static function cron_repair_themenseiten_material_relations(){

       return;

        $themenseiten = get_posts([
            'post_type'=> 'themenseite',
            'numberposts' => -1,
            'post_status'=> 'any'
        ]);



        foreach ($themenseiten as $themenseite){

            ini_set('display_errors', 1);
            @error_reporting(E_ALL);

            $t_id = $themenseite->ID;

            var_dump($t_id);

            $gruppen = self::get_gruppen($t_id);
            try {
                if(is_array($gruppen)){
                foreach ($gruppen as $grp){

                    if(is_array($grp)){
                        $gruppen_name = $grp['gruppe'];

                        $i = 0;
                        if(is_array($grp['auswahl'])) {
                            foreach ($grp['auswahl'] as $material_id) {


                                    update_post_meta($material_id, 'material_themenseiten_' . strval($i) . '_single_themenseite', $t_id);
                                    update_post_meta($material_id, 'material_themenseiten_' . strval($i) . '_single_themengruppe', $gruppen_name);
                                    $i++;



                            }
                        }
                    }

                }

            }
            } catch (Exception $e) {
                echo $e->getCode();
            }


        }

    }

}
