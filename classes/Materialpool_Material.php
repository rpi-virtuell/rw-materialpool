<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo cpt auflistung anpassen
 */


class Materialpool_Material {

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function load_template($template) {
        global $post;

        if (is_tax() ) {
            return $template;
        }
	    if ( !is_object( $post ) ) return $template;
        $template_path = $template;
        if ($post->post_type == "material" && !is_embed() ){
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-material.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-material.php';
                }
            }
            if ( is_archive() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/archive-material.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/archive-material.php';
                }
            }
            $autoren = get_metadata( 'post', $post->ID, 'material_autoren', false );
            foreach( $autoren  as $autor ) {
                if ( is_array( $autor ) ) {
                    Materialpool_Statistic::log_autor( $autor  );
                }
            }
            $orgas = get_metadata( 'post', $post->ID, 'material_organisation', false );
            foreach( $orgas  as $orga ) {
                if ( is_array( $orga ) ) {
                    Materialpool_Statistic::log_organisation( $orga  );
                }
            }
            Materialpool_Statistic::log( $post->ID, $post->post_type );

            return $template_path;
        }
        if ( $post->post_type == "post" && Materialpool_Material::is_old_material() ) {
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-material-old.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-material-old.php';
                }
                return $template_path;
            }
        }
/*	    if ($post->post_type == "material" && is_embed() ){
		    if ( is_single() ) {
			    if ( $theme_file = locate_template( array ( 'materialpool/material-embed-content.php' ) ) ) {
				    $template_path = $theme_file;
			    } else {
				    $template_path = Materialpool::$plugin_base_dir . 'templates/material-embed-content.php';
			    }
			    return $template_path;
		    }
	    }*/

	    return $template;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function add_template_check_external_files ( $checkArray ) {
        $checkArray[ 'materialpool/single-material.php' ] = Materialpool::$plugin_base_dir . 'templates/single-material.php';
        $checkArray[ 'materialpool/archive-material.php'] = Materialpool::$plugin_base_dir . 'templates/archive-material.php';
        return $checkArray;
    }

    /**
     * Change the columns for list table
     *
     * @since   0.0.1
     * @access  public
     * @var     array    $columns    Array with columns
     * @return  array
     */
    static public function cpt_list_head( $columns ) {
        $columns[ 'material_views' ] = _x( 'Views', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-bildungsstufe' ] = _x( 'Bildungsstufe', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-schlagworte' ] = _x( 'Schlagworte', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-medientyp' ] = _x( 'Medientyp', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-organisation' ] = _x( 'Organisation', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-autor' ] = _x( 'Autoren', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-online' ] = _x( 'Online', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-status' ] = _x( 'Status', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-owner' ] = _x( 'Redakteur', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-vorschlag' ] = _x( 'Eintrager', 'Material list field',  Materialpool::$textdomain );
        return $columns;
    }

    /**
     * Add content for the custom columns in list table
     *
     * @since   0.0.1
     * @access  public
     */
    static public function add_taxonomy_filters() {
        global $typenow;

        // an array of all the taxonomyies you want to display. Use the taxonomy name or slug
        $taxonomies = array('bildungsstufe', 'schlagwort', 'medientyp', 'kompetenz', 'lizenz');

        // must set this to the post type you want the filter(s) displayed on
        if( $typenow == 'material' ){

            foreach ($taxonomies as $tax_slug) {
                $tax_obj = get_taxonomy($tax_slug);
                $tax_name = $tax_obj->labels->name;
                $terms = get_terms($tax_slug);
                if(count($terms) > 0) {
                    echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
                    echo "<option value=''>$tax_name</option>";
                    foreach ($terms as $term) {
                        $selected = '';
                        if ( isset(  $_GET[$tax_slug] )  && $_GET[$tax_slug] == $term->slug ) {
                            $selected = 'selected="selected"';
                        }
                        echo '<option value='. $term->slug . ' '. $selected . '>' . $term->name .' (' . $term->count .')</option>';
                    }
                    echo "</select>";
                }
            }
        }
    }

    /**
     * Add content for the custom columns in list table
     *
     * @since   0.0.1
     * @access  public
     * @var     string  $column_name    name of the current column
     * @var     int     $post_id        ID of the current post
     */
    static public function cpt_list_column( $column_name, $post_id ) {
        global $wpdb;

        $data = '';
        if ( $column_name == 'material-autor' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-autor-2-'.$post_id ) ) ) {
	            $autoren = get_metadata( 'post', $post_id, 'material_autoren' );
	            if ( is_array( $autoren[0] )) {
		            foreach ( $autoren[0] as $autor ) {
			            $post = get_post( $autor );

			            $vorname = get_post_meta($autor, 'autor_vorname', true);
			            $nachname = get_post_meta($autor, 'autor_nachname', true);
			            $data .= '<a href="/wp-admin/edit.php?post_type=material&all_posts=1&AUTOR_FILTER_FIELD_NAME=' .  $autor  . '">' . $vorname . ' '.$nachname  .'</a><br>';

		            }
	            }
	            set_transient( 'mp-cpt-list-material-autor-2-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
	            $data .= $transient;
            }
        }

        if ( $column_name == 'material-medientyp' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-medientyp2-'.$post_id ) ) ) {
                $medientyp = get_metadata( 'post', $post_id, 'material_medientyp' );
	            if ( is_array( $medientyp[0] )) {
		            foreach ( $medientyp[0] as $medium ) {
			            $term = get_term( $medium, "medientyp" );
			            $data .=  $term->name. '<br>';
		            }
	            }
                set_transient( 'mp-cpt-list-material-medientyp2-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
                $data .= $transient;
            }
        }
        if ( $column_name == 'material-bildungsstufe' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-bildungsstufe-'.$post_id ) ) ) {
                $bildungsstufe = get_metadata( 'post', $post_id, 'material_bildungsstufe' );
                if ( is_array( $bildungsstufe[0] )) {
	                foreach ( $bildungsstufe[0] as $bildung ) {
                        $term = get_term( $bildung, "bildungsstufe" );
		                $data .=  $term->name. '<br>';
	                }
                }
                set_transient( 'mp-cpt-list-material-bildungsstufe-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
                $data .= $transient;
            }

        }
        if ( $column_name == 'material-owner' ) {
            $post = get_post( $post_id);
            $user = get_user_by( 'ID', $post->post_author );
            $data = $user->display_name;
        }
        if ( $column_name == 'material-vorschlag' ) {
            $data = get_metadata( 'post', $post_id, 'material_von_name', true );
        }

        if ( $column_name == 'material-schlagworte' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-schlagworte-'.$post_id ) ) ) {
                $schlagworte = get_metadata( 'post', $post_id, 'material_schlagworte' );
	            if ( is_array( $schlagworte[0] )) {
		            foreach ( $schlagworte[0] as $schlagwort ) {
			            $term = get_term( $schlagwort, "schlagwort" );
			            $data .=  $term->name. '<br>';
		            }
	            }
	            set_transient( 'mp-cpt-list-material-schlagworte-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
                $data .= $transient;
            }
        }
        if ( $column_name == 'material-organisation' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-organisation-2-'.$post_id ) ) ) {
	            $organisationen = get_metadata( 'post', $post_id, 'material_organisation' );
                if ( is_array( $organisationen[0] )) {
                    foreach ( $organisationen[0] as $organisation ) {
	                    $post = get_post( $organisation );
	                    $data .= '<a href="/wp-admin/edit.php?post_type=material&all_posts=1&ORGA_FILTER_FIELD_NAME=' .  $post->ID . '">' . $post->post_title .'</a><br>';

                    }
                }
                set_transient( 'mp-cpt-list-material-organisation-2-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
                $data .= $transient;
            }
        }

        if ( $column_name == 'material-status' ) {
            $labels =  get_post_status_object( get_post_status( $post_id) );
            $data = $labels->label;

        }
        if ( $column_name == 'material_views' ) {
            $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
            $query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->mp_stats} WHERE object = %d",
                $post_id
            );
            $results = $wpdb->get_var(  $query );

            $data = $results;
        }

        echo $data;
    }

    /**
     * Set the sortable columns
     *
     * @since   0.0.1
     * @access  public
     * @param   array   $columns    array with the default sortable columns
     * @return  array   Array with sortable columns
     */
    static public function cpt_sort_column( $columns ) {
        return array_merge( $columns, array(
            'taxonomy-lizenz' => 'taxonomy-lizenz',
            'taxonomy-verfuegbarkeit' => 'taxonomy-verfuegbarkeit',
        ) );
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function generate_title( $post_id ) {
        global $wpdb;
	    $acf = $_POST['acf'];
        $post_type = get_post_type($post_id);
        $post_status = get_post_status ($post_id);
        $post_parent = wp_get_post_parent_id( $post_id );

        if ( "material" != $post_type ) return;
        if ( "trash" == $post_status ) return;

        $title =  get_metadata('post', $post_id, 'material_titel', true );

        $post_name = wp_unique_post_slug( sanitize_title( $title ), $post_id, 'publish', $post_type, $post_parent );
        $post_content = '';
        $url = '';
        // Prio 1: hochgeladenes Bild
        $pic  = $acf['field_5dc13b1362bd1'];
        if ( is_array( $pic ) ) {
            foreach ( $pic as $picArray ) {
                $id = (int) $picArray[ 'id' ];
            }
        }
        if ( is_int( $id ) ) {
            $urlA = wp_get_attachment_image_src( $id  );
            $url = $urlA[ 0 ];
        }
        // Prio 2, Cover URL
        if ( $url == '' ) {
            $url  = $acf['field_5dc13b57f2a74'];
        }

        if ( $url != '' ) {
            $post_content ='<img class="size-medium  alignleft" src="'. trim( $url ) .'" alt="" sizes="(max-width: 300px) 100vw, 300px">';
        }

        $post_content .= '<strong>' . wp_unslash( apply_filters( 'content_save_pre', $acf['field_5dbc82995b741'] ) ) . '</strong>';
        $post_content .= "\n\n<p>";
        $text = wp_unslash( $acf['field_5dbc82ca3e84f']);
        $text = strip_shortcodes( $text );
        $text = apply_filters( 'the_content', $text );
        $text = str_replace(']]>', ']]&gt;', $text);
        $excerpt_length = apply_filters( 'excerpt_length', 55 );
        $excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
        $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
        $post_content .= $text . '&hellip;';

        $wpdb->update(
            $wpdb->posts,
            array(
                'post_title' => stripslashes( $title ),
                'post_name' => $post_name,
                'post_content' => $post_content,
            ),
            array( 'ID' => $post_id ),
            array(
                '%s',
                '%s'
            ),
            array( '%d' )
        );

        $_POST[ 'post_title'] = $title;



        // Autoren für FacetWP spiechern
        delete_post_meta( $post_id, 'material_autor_facet' );
	    delete_post_meta( $post_id, 'material_autoren_facet_view' );
	    $autoren = get_post_meta( $post_id, 'material_autoren', false );
	    if ( is_array( $autoren[0] )) {
		    foreach ( $autoren[0] as $autor ) {
			    add_post_meta( $post_id, 'material_autoren_facet_view', $autor );
			    $vorname = get_post_meta($autor, 'autor_vorname', true );
			    $nachname = get_post_meta($autor, 'autor_nachname', true );
			    add_post_meta( $post_id, 'material_autor_facet', $vorname . ' ' . $nachname );

		    }
	    }




        // Organisationen für FacetWP speichern
        delete_post_meta( $post_id, 'material_organisation_facet' );
	    delete_post_meta( $post_id, 'material_organisation_facet_view' );
        delete_post_meta( $post_id, 'material_alpika_facet' );
	    $organisationen = get_post_meta( $post_id, 'material_organisation', false );
	    if ( is_array( $organisationen[0] )) {
		    foreach ( $organisationen[0] as $organisation ) {
			    add_post_meta( $post_id, 'material_organisation_facet_view', $organisation );
			    $organisationen_meta = get_post( $organisation );
			    add_post_meta( $post_id, 'material_organisation_facet', $organisationen_meta->post_title );
			    /*organisation_alpika*/
			    if(get_post_meta($organisationen_meta->ID,'organisation_alpika', true)){
				    add_post_meta( $post_id, 'material_alpika_facet', 1 );
			    }

			    /*organisation_konfession zu material konfessionen hinzugügen*/
			    $konfession = get_post_meta( $organisationen_meta->ID,'organisation_konfession', true);
			    if($konfession && isset($konfession['name'])){
				    wp_set_post_tags( $post_id, $konfession['name'], true);
			    }

		    }
	    }

        // Wenn Special, dann MaterialURL auf das Material selbst zeigen lassen.
        if (  $acf['field_5dbc823aa108e'] == 1  ) {
            clean_post_cache( $post_id );
            $p = get_post( $post_id );
            $url = get_permalink( $p );
            update_post_meta( $post_id, 'material_url', $url  );
            $_POST[ 'pods_meta_material_url' ] = $url;
        }

        // Transients für Backendliste löschen
        delete_transient( 'mp-cpt-list-material-autor-'.$post_id );
        delete_transient( 'mp-cpt-list-material-medientyp-'.$post_id );
        delete_transient( 'mp-cpt-list-material-medientyp2-'.$post_id );
        delete_transient( 'mp-cpt-list-material-schlagworte-'.$post_id );
        delete_transient( 'mp-cpt-list-material-organisation-'.$post_id );

        // Transients für Frontendcache löschen
        delete_transient( 'facet_serach2_entry-'.$post_id );
        delete_transient( 'rss_material_entry-'.$post_id );
        delete_transient( 'facet_autor_entry-'.$post_id );
        delete_transient( 'facet_themenseite_entry-'.$post_id );
        delete_transient( 'facet_organisation_entry-'.$post_id );

        delete_metadata( 'post', $post_id, 'material_v2_screesnhot_url' );
        delete_metadata( 'post', $post_id, 'material_v2_screesnhot_gen' );




        // ggf Abhängige Themenseiten aus dem RocketCache entfernen
        //        $themen = Materialpool_Material::get_themenseiten_for_material( $post_id );
        //        if ( is_array( $themen ) &&  sizeof( $themen ) > 0 ) {
        //            foreach ( $themen as $item ) {
        //                if (  function_exists( 'rocket_clean_post' ) ) {
        //                    rocket_clean_post( $item->id );
        //                }
        //            }
        //        }

	    // Für den Fall, das auf der Startseite Materialien aufgelistet werden, den Cache der Startseite ungültig machen.
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
        Materialpool_Material::set_createdate( $post_id );
        Materialpool_Material::save_material_to_themenseiten($post_id);
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function remove_post_custom_fields() {
        //remove_meta_box( 'tagsdiv-altersstufe' , 'material' , 'normal' );
        //remove_meta_box( 'bildungsstufediv' , 'material' , 'normal' );
        //remove_meta_box( 'tagsdiv-inklusion' , 'material' , 'normal' );
        //remove_meta_box( 'tagsdiv-konfession' , 'material' , 'normal' );
        //remove_meta_box( 'tagsdiv-lizenz' , 'material' , 'normal' );
        //remove_meta_box( 'medientypdiv' , 'material' , 'normal' );
        //remove_meta_box( 'tagsdiv-schlagwort' , 'material' , 'normal' );
        //remove_meta_box( 'tagsdiv-sprache' , 'material' , 'normal' );
        //remove_meta_box( 'tagsdiv-verfuegbarkeit' , 'material' , 'normal' );
        //remove_meta_box( 'tagsdiv-zugaenglichkeit' , 'material' , 'normal' );
        //remove_meta_box( 'vorauswahldiv' , 'material' , 'normal' );
        //remove_meta_box( 'tagsdiv-werkzeug' , 'material' , 'normal' );
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function custom_post_status(){
        register_post_status( 'vorschlag', array(
            'label'                     => _x( 'Vorschlag', 'material' ),
            'public'                    => false,
            'show_in_admin_all_list'    => false,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Vorschlag <span class="count">(%s)</span>', 'Vorschläge <span class="count">(%s)</span>' )
        ) );
        register_post_status( 'check', array(
            'label'                     => _x( 'Überprüfen', 'material' ),
            'public'                    => false,
            'show_in_admin_all_list'    => false,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Überprüfen <span class="count">(%s)</span>', 'Überprüfen <span class="count">(%s)</span>' )
        ) );
	    register_post_status( 'broken', array(
		    'label'                     => _x( 'BrokenLink', 'material' ),
		    'public'                    => false,
		    'show_in_admin_all_list'    => false,
		    'show_in_admin_status_list' => true,
		    'label_count'               => _n_noop( 'BrokenLink <span class="count">(%s)</span>', 'BrokenLinks <span class="count">(%s)</span>' ),
	    ) );
	    register_post_status( 'notbroken', array(
		    'label'                     => _x( 'Not Broken', 'material' ),
		    'public'                    => true,
		    'show_in_admin_all_list'    => false,
		    'show_in_admin_status_list' => true,
		    'label_count'               => _n_noop( 'Not Broken <span class="count">(%s)</span>', 'Not Broken <span class="count">(%s)</span>' ),
	    ) );
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function append_post_status_list(){
        global $post;

	    $complete1 = $complete2 = $complete3 = $complete4 = '';
	    $setlabel = '';



	    if($post->post_type == 'material'){
	        switch ($post->post_status){

		        case 'vorschlag':
			        $complete1 = ' selected=\"selected\"';
			        $setlabel = '$("#post-status-display").html("Vorschlag");';
			        break;
		        case 'check':
			        $complete2 = ' selected=\"selected\"';
			        $setlabel = '$("#post-status-display").html("Überptüfen");';
			        break;
		        case 'broken':
			        $complete3 = ' selected=\"selected\"';
			        $setlabel = '$("#post-status-display").html("Broken Link");';
			        break;
                case 'notbroken':
			        $complete4 = ' selected=\"selected\"';
			        $setlabel = '$("#post-status-display").html("Not Broken");';
			        break;
	        }
            echo '
          <script>
          jQuery(document).ready(function($){
               $("select#post_status").append("<option value=\"vorschlag\" '.$complete1.'>Vorschlag</option>");
               $("select#post_status").append("<option value=\"check\" '.$complete2.'>Überprüfen</option>");
               $("select#post_status").append("<option value=\"broken\" '.$complete3.'>Broken Link</option>");
               $("select#post_status").append("<option value=\"notbroken\" '.$complete4.'>Not-Broken Link</option>");';

            echo $setlabel .'
            
          });
          </script>
          ';
        }
    }

    public static function publish_on_not_broken_link($new_status, $old_status, WP_Post  $post){
	    if ( 'notbroken' === $new_status  ){
		    $post->post_status = 'publish';
		    wp_update_post($post);
		    update_post_meta($post->ID,'material_url_notbroken',1);
	    }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function add_metaboxes() {
        add_meta_box('material_bookmarklet', __( 'Bookmarklet', Materialpool::$textdomain ), array( 'Materialpool_Material', 'bookmarklet_metabox' ), 'material', 'side', 'default');
        add_meta_box('material_url', __( 'Material', Materialpool::$textdomain ), array( 'Materialpool_Material', 'material_metabox' ), 'material', 'side', 'default');
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function bookmarklet_metabox() {
        $js = file_get_contents( Materialpool::$plugin_base_dir . 'js/bookmarklet.js' );
        echo "<a href='". $js ."'>". __( 'Materialpool++', Materialpool::$textdomain ) ."</a><br>";
        _e( 'Zieh den Link in deine Lesezeichenliste', Materialpool::$textdomain );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function material_metabox() {
        $url = Materialpool_Material::get_url();
        echo "<a target='_new' href='". $url ."' class='preview button' >". __( 'zum Material', Materialpool::$textdomain ) ."</a><br><br>";
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function material_list_post_join( $join ) {
        global $pagenow, $wpdb;

        if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='material' && isset( $_GET['s'] )  && $_GET['s'] != '') {
            $join .='LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
        }
        return $join;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function material_list_post_where( $where ) {
        global $pagenow, $wpdb;

        if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='material' && isset( $_GET['s'] )  && $_GET['s'] != '') {
            $where = preg_replace(
                "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
        }
        return $where;
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function material_list_post_distinct(  $distinct ) {
        global $pagenow;

        if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='material' && isset( $_GET['s'] ) && $_GET['s'] != '') {
            $distinct = " DISTINCT ";
        }
        return $distinct;
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function check_404_old_material() {
        global $wp_query;
        global $wpdb;

        if ( is_404() ) {
            $uri = $_SERVER[ 'REQUEST_URI' ];
            if ( strpos( $uri, '/material/') !== false ) {
                // old_slug im Material suchen und ggf Umleiten
                $query = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'old_slug' and meta_value = %s",
                    $uri
                );
                $results = $wpdb->get_var(  $query );
                if ( $results !== null ) {
                    status_header( 301 );
                    $wp_query->is_404=false;
                    if ( wp_redirect( get_permalink( $results ) ) ) {
                        exit;
                    }
                }
            }
            if ( strpos( $uri, '/tagpage/') !== false ) {
                // old_slug im Material suchen und ggf Umleiten
                $query = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'old_slug' and meta_value = %s",
                    $uri
                );
                $results = $wpdb->get_var(  $query );
                if ( $results !== null ) {
                    status_header( 301 );
                    $wp_query->is_404=false;
                    if ( wp_redirect( get_permalink( $results ) ) ) {
                        exit;
                    }
                }

                // Mapping alte Tagpage URLS auf Themenseiten-Schlagworte
                $data = array();
                $data[ '/tagpage/8B0CFB06-07F5-4412-A277-037EAF7DEC72' ] = 'Bioethik';
                $data[ '/tagpage/0AE84313-D98D-4238-97DF-05BF78E65F52' ] = 'Weltreligionen';
                $data[ '/tagpage/489AF876-9CF0-45E8-AE33-06B94F1F432C' ] = 'Ökumene';
                $data[ '/tagpage/AAA3FB6E-ABC7-4202-90CB-07BAA5C911F1' ] = 'Christologie';
                $data[ '/tagpage/4E4BDBF8-C244-4942-999D-0831ED7F6AD3' ] = 'Glück';
                $data[ '/tagpage/A620D6E0-1CE0-472E-87BF-08832B7EE780' ] = 'Differenzierung';
                $data[ '/tagpage/6F49BB78-35EC-4487-9DEB-0AD429AB1695' ] = 'Religionsunterricht';
                $data[ '/tagpage/27456C9B-36AE-46AD-B670-0AF4467AF697' ] = 'Kinderarbeit';
                $data[ '/tagpage/81582B7D-20AD-4745-B224-0BB0369FEEA5' ] = 'Gemeinschaftsschule';
                $data[ '/tagpage/CABDD079-2EC7-41BF-9519-0C6038CC090A' ] = 'Toleranz';
                $data[ '/tagpage/E3A02B48-176F-4078-AC52-0D98A68D9C4C' ] = 'Gewalt';
                $data[ '/tagpage/EC556AFD-70F4-4D36-A6CD-0F560DDC38F2' ] = 'Gott';
                $data[ '/tagpage/CF37F2C2-333F-402D-A12D-12EC80646150' ] = 'Frieden';
                $data[ '/tagpage/F6704664-0043-458B-9123-133E56FECAD3' ] = 'Behinderung';
                $data[ '/tagpage/D2D70824-F884-44C1-BD42-141917D4F9CB' ] = 'Antisemitismus';
                $data[ '/tagpage/BD56D5A9-27D8-4A48-BEC8-1827B19A8C3F' ] = 'Konfessionen';
                $data[ '/tagpage/622E60CA-8920-4537-B964-1AC2A4AED861' ] = 'Menschenbild';
                $data[ '/tagpage/4C7EE0C6-FAF0-490B-A1C1-1B79F01A6D8D' ] = 'Kinder';
                $data[ '/tagpage/C9320D9A-173A-4F55-8FB4-1C8949FA1C74' ] = 'Calvin';
                $data[ '/tagpage/C36377B5-9821-4372-A893-1CF3FBC424BA' ] = 'Frieden';
                $data[ '/tagpage/9873C1F0-B0C4-41CE-BCC9-1DE4FC9CD231' ] = 'Israel';
                $data[ '/tagpage/F5F15837-E178-49F4-950F-1F931962FF1B' ] = 'Ostern';
                $data[ '/tagpage/2CD2B671-4DD0-403B-91E8-20C4DB6988F1' ] = 'Jona';
                $data[ '/tagpage/CC4F673B-B90D-40F5-8F9A-20DD9E5EB6FC' ] = 'Liebe';
                $data[ '/tagpage/F549345E-0192-4325-830E-228125351CCE' ] = 'Israel';
                $data[ '/tagpage/FC38344E-B39C-4E90-9592-22E52E6B21EB' ] = 'Abendmahl';
                $data[ '/tagpage/79EBA410-723E-4A11-B175-23DF0B919797' ] = 'Rassismus';
                $data[ '/tagpage/18AEE466-CB95-4545-99C1-24CC69A34ACB' ] = 'Kinderrechte';
                $data[ '/tagpage/E9DE6811-BC3B-4DC0-BD96-258797A729D5' ] = 'Gerechtigkeit';
                $data[ '/tagpage/CAC702E8-D75C-4DC0-AF6D-265804FAD246' ] = 'Spielfilm';
                $data[ '/tagpage/66E45CD5-BCAB-4978-B3A7-26E3FAE0C82E' ] = 'Globalisierung';
                $data[ '/tagpage/46721F12-96A0-49D2-A36C-2B389DAA3E7C' ] = 'Psalm 23';
                $data[ '/tagpage/99555EB7-36C3-4291-848D-2B5C217359E0' ] = 'Friedenspädagogik';
                $data[ '/tagpage/EE508173-819E-457A-9FC7-2BADAFDA4195' ] = 'Islam';
                $data[ '/tagpage/AEBE75EC-8CC7-4A93-AB50-2BB1D3C1A86A' ] = 'Gottesbild';
                $data[ '/tagpage/38B5E21C-1B48-42B3-9ED8-3057BB16D8B2' ] = 'Propheten';
                $data[ '/tagpage/B9444119-7A4A-4BE5-A788-31EC07A40397' ] = 'Philosophie';
                $data[ '/tagpage/E5EDF8B4-67CE-4BE0-BB4E-3309C45A949A' ] = 'Hinduismus';
                $data[ '/tagpage/C350E1D1-5389-4E85-BDAE-339A9B5BAE50' ] = 'Gebet';
                $data[ '/tagpage/9747D7D6-1A81-4EE2-B41D-3482D982C2F4' ] = 'Jakob';
                $data[ '/tagpage/DB6F6D9A-965E-448A-8CFD-36B1245C3506' ] = 'Jesus';
                $data[ '/tagpage/610699D8-18BC-4B7F-8BE4-39C62EDA4768' ] = 'Papst';
                $data[ '/tagpage/618D7B13-6EF5-456F-B8C1-3ADA6632D495' ] = 'Franziskus';
                $data[ '/tagpage/ABD6323E-0F44-4EE8-8628-3BC3AF12CBBB' ] = 'Hexenverfolgung';
                $data[ '/tagpage/987CE798-ECF1-4A03-B9BE-3C5FAB465832' ] = 'Vorurteil';
                $data[ '/tagpage/51B77216-D142-4188-B70A-3E70FA5B29B4' ] = 'Schöpfung';
                $data[ '/tagpage/2E27E2C7-2ADB-45E2-ABF9-3E8DD0CBBAEF' ] = 'Textarbeit';
                $data[ '/tagpage/596B4AF5-7728-45AB-992D-3ED27AFAD0D8' ] = 'Koran';
                $data[ '/tagpage/B6CD500C-A9F8-4AD2-B410-3F77EFE25678' ] = 'Gott';
                $data[ '/tagpage/4CEF261E-8ECC-4269-8B36-4239F4F0F7A7' ] = 'Todesstrafe';
                $data[ '/tagpage/CEF5B5D7-EAA2-4AAD-8BFB-4535199858EF' ] = 'Fastnacht';
                $data[ '/tagpage/CCA79F65-E4D9-49BB-97F2-45E023E0627A' ] = 'Klimawandel';
                $data[ '/tagpage/B425B93A-8DF4-4E71-8A60-45E662794A42' ] = 'Religionskritik';
                $data[ '/tagpage/E06FF036-B23D-4265-AC03-48130DFE07E7' ] = 'Elisabeth von Thüringen';
                $data[ '/tagpage/4F73E492-A3EA-4548-818F-4B0B3611A993' ] = 'Jesus Christus (der Erlöser)';
                $data[ '/tagpage/4CBFEA31-A109-431B-9193-4BBA2CAAC1A9' ] = 'Bibel';
                $data[ '/tagpage/542A1037-C819-4073-BB26-4CC872174BE3' ] = 'Aberglaube';
                $data[ '/tagpage/293C59EC-704D-438E-8BBE-4D2C6AB49F06' ] = 'Noah';
                $data[ '/tagpage/7097B410-8D34-477D-9493-5029E9646879' ] = 'Zehn Gebote';
                $data[ '/tagpage/08BFD499-7AA1-4448-8500-5434F8429E77' ] = 'Sterbehilfe';
                $data[ '/tagpage/5AD44681-9FF8-4B26-800D-549833A8BAFF' ] = 'Cyber-Mobbing';
                $data[ '/tagpage/32CABB14-84FE-40E2-9700-54E28FCAB53C' ] = 'Umwelt Jesu';
                $data[ '/tagpage/50EF72CE-35A0-414C-932F-550D16EC46E0' ] = 'Karwoche';
                $data[ '/tagpage/E08560A2-DD9D-40BC-81F1-55D6562D5412' ] = 'Kreuzestheologie';
                $data[ '/tagpage/B560441F-6162-4CCD-B252-58B64388C4C9' ] = 'Symbol';
                $data[ '/tagpage/8DE8F241-FEA7-4463-B596-5CF2DD16DBCE' ] = 'Filmdidaktik';
                $data[ '/tagpage/D7339F7E-9DAF-4687-B6D7-5E1A7882FCF4' ] = 'Theodizee';
                $data[ '/tagpage/52A35EAF-7584-405E-AEAA-641B5AEFA0F0' ] = 'Filmanalyse';
                $data[ '/tagpage/3A54C5C9-F390-40DB-9F14-64770501B921' ] = 'Judentum';
                $data[ '/tagpage/8645D67D-F7B0-4A02-A3DE-648B246279EC' ] = 'Judentum';
                $data[ '/tagpage/D14B071B-1A5D-4668-AE84-64EC831E5672' ] = 'Film';
                $data[ '/tagpage/7B46CE91-3327-43BD-823F-67B7F45A9230' ] = 'Menschenrechte';
                $data[ '/tagpage/EACF2368-03FC-464E-A477-69FC34BE3FCB' ] = 'Paulus';
                $data[ '/tagpage/F251B29B-73A3-4BAF-A176-6B4586926CB4' ] = 'Taufe';
                $data[ '/tagpage/B834AF14-C55C-4630-BBFC-6B7DC7466246' ] = 'Diakonie';
                $data[ '/tagpage/CE3E3475-E9AF-4CE4-8043-6EED38920F86' ] = 'Auferstehung';
                $data[ '/tagpage/0A77E477-9B0F-47F0-8DD4-706592AFA0E4' ] = 'Kreuz';
                $data[ '/tagpage/B42EDDAE-DFF7-4879-8225-70AE8FA8A15B' ] = 'Bibel';
                $data[ '/tagpage/7F284F22-0B0B-4998-B218-70D37EE2145D' ] = 'Kooperatives Lernen';
                $data[ '/tagpage/3D492BBC-8186-4A13-ADF2-70F77A023FC7' ] = 'Rut';
                $data[ '/tagpage/772297D0-1069-41CE-9266-74C639C8ED9F' ] = 'Maria';
                $data[ '/tagpage/F67F389B-A0DB-4420-99B1-757D5BC87933' ] = 'Jakob';
                $data[ '/tagpage/FED4D4B3-ADE6-4332-9B24-769F92B6A108' ] = 'Amos';
                $data[ '/tagpage/545EE999-E9BA-4E6A-B4BF-78960FD0464B' ] = 'Kirchenraum';
                $data[ '/tagpage/03E0FDD1-2136-4F0B-8600-790A3D32589D' ] = 'Katholische Kirche';
                $data[ '/tagpage/1458BAE4-1707-4B25-AE33-798B06589BD3' ] = 'Behinderung';
                $data[ '/tagpage/9DA8EFC7-E24A-486B-9B1C-79F6A4687E02' ] = 'Islam';
                $data[ '/tagpage/02F8292C-A8AD-4BF5-91B8-7D515BBE0519' ] = 'Rosch ha-Schana';
                $data[ '/tagpage/342ADCE4-DED2-406C-B989-7F303CE8F4B3' ] = 'Friedenspädagogik';
                $data[ '/tagpage/BF9FF171-FB2F-4031-A39B-854ABF7BDDEB' ] = 'Jesus';
                $data[ '/tagpage/C8E5ECA2-071C-4A39-B88A-86239B21D171' ] = 'Kompetenzorientierung';
                $data[ '/tagpage/F45657F2-D21E-4936-9FB0-8AB2B1CD1673' ] = 'Dilemma';
                $data[ '/tagpage/24159F82-0176-44B3-85FB-8AB8C2F13F9F' ] = 'Gebet';
                $data[ '/tagpage/148BC103-64AB-4495-BA20-8AF95B957959' ] = 'Vaterunser';
                $data[ '/tagpage/5FD7C202-43C3-45A7-BE17-94017AB8109E' ] = 'Schöpfung';
                $data[ '/tagpage/CD8EBA22-08C4-4AE0-A705-94B2E70A7D6B' ] = 'Tod';
                $data[ '/tagpage/EB59BF9B-C2F1-48FD-AED2-9566E474F841' ] = 'Weltreligionen';
                $data[ '/tagpage/27CC9963-0593-458C-917F-980F4F50579D' ] = 'Inklusion';
                $data[ '/tagpage/F12B83F6-7F64-40BB-9429-99410CA2B0EA' ] = 'Noah';
                $data[ '/tagpage/D9F09B00-3159-43B5-9D72-9A5499087C43' ] = 'Karfreitag';
                $data[ '/tagpage/F84ED260-E8CC-4C26-8A7D-9CF7CCB7C3DC' ] = 'Gott';
                $data[ '/tagpage/B2697817-889D-4952-B17B-9D53E9E40B7F' ] = 'Religionspädagogik';
                $data[ '/tagpage/992BF121-5931-403B-9092-9EC11A06AEE8' ] = 'Nahostkonflikt';
                $data[ '/tagpage/AD932B62-68DB-4A8A-AA24-9F178B9A14E1' ] = 'Evangelische Kirche';
                $data[ '/tagpage/90F61EB7-30A0-4F80-8F76-9F4BD6A961E4' ] = 'Mohammed';
                $data[ '/tagpage/5D92CBB5-57BD-4895-8FDF-9F729C3D3AD6' ] = 'Krieg';
                $data[ '/tagpage/14411804-E1E1-483E-B9BE-A161D8554546' ] = 'Gewissen';
                $data[ '/tagpage/656A3455-EAE2-4C87-ABE5-A2FF4F58334B' ] = 'Cyber-Mobbing';
                $data[ '/tagpage/4AF2A5EC-F4FC-4290-8ED7-A5426A6F7EEA' ] = 'Familie';
                $data[ '/tagpage/AC2CDC34-2343-42BD-9D9A-A5B11F454DC3' ] = 'Atheismus';
                $data[ '/tagpage/8EC6B604-30E9-402E-A13F-A68A7150B34D' ] = 'Martin Luther King';
                $data[ '/tagpage/F6C3CE33-FAE2-47BE-8B06-A78E2A227931' ] = 'Film';
                $data[ '/tagpage/732B8563-DE63-47F8-821F-A9366D19082F' ] = 'Ostern';
                $data[ '/tagpage/784A79F6-E0D4-4406-B68D-AAB91D2ECB0D' ] = 'Schuld';
                $data[ '/tagpage/25913558-2317-4B70-93A9-AB14940CEC4C' ] = 'Bewahrung der Schöpfung';
                $data[ '/tagpage/78024F2E-3EB6-470A-B192-AC18E1B95192' ] = 'Rahel';
                $data[ '/tagpage/6E3C8F78-C5B0-4FA7-A066-AE771EC13BF3' ] = 'Sekte';
                $data[ '/tagpage/76DAA391-2755-4A57-8F03-AE78B445176E' ] = 'Kino';
                $data[ '/tagpage/46EE1958-D63F-486C-8A85-B06786F4092E' ] = 'Franziskus';
                $data[ '/tagpage/1ECBBB49-CF7C-4CA1-B6DF-B1AE668BB3E6' ] = 'Ökologie';
                $data[ '/tagpage/A5EB0C92-F146-4DBE-94B6-B46E9B41D807' ] = 'Paradies';
                $data[ '/tagpage/393DF8A5-A021-4279-8009-B5C838616505' ] = 'Psalmen';
                $data[ '/tagpage/862822FD-70C3-43F6-A629-B6C3DA627FCD' ] = 'Pessach';
                $data[ '/tagpage/9E5BB8F7-74F2-4232-8DE7-B6FA1BAFFFFB' ] = 'Klima-Kollekte';
                $data[ '/tagpage/81E48E81-6F38-4C88-8BEA-BA22A0AD8BF5' ] = 'Konfessionelle Kooperation';
                $data[ '/tagpage/556DE2F5-19AF-41B3-A3FD-BA84EAA09B39' ] = 'Ehe';
                $data[ '/tagpage/A1C14BA1-5B68-49CF-8277-BB2C9BA3BD1E' ] = 'Homosexualität';
                $data[ '/tagpage/70DBFDF1-B740-43ED-ADE5-BB42270B2945' ] = 'Geocaching';
                $data[ '/tagpage/FD08025F-5E5C-4BBE-8B9C-BC28BDEC9792' ] = 'Freiarbeit';
                $data[ '/tagpage/1038AF5D-CEB9-48CC-836F-BC94B59CD536' ] = 'Interreligiöses Lernen';
                $data[ '/tagpage/3E1025F7-D513-40B3-B7DD-BD04E5765E00' ] = 'Euthanasie';
                $data[ '/tagpage/344E2DB4-3E27-4F21-9C6B-BD7E4E430ACD' ] = 'Klimawandel';
                $data[ '/tagpage/2E15D5DC-2FE4-4D10-A4E5-BFCEBAE6F245' ] = 'Jom Kippur';
                $data[ '/tagpage/4E4F56F0-AF1C-42A2-B72F-C0EF47B55776' ] = 'Nächstenliebe';
                $data[ '/tagpage/DA40A555-3735-42B3-A7BD-C21130254748' ] = 'Nahtoderfahrung';
                $data[ '/tagpage/F6424083-6EED-4E95-ADEF-C3221B190D78' ] = 'Sexualität';
                $data[ '/tagpage/1AAA3E1F-6771-4E42-9B74-C3697BA054A3' ] = 'Freundschaft';
                $data[ '/tagpage/1A51BF59-0BEB-41A1-A5AE-C3CB5FD042D3' ] = 'Qumran';
                $data[ '/tagpage/32BF6BAA-7E7C-4F42-BD58-C3F3F9C8E380' ] = 'Gottesbeweis';
                $data[ '/tagpage/3BB056F1-2233-4F89-A378-C63907D9A6A8' ] = 'Bibelübersetzung';
                $data[ '/tagpage/993D2C8F-ABE9-4BEF-B0F3-C6F94CC2F4B2' ] = 'Familie';
                $data[ '/tagpage/B0E116A0-B2DD-4D42-9D44-C86643C1CC4C' ] = 'Terrorismus';
                $data[ '/tagpage/1B948ECC-7EAF-4625-9F0F-C8907B2BA23D' ] = 'Nationalsozialismus';
                $data[ '/tagpage/C096DAA2-5E98-44AE-926A-C915121FF135' ] = 'Bildbetrachtung';
                $data[ '/tagpage/BC017040-D052-48F4-BE82-CADBDD05756F' ] = 'Trickfilm';
                $data[ '/tagpage/01372639-2D10-4F7B-BB16-CAE8CE38AC26' ] = 'Gewalt';
                $data[ '/tagpage/372A0741-0520-4BC3-949E-CB7BF4AF4840' ] = 'Diakonie';
                $data[ '/tagpage/006B13B8-72E9-4985-87CD-CDD8EE2FC2C4' ] = 'Psalmen';
                $data[ '/tagpage/66F70273-DA88-48AF-9E16-CEE416B38911' ] = 'Bibel';
                $data[ '/tagpage/910523B7-2135-4A2C-815C-D4FB56C72473' ] = 'Albert Schweitzer';
                $data[ '/tagpage/3D047296-4F9D-405F-9CBB-D8850A6E59B4' ] = 'Religionsunterricht';
                $data[ '/tagpage/51E032C7-4147-404D-A8DF-DD29BE35FBF9' ] = 'Kreationismus';
                $data[ '/tagpage/7774A609-D6F8-496B-92D3-DD2FA66387F3' ] = 'Kompetenzorientierung';
                $data[ '/tagpage/3128027E-3DE8-47D3-84C6-DE8BC1F25A54' ] = 'Kindertheologie';
                $data[ '/tagpage/824E687F-1EA5-425D-95EC-DF3A67BD4FD4' ] = 'Ritual';
                $data[ '/tagpage/1D582B47-51B2-484B-84B2-E0D31921726B' ] = 'Kirchenraum';
                $data[ '/tagpage/1860E67D-26F7-4C67-8A77-E2CBC3B84976' ] = 'Auferstehung';
                $data[ '/tagpage/34B9A391-D9CB-4E9B-BD32-E50EB8FA3857' ] = 'Tod';
                $data[ '/tagpage/D6D16636-559C-4CAA-92D2-E8F189B1E8D8' ] = 'Freundschaft';
                $data[ '/tagpage/0407C082-40F7-43ED-A486-E974A4ADF659' ] = 'Soziale Gerechtigkeit';
                $data[ '/tagpage/BAEDC50F-94D6-4411-835A-E9896DC40544' ] = 'Frieden';
                $data[ '/tagpage/A9181B19-8E19-4258-BED8-EA51DD0724E1' ] = 'Unterrichtsvorbereitung';
                $data[ '/tagpage/DBBBA537-6DEF-4B57-834B-EB5158B0DBA4' ] = 'Jan Hus';
                $data[ '/tagpage/92E785DD-A7B7-4A21-8D6B-EBFB4843E234' ] = 'Ramadan';
                $data[ '/tagpage/BCA7987F-46E1-4FFC-AB94-EC21698D19A6' ] = 'Friedhof';
                $data[ '/tagpage/F97073B0-2719-48A3-BD68-EC8082CBA198' ] = 'Vatikan';
                $data[ '/tagpage/0A947B55-E67F-4F91-B882-ECD27A9F403E' ] = 'Tod';
                $data[ '/tagpage/083BC733-89AC-48B8-83AD-EDE2D3BCDA47' ] = 'Aufgaben';
                $data[ '/tagpage/98DD90DB-773A-4DBA-9F66-F0BF2086B868' ] = 'Bewahrung der Schöpfung';
                $data[ '/tagpage/D025D4B9-80B0-4F4F-B0AE-F1845CB5F821' ] = 'Internet';
                $data[ '/tagpage/3B618CAA-2226-4263-A791-F3F23A5A358A' ] = 'David';
                $data[ '/tagpage/2F0B5658-32AF-4AD1-B879-F3FB39CB39C8' ] = 'Armut';
                $data[ '/tagpage/11D76B55-FA50-4813-AB46-F53B1DD6F726' ] = 'Taizé';
                $data[ '/tagpage/103AC2A3-DFB7-4CA7-B4F8-F951CA0856C4' ] = 'Abendmahl';
                $data[ '/tagpage/8713455D-C19A-4490-92B6-F9C5FF92247D' ] = 'Apostelgeschichte';
                $data[ '/tagpage/1F6948BA-8581-403D-BE79-FB9045CC0074' ] = 'Interreligiöses Lernen';
                $data[ '/tagpage/1479D2FD-1717-49E7-8CFF-FC20CF87B7B1' ] = 'Organspende';
                $data[ '/tagpage/08528568-F6B0-4F2F-8739-FF947427D129' ] = 'Sucht';

                if ( key_exists( $uri, $data ) ) {
                    $keyword = $data[ $uri ];
                    $args = array(
                        'post_type' => 'themenseite',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'schlagwort',
                                'field'    => 'name',
                                'terms'    => $keyword,
                            ),
                        ),
                    );
                    $query = new WP_Query( $args );
                    if ( is_array( $query->posts ) ) {
                        if ( wp_redirect( get_permalink( $query->posts[ 0 ]->ID ) ) ) {
                            exit;
                        }
                    }
                }


            }
            if ( strpos( $uri, '/check_autor/') !== false ) {
                $hash = substr( $uri, 13 );

                $result = $wpdb->get_var( $wpdb->prepare( "SELECT $wpdb->postmeta.post_id   FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_value = %s " , $hash ) );
                if ( ! is_wp_error( $result) && $result !== false ) {
                    add_metadata( 'post', $result, 'autor_email_read', time() );
                    wp_redirect( get_permalink( $result) );
                    exit;
                }
            }
            if ( strpos( $uri, '/check_organisation/') !== false ) {
                $hash = substr( $uri, 20 );
                $result = $wpdb->get_var( $wpdb->prepare( "SELECT $wpdb->postmeta.post_id   FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_value = %s " , $hash ) );
                if ( ! is_wp_error( $result) && $result !== false ) {
                    add_metadata( 'post', $result, 'organisation_email_read', time() );
                    wp_redirect( get_permalink( $result) );
                }
            }
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function remove_from_bulk_actions( $actions ) {
        unset( $actions[ 'edit' ] );
        return $actions;
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function row_actions( $actions, $post ) {
        if ( $post->post_type == "material" ) {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function vorschlag_shortcode( $args ) {
        $user = "";
        $email = "";
        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            $user = $current_user->display_name;
            if ($user == '') $user = $current_user->user_login;
            $email = $current_user->user_email;
        }
        $back = <<<END

<div class="materialpool-vorschlag">
    Ich möchte folgendes Material vorschlagen zur Aufnahme in den Materialpool.<br>
    <div class="materialpool-vorschlag-url">
        URL: <input type="text" id="vorschlag-url" >
    </div>
    <div class="materialpool-vorschlag-title">
        Titel des Materials<br>
       <input type="text" id="vorschlag-title"  value="$title">
    </div>
    <div class="materialpool-vorschlag-beschreibung">
        Kurzbeschreibung<br>
        <textarea id="vorschlag-beschreibung" ></textarea>
    </div>
    <br>
    <hr>
    <div class="materialpool-vorschlag-namne">
        Dein Name: <input type="text" id="vorschlag-name" value="$user">
    </div>    
    <div class="materialpool-vorschlag-email">
        Deine E-Mail: <input type="text" id="vorschlag-email"  value="$email">
    </div>
    <button class="materialpool-vorschlag-send">Vorschlagen</button>
       
    <div class="materialpool-vorschlag-hinweis">
        
    </div>
    
</div>
END;


        return $back;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function title() {
        echo Materialpool_Material::get_title();
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_title() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_titel', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function shortdescription() {
        echo Materialpool_Material::get_shortdescription();
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_shortdescription() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_kurzbeschreibung', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function description() {
        echo Materialpool_Material::get_description();
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @filters materialpool_material_description
     *
     */
    static public function get_description($trim = false) {
        global $post;
        $autor = '';
        $organisation = '';

        $description = get_metadata( 'post', $post->ID, 'material_beschreibung', true );
        $description = apply_filters( 'materialpool_material_description', $description, $post );
        if($trim){
	        $more = '... <a href="' . get_permalink( $post ) . '" class="more-link">mehr lesen</a>';
	        return wp_trim_words($description, $trim,$more);
        }

        return $description ;

    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function material_von_name() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_von_name', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function description_footer() {
        global $post;

        $user = get_user_by( 'ID', $post->post_author );
        $ts = strtotime( $post->post_date );
        if ( Materialpool_Material::material_von_name() != '' ) {
            echo "Material vorgeschlagen von " . Materialpool_Material::material_von_name() . "<br>";
        }
        echo "Im Materialpool eingetragen: " . date( 'd.m.Y', $ts) ." von " . $user->display_name;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function releasedate() {
        echo Materialpool_Material::get_releasedate();
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_releasedate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_veroeffentlichungsdatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function depublicationdate() {
        echo Materialpool_Material::get_depublicationdate();
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_depublicationdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_depublizierungsdatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function reviewdate() {
        echo Materialpool_Material::get_reviewdate();
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_reviewdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_wiedervorlagedatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function createdate() {
        echo Materialpool_Material::get_createdate();
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_createdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_erstellungsdatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function url() {
        echo Materialpool_Material::get_url();
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function url_shorten() {
        $url =  parse_url ( Materialpool_Material::get_url() );
        if ( $url !== false ) {
            echo $url[ 'host' ];
        }

    }
    /**
     *
     * @since 0.0.1
     * @access  public
     * @filters materialpool-template-material-url
     */
    static public function url_html() {
        $url = Materialpool_Material::get_url();
        echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-url', 'materialpool-template-material-url' ) .'">' . $url . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_url() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function picture() {
        echo Materialpool_Material::get_picture();
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @filters materialpool-template-material-picture
     *
     */
    static public function picture_html( $id = null ) {
        global $post;
        if ( $id == null ) $id = $post->ID;
        $pic  = Materialpool_Material::get_picture( $id );
        $data = '';
        if ( is_array( $pic ) ) {
            $url = wp_get_attachment_url( $pic[ 'ID' ] );
            $data =  '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
        }
        if ( $data == '' ) {
            $url = Materialpool_Material::get_picture_url( $id );
            if ( $url != '')  {
                $data =  '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
            }
        }
        if ( $data == '' ) {
            $url = Materialpool_Material::get_screenshot( $id );
            if ( $url != '')  {
                $data =  '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
            }
        }

        echo $data;

    }



    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_picture_source( $id = null ) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        return get_metadata( 'post', $id, 'material_cover_quelle', true );
    }
    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_picture( $id = null ) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        return get_metadata( 'post', $id, 'material_cover', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_cover_url( $id = null )
    {

        global $post;
        if ($id == null) $id = $post->ID;

        $pic = Materialpool_Material::get_picture($id);
        $url = '';

        if (is_array($pic)) {
            $url = wp_get_attachment_url($pic['ID']);
        }

        if ($url == '') {
            $url = Materialpool_Material::get_picture_url($id);
        }
        if ($url == '') {
            $url = Materialpool_Material::get_screenshot($id);
        }
        if ($url == '') {
            $url = plugin_dir_url(dirname(__DIR__)) . '/assets/dummy.jpg';
        }

        return $url;
    }
     static public function get_picture_url( $id = null ) {
        return get_metadata( 'post', $id, 'material_cover_url', true );
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_screenshot( $id = null ) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        return get_metadata( 'post', $id, 'material_screenshot', true );
    }



    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function cover_facet_html( $id = null) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        $url = '';
        $data = '';
        // Prio 1: hochgeladenes Bild
        $pic  = Materialpool_Material::get_picture( $id );
         if ( is_int( (int) $pic ) ) {
            $url = wp_get_attachment_url( $pic );
       }
        // Prio 2, Cover URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_picture_url( $id );
        }
        // Prio 3, Screenshot URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_screenshot( $id );
        }
        if ( $url != '' && is_string($url) && trim( $url)  != '' ) {
            $data = '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'alignleft materialpool-template-material-picture-facet' ) .'"/>';

        }

        return $data;
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function  get_cover( $id = null) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        $url = '';

        $pic  = Materialpool_Material::get_picture( $id );
	    if ( $pic && !is_array( $pic )) {

		    $url = wp_get_attachment_url( $pic );
	    }elseif ( is_array( $pic ) ) {
            $url = wp_get_attachment_url( $pic[ 'ID' ] );
        }

	    // Prio 2, Cover URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_picture_url( $id );
        }
        // Prio 3, Screenshot URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_screenshot( $id );
        }

        return trim($url);
    }




    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function cover_facet_html_noallign( $id = null ,$placeholderURL = null) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        $url = '';
        $data = '';
        // Prio 1: hochgeladenes Bild
        $pic  = Materialpool_Material::get_picture( $id );
        if ( $pic != '' ) {
            $url = wp_get_attachment_url( $pic );
        }
        // Prio 2, Cover URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_picture_url( $id );
        }
        // Prio 3, Screenshot URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_screenshot( $id );
        }
        if ( $url != '' && trim( $url)  != '' ) {
            if (empty($placeholderURL)){
                $data = '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture-facet' ) .'"/>';
            }
            else
            {
                $data = '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture-facet' ) .'" onerror="this.onerror = null; this.src=\"'.$placeholderURL.'\"" />';
            }
        }

        return $data;
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function werk() {
        $werkID =  Materialpool_Material::get_werk_id();
        if ( false === $werkID ) {
            return;
        }
        $werk = get_post( $werkID );
        echo $werk->post_title;
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-werk
     *
     */
    static public function werk_html() {
        $werkID =  Materialpool_Material::get_werk_id();
        if ( $werkID != '' ) {
            $werk = get_post( $werkID );
            $url = get_permalink( $werkID );
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-werk', 'materialpool-template-material-werk' ) .'">' . $werk->post_title . '</a>';
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_werk_id() {
        global $post;

        $werk = get_metadata( 'post', $post->ID, 'material_werk', true );
        if ( is_int( (int) $werk ) ) {
            return ( $werk );
        } else {
            return false;
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     * @todo
     */
    static public function is_werk() {
        global $post;
        if(is_a($post,'WP_Post')){
	        global $wpdb;
	        $result = $wpdb->get_row( $wpdb->prepare("SELECT count(pm.post_id) as count FROM $wpdb->postmeta pm, $wpdb->posts p WHERE pm.meta_key = %s and pm.meta_value = %s and pm.post_id = p.ID and p.post_status = %s", 'material_werk', $post->ID , 'publish') );
	        if ( is_object( $result)  && $result->count > 0 ) {
		        return true;
	        } else {
		        return false;
	        }
        }

    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     * @todo
     */
    static public function is_part_of_werk() {
        global $post;
	    if(is_a($post,'WP_Post')){
		    global $wpdb;
		    $result = $wpdb->get_row( $wpdb->prepare("SELECT count(post_id) as count FROM $wpdb->postmeta WHERE meta_key = %s and meta_value != '' and post_id = %s", 'material_werk', $post->ID ) );
		    if ( is_object( $result)  && $result->count > 0 ) {
			    return true;
		    } else {
			    return false;
		    }
	    }

    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     * @todo
     */
    static public function volumes() {
        global $wpdb;
        global $post;

        if ( self::is_werk() ) {
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s  and $wpdb->posts.post_status = 'publish'  order by post_title asc" , 'material_werk', $post->ID ) );
            foreach ( $result as $material ) {
                echo $material->post_title . '<br>';
            }
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     * @todo
     */
    static public function volumes_ids() {
        global $wpdb;
        global $post;

        if ( self::is_werk() ) {
            $ar = array();
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s  and $wpdb->posts.post_status = 'publish'  order by post_title asc" , 'material_werk', $post->ID ) );
            foreach ( $result as $material ) {
                $ar[] = $material->ID;
            }
            return $ar;
        }
        return false;
    }
    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-volumes
     *
     * @todo
     */
    static public function volumes_html() {
        global $wpdb;
        global $post;

        if ( self::is_werk() ) {
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s  and $wpdb->posts.post_status = 'publish'  order by post_title asc" , 'material_werk', $post->ID ) );
            foreach ( $result as $material ) {
                $url = get_permalink( $material->ID );
                echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-volumes', 'materialpool-template-material-volumes' ) .'">' . $material->post_title . '</a><br>';
            }
        }
    }

    /**
 *
 * @since 0.0.1
 * @access public
 *
 */
    static public function sibling_volumes ( $selfinclude = false ) {
        global $wpdb;
        global $post;

        if ( self::is_part_of_werk() ) {
            $werk = self::get_werk_id();
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s and $wpdb->posts.post_status = 'publish'   order by post_title asc" , 'material_werk', $werk ) );
            foreach ( $result as $material ) {
                if ( ! $selfinclude ) {
                    if ( $material->ID == $post->ID ) {
                        break;
                    }
                }
                echo $material->post_title . '<br>';
            }
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-sibling
     *
     */
    static public function sibling_volumes_html ( $selfinclude = false ) {
        global $wpdb;
        global $post;

        if ( self::is_part_of_werk() ) {
            $werk = self::get_werk_id();
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s  and $wpdb->posts.post_status = 'publish'  order by post_title asc" , 'material_werk', $werk ) );
            foreach ( $result as $material ) {
                if ( ! $selfinclude ) {
                    if ( $material->ID == $post->ID ) {
                        break;
                    }
                }
                $url = get_permalink( $material->ID );
                echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-sibling', 'materialpool-template-material-sibling' ) .'">' . $material->post_title . '</a><br>';
            }
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function verweise () {
        $verweise = Materialpool_Material::get_verweise();
        foreach ( $verweise as $verweis ) {
            echo $verweis[ 'post_title' ] . '<br>';
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function has_verweise () {
        $verweise = Materialpool_Material::get_verweise();
        $back = true;

        if(!empty($verweise)){
            foreach ($verweise as $verweis)
            {
                if (!empty($verweis))
                {
                    break;
                }
                else
                {
                    $back = false;
                }
            }
        }
        else
        {
            $back = false;
        }
        return $back;
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-verweise
     */
    static public function verweise_html () {
        $verweise = Materialpool_Material::get_verweise();
        foreach ( $verweise as $verweis ) {
            $url = get_permalink( $verweis[ 'ID' ] );
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-verweise' ) .'">' . $verweis[ 'post_title' ] . '</a><br>';

        }
    }


    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-verweise
     */
    static public function get_verweise_ids () {
        $back = array();
        $verweise = Materialpool_Material::get_verweise();
        if(is_array($verweise) && $verweise[0]){
	        foreach ( $verweise as $verweis ) {
		        $back[] = (int) $verweis;
	        }

        }
        return $back;
    }
    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_verweise() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_verweise', true );
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function availability() {
        global $post;
        $id = get_field( 'material_verfuegbarkeit', $post->ID, ARRAY_A );
        $vid = get_term( $id, 'verfuegbarkeit' ,  ARRAY_A );
        if  ( is_array( $vid)) {
            $link = add_query_arg( 'fwp_verfuegbarkeit', FWP()->helper->safe_value( $vid[ 'slug'] ), home_url(). '/facettierte-suche/' );
            echo "<a href='". $link ."'>".$vid[ 'name']."</a>";
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_availability() {
        global $post;
        $id = get_field( 'material_verfuegbarkeit', $post->ID, ARRAY_A );
        $vid = get_term( $id, 'verfuegbarkeit' ,  ARRAY_A );
        if ( is_array( $vid ) ) {
            return $vid[ 'name'];
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function inklusion() {
        echo Materialpool_Material::get_inklusion();
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_inklusion() {
        global $post;

        $id = get_field( 'material_inklusion', $post->ID, ARRAY_A );
        $vid = get_term( $id, 'inklusion' ,  ARRAY_A );
        if ( is_array( $vid ) ) {
            return $vid[ 'name'];
        }
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function lizenz() {
        global $post;
        $id = get_field( 'material_lizenz', $post->ID, ARRAY_A );
        $vid = get_term( $id, 'lizenz' ,  ARRAY_A );
        if  ( is_array( $vid)) {
                    $link = add_query_arg( 'fwp_lizenz', FWP()->helper->safe_value( $vid[ 'slug'] ), home_url(). '/facettierte-suche/' );
                    echo "<a href='". $link ."'>".$vid[ 'name']."</a>";
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_lizenz() {
        global $post;

        $id = get_field( 'material_lizenz', $post->ID, ARRAY_A );
        $vid = get_term( $id, 'lizenz' ,  ARRAY_A );
        if ( is_array( $vid ) ) {
            return $vid[ 'name'];
        }
    }
    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_werkzeuge() {
        global $post;

        $id = get_field( 'material_werkzeug', $post->ID, ARRAY_A );
        if (is_array($id))
        {
        $vid = get_term( $id[0], 'werkzeug' ,  ARRAY_A );
        if ( is_array( $vid ) ) {
            return $vid[ 'name'];
        }
        }
        return false;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function werkzeuge_html() {
        global $post;

        $id = get_field( 'material_werkzeug', $post->ID, ARRAY_A );
        if (is_array($id))
        {
        $vid = get_term( $id[0], 'werkzeug' ,  ARRAY_A );
        if ( is_array( $vid ) ) {
            echo "<a href='/" . $vid['taxonomy'] . "/" . $vid['slug'] . "'>" . $vid['name'] . "</a>";
        }
        }
    }



    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function organisation () {
        $verweise = Materialpool_Material::get_organisation();
        foreach ( $verweise as $verweisID ) {
            $verweis = get_post( $verweisID, ARRAY_A );
            echo $verweis[ 'post_title' ] . '<br>';
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-verweise
     * @filters materialpool-template-material-organisation
     */
    static public function organisation_html () {
        global $post;
        $verweise = Materialpool_Material::get_organisation();
        foreach ( $verweise as $verweisID ) {
            $verweis = get_post( $verweisID, ARRAY_A );
            $url = get_permalink( $verweis[ 'ID' ] );
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-verweise' ) .'">' . $verweis[ 'post_title' ] . '</a><br>';

        }
        $organisation = apply_filters( 'materialpool_material_description_interim_organisation', get_metadata( 'post', $post->ID, 'material_organisation_interim', true ) );
        if ( $organisation != '' ) {
            echo '<a class="'. apply_filters( 'materialpool-template-material-organisation', 'materialpool-template-material-organisation' ) .'">' . $organisation. '</a>';
        }

    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-verweise
     */
    static public function organisation_html_cover () {
        global $post;
        $verweise = Materialpool_Material::get_organisation();
        if($verweise && intval($verweise[0])>0){


            foreach ( $verweise as $verweisID ) {
		        if(!$verweisID) continue;
		        $verweis = get_post( $verweisID, ARRAY_A );
		        $url = get_permalink( $verweis[ 'ID' ] );
		        $logo = get_metadata( 'post', $verweis[ 'ID' ], 'organisation_logo_url', true );
		        echo "<div class='materialpool-template-material-organisation'>";
		        if ( $logo != '') {
			        echo '<a href="' . $url . '" style="background-image:url(\'' . $logo . '\')" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-organisation-logo' ) .'"><img src="' . $logo . '"></a>';
		        }
		        echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-organisation' ) .'">' . $verweis[ 'post_title' ] . '</a>';
		        echo "</div>";
	        }
        }

        $organisation = apply_filters( 'materialpool_material_description_interim_organisation', get_metadata( 'post', $post->ID, 'material_organisation_interim', true ) );
        if ( $organisation != '' ) {
            echo "<div class='materialpool-template-material-organisation'>";
            echo $organisation ;
            echo "</div>";
        }
    }


    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function has_organisation () {
        global $post;
        $verweise = Materialpool_Material::get_organisation(false);
        $back = true;

        if(!empty($verweise)){
            foreach ($verweise as $verweis)
            {
                if (!empty($verweis))
                {
                    break;
                }
                else
                {
                    $back = false;
                }
            }
        }
        else
        {
            $back = false;
        }
        $interim = get_metadata( 'post', $post->ID, 'material_organisation_interim', true );
        if (!empty($interim) ) {
            $back = true;
        }
        return $back;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_organisation($single = true) {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_organisation', $single );
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-organisation
     */
    static public function organisation_facet_html () {
        $organisationen = Materialpool_Material::get_organisation();
        $data = '';
        if (is_array($organisationen))
        {
            foreach ( $organisationen as $organisationID ) {
                $organisation = get_post( $organisationID, ARRAY_A );
                if ( $organisation != '' )
                    if ( $data != '') {
                        $data .= ', ';
                    }
                $data .= $organisation[ 'post_title' ];
            }
        }
        return "<span class='search-organisation'>" . $data . "</span>";
    }



    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function has_autor() {
        global $post;
        $verweise = Materialpool_Material::get_autor(false);
        $back = true;

        if(!empty($verweise)){
          foreach ($verweise as $verweis)
          {
              if (!empty($verweis))
              {
                  break;
              }
              else
              {
                  $back = false;
              }
          }
        }
        else
        {
            $back = false;
        }
        $interim = get_metadata( 'post', $post->ID, 'material_autor_interim', true );
        if (!empty($interim) ) {
            $back = true;
        }

        return $back;
    }


    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function autor () {
        $verweise = Materialpool_Material::get_autor(false);
        foreach ( $verweise as $verweis ) {
            $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
            $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
            echo $vorname. ' ' . $nachname . '<br>';
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function autor_list () {


        $verweise =  Materialpool_Material::get_autor(false);
        if(!empty($verweise)&& !is_array($verweise)){

            echo $verweise;

        }elseif (is_array($verweise)){
            foreach ( $verweise as $verweis ) {
                if ($count > 0 ) {
                    echo ", ";
                }
                if(isset($verweis[ 'ID' ])){
                    $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
                    $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
                    echo $vorname . ' ' . $nachname;
                    $count++;
                }

            }
        }


    }

    /**
 *
 * @since 0.0.1
 * @access public
 * @filters materialpool-template-material-autor
 */
    static public function autor_html () {
        self::get_autor_html();
    }

    static public function get_autor_html () {
        global $post;
        $verweise = Materialpool_Material::get_autor();
        $autoren = "";
        if (!empty($verweise)){
            foreach ( $verweise as $verweis ) {
                if (empty($verweis))
                    continue;
                $a = get_post($verweis);
                if (is_object($a))
                {
                    $autoren .= '<a href="' . $a->guid . '" class="'. apply_filters( 'materialpool-template-material-autor', 'materialpool-template-material-autor' ) .'">' . $a->post_title . '</a><br>';
                }
                /*
                $url = get_permalink( $verweis);
                $vorname = get_post_meta($verweis, 'autor_vorname', true );
                $nachname = get_post_meta($verweis, 'autor_nachname', true );
                $autoren .= '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-autor', 'materialpool-template-material-autor' ) .'">' . $vorname .' '. $nachname . '</a><br>';
                */

            }
        }
        // Output INterim Autor
        $autor = apply_filters( 'materialpool_material_description_interim_autor', get_metadata( 'post', $post->ID, 'material_autor_interim', true ) );
        if ( $autor != '' ) {
            $autoren .= $autor ;
        }
        return $autoren;
    }
    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function autor_html_picture () {
        global $post;
        $verweise = Materialpool_Material::get_autor(false);
        if(!empty($verweis)){
	        foreach ( $verweise as $verweisID ) {
                if (empty($verweisID))
                    continue;
		        $verweis = get_post( $verweisID, ARRAY_A );
		        $url = get_permalink( $verweis[ 'ID' ] );
		        $logo = get_metadata( 'post', $verweis[ 'ID' ], 'autor_bild_url', true );
		        $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
		        $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
		        if ( $logo != '') {
			        //echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'"><img  class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'" src="' . $logo . '"></a>';
			        echo '<a href="' . $url . '" style="background-image:url(\'' . $logo . '\')" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'"><img  class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'" src="' . $logo . '"></a>';
		        }
		        echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-autor', 'materialpool-template-material-autor' ) .'">' . $vorname . ' '. $nachname . '</a>';

	        }
        }


        // Output INterim Autor
        $autor = apply_filters( 'materialpool_material_description_interim_autor', get_metadata( 'post', $post->ID, 'material_autor_interim', true ) );
        if ( $autor != '' ) {
            echo $autor;
        }
    }


    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function autor_facet_html () {
        $verweise = Materialpool_Material::get_autor(false);
        $data = '';
        if (is_array($verweise))
        {
            foreach ( $verweise as $verweis ) {
                if ( $verweis != '' )
                    if ( $data != '') {
                        $data .= ', ';
                    }
                $vorname = get_post_meta($verweis, 'autor_vorname', true );
                $nachname = get_post_meta($verweis, 'autor_nachname', true );
                $data .=  $vorname .' '. $nachname;
            }
        }
        return "<span class='search-autor'>" . $data . "</span>";
    }



    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function bildungsstufe_facet_html () {
        global $post;

        $data = '';
        $term_list = wp_get_post_terms( $post->ID, 'bildungsstufe' );
        if  ( is_array( $term_list)) {
            foreach ( $term_list as $tax ) {
                if ( $tax->parent != 0 ) {
                    $link = "/facettierte-suche/?fwp_bildungsstufe=". $tax->slug;
                    $data .= '<span class="facet-tag"><a href="' . $link . '">' . $tax->name .'</a></span>';
                }
            }
        }

        return $data;
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @return  commaseparated bildungssufen
     */
    static public function get_bildungsstufen () {
        global $post;

        $data = array();
        $term_list = wp_get_post_terms( $post->ID, 'bildungsstufe' );
        if  ( is_array( $term_list)) {
            foreach ( $term_list as $tax ) {
                if ( $tax->parent != 0 ) {
	                $link = Materialpool_Material::add_preselect_filters_to_url('fwp_bildungsstufe',$tax->slug );
	                $data[] =  "<a href='$link'>{$tax->name}</a>";
                }
            }
        }
        if ( is_array( $data ) ) {
            return implode( ', ', $data );
        } else {
            return $data;
        }
    }

    static public function bildungsstufen () {
        return self::get_bildungsstufen();
    }



    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function inklusion_facet_html () {
        global $post;

        $data = '';
        $term_list = wp_get_post_terms( $post->ID, 'inklusion' );
        if  ( is_array( $term_list)) {
            foreach ( $term_list as $tax ) {
                $link = "/facettierte-suche/?fwp_inklusion=". $tax->slug;
                $data .= '<span class="facet-tag"><a href="' . $link . '">' . $tax->name .'</a></span>';
            }
        }
        return $data;
    }



    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_autor(bool $single = true)
    {
        global $post;

        $interim_autoren = get_metadata('post', $post->ID, 'material_autor_interim', $single);
        $autoren = get_metadata('post', $post->ID, 'material_autoren', $single);

        if (empty($autoren)) {
            return $interim_autoren;
        } else {
            return $autoren;
        }
    }

    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_special() {
        global $post;

        $back = false;
        $special =  get_metadata( 'post', $post->ID, 'material_special', true );

        if ( $special == '1' ) {
            $back = true;
        }
        return $back;
    }

    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_old() {
        global $post;

        $back = false;
        $old =  get_metadata( 'post', $post->ID, 'materialpool_old', true );

        if ( $old == '1' ) {
            $back = true;
        }
        return $back;
    }

    /**
     * @since 0.0.1
     * @access public
     * @return bool
     */
    static public function is_alpika() {
        global $post;
        $alpika =  get_post_meta( $post->ID, 'material_alpika_facet', true );
        return $alpika?true:false;
    }


    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_viewer() {
        global $post;

        $back = false;
        $url =  get_metadata( 'post', $post->ID, 'material_url', true );
        $ignore = get_metadata( 'post', $post->ID, 'material_no_viewer', true );
        if ( mb_endsWith($url, '.pdf' ) && $ignore != 1  ) {
            $back = true;
        }

        return $back;
    }

    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_playable() {
        global $post;

        $back = false;
        $url =  get_metadata( 'post', $post->ID, 'material_url', true );
        $ignore = get_metadata( 'post', $post->ID, 'material_no_viewer', true );

        $re = '/^(http(s)??\:\/\/)?(www\.)?((youtube\.com\/watch\?v=)|(youtu.be\/))([a-zA-Z0-9\-_])+$/';
        preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);

        if ( count ( $matches ) > 0  && $ignore != 1 )  {
            $back = true;
        }

        return $back;
    }


    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_old_material() {
        global $post;

        $back = false;
        $meta = get_metadata( 'post', $post->ID, 'materialpool_old', true );
        if ( $meta == 1 ) {
            $back = true;
        }
        return $back;
    }

    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_embed() {
        global $post;

        $back = true;

        $url =  wp_oembed_get( get_metadata( 'post', $post->ID, 'material_url', true )) ;
        $ignore = get_metadata( 'post', $post->ID, 'material_no_viewer', true );
        if ($url === false || $ignore == 1 ) {
            $back = false;
        }

        return $back;
    }

    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_download() {
        global $post;

        $back = false;
        $url =  get_metadata( 'post', $post->ID, 'material_url', true );
        if ( mb_endsWith($url, '.pdf' ) ) {
            $back = true;
        }

        return $back;
    }



    /**
     * @since 0.0.1
     * @access public
     * @return string
     */
    static public function get_schlagworte() {
        global $post;

        $data = '';
        $term_list = wp_get_post_terms( $post->ID, 'schlagwort' );
        if  ( is_array( $term_list)) {
            foreach ( $term_list as $tax ) {
                if ( $data != '') $data .= ', ';
                $data .= $tax->name;
            }
        }
        return $data;
    }

    /**
     * @since 0.0.1
     * @access public
     * @return string
     */

    static public function get_medientypen() {
        global $post;

	    $data= array();

	    $term_list = wp_get_post_terms( $post->ID, 'medientyp' );
	    if  ( is_array( $term_list)) {
		    foreach ( $term_list as $tax ) {
			    if ( $tax->parent != 0 ) {

                   $link =  Materialpool_Material::add_preselect_filters_to_url('fwp_medientyp', $tax->slug);
				    $data[] =  "<a href='$link'>{$tax->name}</a>";
			    }
		    }
	    }

	    /*$data = '';
        $term_list = wp_get_post_terms( $post->ID, 'medientyp' );
        if  ( is_array( $term_list)) {
            foreach ( $term_list as $tax ) {
                if ( $data != '') $data .= ', ';
                $data .= $tax->name;
            }
        }*/

        return implode(', ',$data);
    }

    /**
     * @since 0.0.1
     * @access public
     * prints feed item categpries
     */
    static public function the_rss_categories() {
        global $post;

        $categories = explode(',',self::get_bildungsstufen());


        foreach($categories as $category): if(!empty( $category )):?>
        <category><![CDATA[<?php echo $category;?>]]></category>
        <?php endif; endforeach;

    }

    /**
     * @since 0.0.1
     * @access public
     * prints tags inside item content
     */
    static public function the_rss_tags() {
        global $post;

        $medientypen = explode(',',self::get_medientypen());
        $schlagworte = explode(',',self::get_schlagworte());

        $tags = array_merge($schlagworte,$medientypen);

        echo '<p style="display:none">';

        foreach($tags as $tag): if(!empty( $tag )): ?>
        <a rel="tag"><?php echo $tag;?></a>
        <?php endif; endforeach;

        echo '<p>';
    }


    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function get_schlagworte_html( $url = '' ) {
        global $post;

        $data = '';
        $term_list = wp_get_post_terms( $post->ID, 'schlagwort' );
        if  ( is_array( $term_list)) {
            foreach ( $term_list as $tax ) {
                $link = Materialpool_Material::add_preselect_filters_to_url('fwp_schlagworte', $tax->slug );
                if ( $data != '' ) $data .= ', ';
                $data .= '<a href="' . $link . '">' . $tax->name .'</a>';
            }
        }

        return $data;
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function jahr_html() {
        $jahr = Materialpool_Material::get_jahr();
        $data = '';
        if ( $jahr != '' && $jahr != 0 && $jahr != '0'  ) {
            $data = '<span class="facet-tag">' . $jahr . '</span>';
        }
        return $data;
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_jahr() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_jahr', true );
    }


    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_mediatyps_root() {
        global $post;

        $arr = array();
        $typs = wp_get_object_terms( $post->ID, 'medientyp' );
        foreach ( $typs as $term ) {
            if ( $term->parent == 0 ) {
                $icon =   get_metadata( 'term', $term->term_id, 'medientyp_icon', true );
                $farbe =   get_metadata( 'term', $term->term_id, 'medientyp_farbe', true );
                $arr[] = array(
                  'name' => $term->name,
                  'icon' => $icon,
                  'farbe' => $farbe
                );
            }
        }
        return $arr;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function rating_facet_html() {
        global $post;
        if (function_exists( 'the_ratings_results' )) {
            return '<span class="facet-rating">' . the_ratings_results( $post->ID )  . '</span>';
        }

    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function cta_link() {
        global $post;

        if ( self::is_download() ) {
            $text = "Material herunterladen";
        } else {
            $text = "Zum Material";
        }
         return "<a class='cta-button' href=\"".  self::get_url() ."\">".$text."</a>";
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function cta_url2clipboard() {
        global $post;

        $back = "<a class='cta-button copyurl'>URL in Zwischenablage</a> <script> jQuery(document).ready(function(){ var clipboard = new Clipboard('.copyurl', { text: function() { return '". self::get_url() ."'; } }); clipboard.on('success', function(e) { console.log(e);}); clipboard.on('error', function(e) { console.log(e); });}); </script>";
        return $back;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @param $id
     * @return string
     */
    static public function get_themengruppentitel( $id ) {
        global $wpdb;
        $query_str      = $wpdb->prepare('SELECT *  FROM `' . $wpdb->prefix . 'pods_themenseitengruppen`          
                                         WHERE id = %s ', $id );
        $items_arr      = $wpdb->get_results( $query_str , ARRAY_A );
        return $items_arr[ 0 ][ 'gruppe' ];
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @param $id
     * @return string
     */
    static public function get_themengruppenbeschreibung( $id ) {
        global $wpdb;
        $query_str      = $wpdb->prepare('SELECT *  FROM `' . $wpdb->prefix . 'pods_themenseitengruppen`          
                                         WHERE id = %s ', $id );
        $items_arr      = $wpdb->get_results( $query_str , ARRAY_A );
        return $items_arr[ 0 ][ 'titel_der_gruppe' ];
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @param $id
     * @return array
     */
    static public function get_themengruppe( $id ) {
        global $wpdb;
        $query_str      = $wpdb->prepare('SELECT *  FROM `' . $wpdb->prefix . 'pods_themenseitengruppen`          
                                         WHERE id = %s ', $id );
        $items_arr      = $wpdb->get_results( $query_str , ARRAY_A );
        return $items_arr[ 0 ];
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @param $id
     * @return string
     */
    static public function get_thema( $id ) {
        $thema = get_post( $id);
        return $thema->post_title;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @param $id
     * @return string
     */
    static public function cb_themenseite_checked( $id ) {
        $thema = self::get_themengruppe( $id);
        $auswahlArr = explode( ',', $thema[ 'auswahl'] );
        $back ='';
        if ( in_array( get_the_ID(), $auswahlArr ) ) {
            $back = "checked='checked' class='uncheck_themenseite' ";
        } else {
            $back = " class='check_themenseite' ";
        }
        return $back;
    }



    /**
     *
     * @since 0.0.1
     * @access  public
     * @return string
     *
     */
    static public function cb_themenseite() {
        if (defined('REST_REQUEST') && REST_REQUEST) {
            $thema = (int) $_POST[ 'mp_thema'];
            $gruppe = (int) $_POST[ 'mp_gruppe'];

        } else {
            $thema = (int) $_GET[ 'thema'];
            $gruppe = (int) $_GET[ 'gruppe'];
        }
        $back = '';
        if ( $thema == 0 || $gruppe == 0 ) {
            return $back;
        }
        $gruppenname = self::get_themengruppentitel ($gruppe);
        $themaname = self::get_thema( $thema );
        if ( is_user_logged_in() ) {  // @todo Rolle abfragen welche nötig ist dafür
            $back .= '<div class="material-themenseiten-auswahl">';
            $back .= "<input type='checkbox' " . self::cb_themenseite_checked( $gruppe ) ;
            $back .= " data-thema='". $thema ."' data-gruppe='". $gruppe ."' data-post='". get_the_ID() ."'";
            $back .= ">";
            $back .= "Material der Themenseite '". $themaname ."', Gruppe '" . $gruppenname . "' zuordnen.";
            $back .= '</div>';
        }

        return $back;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @return string
     *
     */
    static public function write_javascript() {
        ?>

        <script>
        jQuery(document).ready(function(){
        jQuery(".pods-form-ui-field-name-pods-meta-material-bildungsstufe").click( function() {
            switch ( jQuery(this).val() ) {
<?php
        $terms = get_terms( array(
            'taxonomy' => 'bildungsstufe',
        ) );
        foreach ( $terms as $term ) {
            if ( $term->parent != 0 ) {
                ?>
                case "<?php echo $term->term_id; ?>":    // Elementarbereich
                set_bildungsstufe( <?php echo $term->parent; ?> );
                break;
            <?php
            }
        }
        ?>

            }
        });
        });
        function set_bildungsstufe( id ) {
            jQuery(".pods-form-ui-field-name-pods-meta-material-bildungsstufe").each( function() {
                if ( jQuery(this).val() == id ) {
                    jQuery(this).attr('checked', true);
                }
            })
        };

        jQuery(document).ready(function(){
            jQuery(".pods-form-ui-field-name-pods-meta-material-medientyp").click( function() {
                switch ( jQuery(this).val() ) {
                <?php
                    $terms = get_terms( array(
                        'taxonomy' => 'medientyp',
                    ) );
                    foreach ( $terms as $term ) {
                    if ( $term->parent != 0 ) {
                    ?>
                    case "<?php echo $term->term_id; ?>":
                        set_medientyp( <?php echo $term->parent; ?> );
                        break;
                <?php
                    }
                    }
                    ?>

                }
            });
        });
        function set_medientyp( id ) {
            jQuery(".pods-form-ui-field-name-pods-meta-material-medientyp").each( function() {
                if ( jQuery(this).val() == id ) {
                    jQuery(this).attr('checked', true);
                }
            })
        };

        </script>
        <?php
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @return string
     *
     */
    static public function review_count() {
        global $wpdb;

        $result = $wpdb->get_var( $wpdb->prepare( "SELECT count( $wpdb->posts.ID) as anzahl  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value != '0000-00-00' order by post_title asc" , 'material_wiedervorlagedatum' ) );
        return $result;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @return string
     *
     */
    static public function depublication_count() {
        global $wpdb;

        $result = $wpdb->get_var( $wpdb->prepare( "SELECT count( $wpdb->posts.ID) as anzahl  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value != '0000-00-00' order by post_title asc" , 'material_depublizierungsdatum' ) );
        return $result;

    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function depublizierung() {
        global $wpdb;

        $result = $wpdb->get_results( $wpdb->prepare( "SELECT  $wpdb->posts.ID FROM $wpdb->posts, $wpdb->postmeta WHERE ( $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value <= CURRENT_DATE   AND $wpdb->postmeta.meta_value != '0000-00-00' AND  $wpdb->postmeta.meta_value != '' ) " , 'material_depublizierungsdatum' ) );
        remove_action( 'wp_trash_post', array( 'Materialpool_Material', 'before_trashed_post' ) );
        if ( is_array( $result ) ) {
            foreach ( $result as $obj ) {
                wp_trash_post(  $obj->ID );
	            // ggf Abhängige Themenseiten aus dem RocketCache entfernen
	            $themen = Materialpool_Material::get_themenseiten_for_material( $obj->ID );
	            if ( is_array( $themen ) &&  sizeof( $themen ) > 0 ) {
		            foreach ( $themen as $item ) {
			            if (  function_exists( 'rocket_clean_post' ) ) {
				            rocket_clean_post( $item->id );
			            }
		            }
	            }

	            // Für den Fall, das auf der Startseite Materialien aufgelistet werden, den Cache der Startseite ungültig machen.
	            if (  function_exists( 'rocket_clean_post' ) ) {
		            // Startseite ermittln und invalid machen.
		            $frontpage_id = get_option( 'page_on_front' );
		            rocket_clean_post( $frontpage_id );
	            }
	            if ( class_exists( 'FWP_Cache') ) {
		            FWP_Cache()->cleanup();
	            }
	            if ( is_object( FWP() ) ) {
		            FWP()->indexer->save_post( $obj->ID );
	            }

            }
        } else {
            if ( ! is_wp_error( $result ) ) {
                wp_trash_post(  $result );
	            // ggf Abhängige Themenseiten aus dem RocketCache entfernen
	            $themen = Materialpool_Material::get_themenseiten_for_material( $result );
	            if ( is_array( $themen ) &&  sizeof( $themen ) > 0 ) {
		            foreach ( $themen as $item ) {
			            if (  function_exists( 'rocket_clean_post' ) ) {
				            rocket_clean_post( $item->id );
			            }
		            }
	            }

	            // Für den Fall, das auf der Startseite Materialien aufgelistet werden, den Cache der Startseite ungültig machen.
	            if (  function_exists( 'rocket_clean_post' ) ) {
		            // Startseite ermittln und invalid machen.
		            $frontpage_id = get_option( 'page_on_front' );
		            rocket_clean_post( $frontpage_id );
	            }
	            if ( class_exists( 'FWP_Cache') ) {
		            FWP_Cache()->cleanup();
	            }
	            if ( is_object( FWP() ) ) {
		            FWP()->indexer->save_post( $obj->ID );
	            }
            }
        }

    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function set_createdate( $postID = null ) {
        global $post;

        if ( $postID == null ) {
            $postID = $post->ID;
        }
        $date = get_post_meta( $postID, 'create_date', true );
        if ( $date == '' ) {
            add_post_meta( $postID, 'create_date', date( 'Y-m-d' ), true );
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     * @return  int
     */
    static public function submit_count() {
        global $wpdb;

        $query_str      = $wpdb->prepare('SELECT count(id) as anzahl   FROM `' . $wpdb->posts . '`  
                                         WHERE post_status = %s ', "vorschlag" );
        $items_arr      = $wpdb->get_results( $query_str , ARRAY_A );
        return $items_arr[ 0 ][ 'anzahl' ];

    }


    static public function add_open_graph() {
        global $post;
        if ( !is_object( $post ) ) return;
        if ( 'material' != $post->post_type ) {
            return;
        }
        $autorn = Materialpool_Material::get_autor(false);
        if(!is_array($autorn)) {
	        $autorn = [$autorn];
        }

	    foreach ( $autorn as $k => $autor ) {
	        $a = get_post($autor);
		    $autorn[ $k ] = strip_tags( $a->post_title );
	    }

        $description = Materialpool_Material::get_description();
        if ( $description != '' ) {
            $description = strip_tags( $description );
        }
        ?>
        <meta name="keywords" content="<?php echo  strip_tags( Materialpool_Material::get_schlagworte() ) ; ?>">
        <meta name="description" content="<?php echo  $description ; ?>">
        <meta name="author" content="<?php echo  implode(',',$autorn) ; ?>">
        <meta property="og:title" content="<?php Materialpool_Material::title(); ?>" />
        <meta property="og:type" content="article" />
        <meta property="og:image" content="<?php echo Materialpool_Material::get_cover(); ?>" />
        <meta property="og:url" content="<?php echo get_permalink(); ?>" />
        <meta property="og:description" content="<?php echo  $description ; ?>" />
        <meta property="og:site_name" content="rpi-virtuell Materialpool" />

        <?php
    }

    static public function admin_posts_filter( $query ) {
        global $pagenow, $wpdb;


        if ( is_admin() && $pagenow=='edit.php' && isset($_GET['ORGA_FILTER_FIELD_NAME']) && $_GET['ORGA_FILTER_FIELD_NAME'] != '') {
            $query->query_vars['meta_key'] = 'material_organisation';
            $query->query_vars['meta_value'] = $_GET['ORGA_FILTER_FIELD_NAME'];
        }
        if ( is_admin() && $pagenow=='edit.php' && isset($_GET['AUTOR_FILTER_FIELD_NAME']) && $_GET['AUTOR_FILTER_FIELD_NAME'] != '') {
            $query->query_vars['meta_key'] = 'material_autoren';
            $query->query_vars['meta_value'] = $_GET['AUTOR_FILTER_FIELD_NAME'];
        }

        if ( is_admin() && $pagenow=='edit.php' && isset( $_REQUEST[ 'mode'] ) &&  $_REQUEST[ 'mode'] == 'incomplete' )  {
            $result = $wpdb->get_results("
SELECT distinct( $wpdb->posts.ID ) , $wpdb->posts.post_title, DATE_FORMAT( post_date, '%d.%m.%y' ) AS datum  FROM 
    $wpdb->posts, $wpdb->postmeta 
WHERE 
    $wpdb->posts.ID = $wpdb->postmeta.post_id AND  
    $wpdb->posts.post_type = 'material' AND
    ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' )  AND
(
    ( 
    (
       not exists( select * from wp_postmeta where meta_key='material_schlagworte' and post_id = wp_posts.ID )
     OR  
        ( 
            wp_postmeta.meta_key = 'material_schlagworte' AND 
            wp_postmeta.meta_value = ''  
        )
        ) 
    )
OR
    ( 
        $wpdb->postmeta.meta_key = 'material_url' AND 
            $wpdb->postmeta.meta_value = ''  
    )
OR
    ( 
        $wpdb->postmeta.meta_key = 'material_beschreibung' AND 
            $wpdb->postmeta.meta_value = ''  
    )
OR
    ( 
        $wpdb->postmeta.meta_key = 'material_kurzbeschreibung' AND 
            $wpdb->postmeta.meta_value = ''  
    )
OR  
    ( 
       not exists( select * from $wpdb->postmeta where meta_key='_pods_material_medientyp' and post_id = $wpdb->posts.ID )
     OR  
        ( 
            $wpdb->postmeta.meta_key = '_pods_material_medientyp' AND 
            $wpdb->postmeta.meta_value = ''  
        )
    )
    OR  
    ( 
       not exists( select * from $wpdb->postmeta where meta_key='material_bildungsstufe' and post_id = $wpdb->posts.ID )
     OR  
        ( 
            $wpdb->postmeta.meta_key = 'material_bildungsstufe' AND 
            $wpdb->postmeta.meta_value = ''  
        )
    )
)   
order by wp_posts.post_date  asc ") ;

            $idlist = array();
            foreach ( $result as $obj ) {
                $idlist[] = $obj->ID ;
            }
            $query->query_vars['post__in'] = $idlist;
        }
        if ( is_admin() && $pagenow=='edit.php' && isset( $_REQUEST[ 'mode'] ) &&  $_REQUEST[ 'mode'] == 'supply' )  {
                $result = $wpdb->get_results("
        SELECT distinct( $wpdb->posts.ID ) , $wpdb->posts.post_title, DATE_FORMAT( post_date, '%d.%m.%y' ) AS datum  FROM 
    $wpdb->posts, $wpdb->postmeta 
WHERE 
    $wpdb->posts.ID = $wpdb->postmeta.post_id AND  
    $wpdb->posts.post_type = 'material' AND
    ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' )  AND
(
    (
       not exists( select * from wp_postmeta where meta_key='material_vorauswahl' and post_id = wp_posts.ID )
     OR  
        ( 
            wp_postmeta.meta_key = 'material_vorauswahl' AND 
            wp_postmeta.meta_value != 2206  
        ) 
    )
)   
order by wp_posts.post_date  asc ") ;


            $idlist = array();
            foreach ( $result as $obj ) {
                $idlist[] = $obj->ID ;
            }
            $query->query_vars['post__in'] = $idlist;
        }


        return $query;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function cleanup_themenseiten(  $material_id, $post, $update ) {
        global $wpdb;
        $post_type = get_post_type($material_id);
        if ( "material" != $post_type ) {
            return;
        }
        if ( $material_id == 0 ) {
            return;
        }
        if ( $post->post_status == 'trash' && $update == true ) {
            // Dazugehörige Themenseiten ermittln um nach dem entfernen des Materials aus den Themengruppen den Cache der Themenseite zu verwerfen.
            $themen = Materialpool_Material::get_themenseiten_for_material( $material_id );

            $tablename = $wpdb->prefix . "pods_themenseitengruppen";
            $query     = "select  id   from $tablename  where  auswahl like '%,{$material_id},%' or auswahl like  '%,{$material_id}' ;";
            $result = $wpdb->get_results( $query );
            if ( is_array( $result ) && sizeof( $result ) > 0 ) {
                foreach ( $result as $item ) {
                    $id      = $item->id;
                    $query2  = "select  auswahl   from $tablename  where id = {$id};";
                    $result2 = $wpdb->get_var( $query2 );
                    $arr2    = explode( ',', $result2 );
                    if (($key = array_search($material_id, $arr2)) !== false) {
                        unset($arr2[$key]);
                    }
                    $string = implode( ',', $arr2 );
                    $query3 = "update $tablename  set auswahl = '$string'  where id = {$id};";
                    $wpdb->get_results( $query3 );
                }
            }
            // Seitencache der dazugehörigen Themenseiten verwerfen
            if ( is_array( $themen ) &&  sizeof( $themen ) > 0 ) {
                foreach ( $themen as $item ) {
                    if (  function_exists( 'rocket_clean_post' ) ) {
                        rocket_clean_post( $item->id );
                    }
                }
            }
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_themenseiten_for_material( $material_id = 0 ) {
        global $post;

        $material_id = ($material_id>0)?$material_id:$post->ID;

        $themenseiten = get_field('material_themenseiten',$material_id);

        $result = [];
        foreach ($themenseiten as $themenseite){

            $thema = get_post($themenseite['single_themenseite']);
            if(is_a($thema, 'WP_Post')){
                $thema->id = $thema->ID;
                $result[] = $thema;
            }
        }

        return $result;

    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function get_themenseiten_for_material_html( $material_id  = 0) {
        $result = Materialpool_Material::get_themenseiten_for_material( $material_id);
        if ( is_array( $result ) &&  sizeof( $result ) > 0 ) {
            echo "Dieses Material ist Teil folgender Themenseiten:<br>";
            foreach ( $result as $item ) {
                $url = get_permalink( $item->id );
                echo "<a href='" . $url  ."'>".  $item->post_title . "</a><br>";

            }
            echo "<br>";
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function back_to_search() {
        if ( $_GET[ 'sq' ] ) {
            $sq = $_GET[ 'sq' ];
            ?>
            <a class='cta-button' href="<?php echo urldecode( $sq ); ?>">Zurück zur Materialsuche</a><br>
            <?php
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function  rss_query_vars( $query_vars ) {
        $query_vars[] = 'rss_organisation';
        $query_vars[] = 'rss_per_page';
        return $query_vars;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function rss_pre_get_posts( $query ) {
        if( $query->is_feed && $query->is_main_query() && $query->query[ 'post_type' ] == 'material' ) {
            if( isset( $query->query_vars[ 'rss_organisation' ] ) && ! empty( $query->query_vars[ 'rss_organisation' ] ) ) {
                if ( $post = get_page_by_path( $query->query_vars[ 'rss_organisation' ] , OBJECT, 'organisation' ) ) {
                    $id = $post->ID;
                } else {
                    $id = $query->query_vars['rss_organisation'];
                }
                $query->set( 'meta_key', 'material_organisation' );
                $query->set( 'meta_value', $id );
            }
            if( isset( $query->query_vars[ 'rss_per_page' ] ) && ! empty( $query->query_vars[ 'rss_per_page' ] ) ) {
                $query->set( 'posts_per_rss', (int) $query->query_vars[ 'rss_per_page' ] );
            }

        }
        //return $query;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    static public function default_hide_meta_box( $hidden, $screen ) {
        if ( ('post' == $screen->base) && ('material' == $screen->id) ){
            $hidden[] = 'pods-meta-zusaetzliche-metadaten';
            $hidden[] = 'trackbacksdiv';
            $hidden[] = 'commentstatusdiv';
            $hidden[] = 'commentsdiv';
            $hidden[] = 'authordiv';
        }
        return $hidden;
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    public static function options_page() {
        add_submenu_page(
            'materialpool',
            _x('Material (Unvollständig)', Materialpool::$textdomain, 'Page Title' ),
            _x('Material (Unvollständig)', Materialpool::$textdomain, 'Menu Title' ),
            'manage_options',
            __FILE__ . '1',
            array( 'Materialpool_Material', 'list_unvollstaendig' )
        );

        add_submenu_page(
            'materialpool',
            _x('Material (Zugeliefert)', Materialpool::$textdomain, 'Page Title' ),
            _x('Material (Zugeliefert)', Materialpool::$textdomain, 'Menu Title' ),
            'manage_options',
            __FILE__ . '2',
            array( 'Materialpool_Material', 'list_zugeliefert' )
        );


	    $submenu_pages = array(
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Altersstufen',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=altersstufe',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Bildungsstufen',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=bildungsstufe',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Inklusionen',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=inklusion',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Kompetenzen',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=kompetenz',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Konfessionen',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=konfession',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Lizenzen',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=lizenz',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Medientypen',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=medientyp',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Rubriken',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=rubrik',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Schlagworte',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=schlagwort',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Sprachen',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=sprache',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Verfuegbarkeit',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=verfuegbarkeit',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Vorauswahlen',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=vorauswahl',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Werkzeuge',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=werkzeug',
			    'function'    => null,// Doesn't need a callback function.
		    ),
		    array(
			    'parent_slug' => 'materialpool',
			    'page_title'  => '',
			    'menu_title'  => 'Zugänglichkeiten',
			    'capability'  => 'manage_options',
			    'menu_slug'   => 'edit-tags.php?taxonomy=zugaenglichkeit',
			    'function'    => null,// Doesn't need a callback function.
		    ),
	    );

	    foreach ( $submenu_pages as $submenu ) {

		    add_submenu_page(
			    $submenu['parent_slug'],
			    $submenu['page_title'],
			    $submenu['menu_title'],
			    $submenu['capability'],
			    $submenu['menu_slug'],
			    $submenu['function']
		    );

	    }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    public static function list_unvollstaendig() {
        global $wpdb;
        $count = 0;
        $result = $wpdb->get_results("
        SELECT distinct( $wpdb->posts.ID ) , $wpdb->posts.post_title, DATE_FORMAT( post_date, '%d.%m.%y' ) AS datum  FROM 
    $wpdb->posts, $wpdb->postmeta 
WHERE 
    $wpdb->posts.ID = $wpdb->postmeta.post_id AND  
    $wpdb->posts.post_type = 'material' AND
    ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' )  AND
(
    ( 
    (
       not exists( select * from wp_postmeta where meta_key='material_schlagworte' and post_id = wp_posts.ID )
     OR  
        ( 
            wp_postmeta.meta_key = 'material_schlagworte' AND 
            wp_postmeta.meta_value = ''  
        )
        ) 
    )
OR
    ( 
        $wpdb->postmeta.meta_key = 'material_url' AND 
            $wpdb->postmeta.meta_value = ''  
    )
OR
    ( 
        $wpdb->postmeta.meta_key = 'material_beschreibung' AND 
            $wpdb->postmeta.meta_value = ''  
    )
OR
    ( 
        $wpdb->postmeta.meta_key = 'material_kurzbeschreibung' AND 
            $wpdb->postmeta.meta_value = ''  
    )
OR  
    ( 
       not exists( select * from $wpdb->postmeta where meta_key='material_medientyp' and post_id = $wpdb->posts.ID )
     OR  
        ( 
            $wpdb->postmeta.meta_key = 'material_medientyp' AND 
            $wpdb->postmeta.meta_value = ''  
        )
    )
    OR  
    ( 
       not exists( select * from $wpdb->postmeta where meta_key='material_bildungsstufe' and post_id = $wpdb->posts.ID )
     OR  
        ( 
            $wpdb->postmeta.meta_key = 'material_bildungsstufe' AND 
            $wpdb->postmeta.meta_value = ''  
        )
    )
)   
order by wp_posts.post_date  asc ") ;
        foreach ( $result as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Material</th><th style='width: 20%;' >Datum</th></tr>";
            }
            echo "<tr><td><a href='". get_edit_post_link( $obj->ID) ."'>" . $obj->post_title . "</a></td><td>" . $obj->datum ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }

    /**
     *
     * @since 0.0.1
     * @access  public
     *
     */
    public static function list_zugeliefert() {
        global $wpdb;
        $count = 0;
        $result = $wpdb->get_results("
        SELECT distinct( $wpdb->posts.ID ) , $wpdb->posts.post_title, DATE_FORMAT( post_date, '%d.%m.%y' ) AS datum  FROM 
    $wpdb->posts, $wpdb->postmeta 
WHERE 
    $wpdb->posts.ID = $wpdb->postmeta.post_id AND
    $wpdb->posts.post_type = 'material' AND
    ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' )  AND
    (
    (
    not exists( select * from wp_postmeta where meta_key='material_vorauswahl' and post_id = wp_posts.ID )
     OR  
        ((
            wp_postmeta.meta_key = 'material_vorauswahl' AND
            wp_postmeta.meta_value != 'a:1:{i:0;s:4:\"2206\";}'
        )
        AND 
        (
            wp_postmeta.meta_key = 'material_vorauswahl' AND
            wp_postmeta.meta_value != 2206
        )
        )
    )
)   
order by wp_posts.post_date  desc  ") ;

        foreach ( $result as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Material</th><th style='width: 20%;' >Datum</th></tr>";
            }
            echo "<tr><td><a href='". get_edit_post_link( $obj->ID) ."'>" . $obj->post_title . "</a></td><td>" . $obj->datum ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }

	/**
	 *
	 * @since 0.0.1
	 * @access  public
	 *
	 */
	public static function before_delete_post( $post_id ) {
	    if ( Materialpool_Material::is_werk() ) {
		    wp_redirect(admin_url('edit.php?post_status=trash&post_type=material&msg=' . urlencode( 'Dieses Material kann nicht gelöscht werden, da es ein Werk ist und auf Bände verweist. Um das Material zu löschen, bitte erst die Band-Verweise entfernen.')));
		    exit();
	    }
	    return;
	}
	/**
	 *
	 * @since 0.0.1
	 * @access  public
	 *
	 */
	public static function before_trashed_post( $post_id ) {
		if ( Materialpool_Material::is_werk() ) {

			wp_redirect(admin_url('edit.php?post_status=published&post_type=material&msg=' . urlencode( 'Dieses Material kann nicht gelöscht werden, da es ein Werk ist und auf Bände verweist. Um das Material zu löschen, bitte erst die Band-Verweise entfernen.')));
			exit();
		}

	}

	/**
	 * @param $meta_id
	 * @param $post_id
	 * @param $meta_key
	 * @param $meta_value
     *
     * If material_url is repaired after setting status to broken, the status will set to publish
	 */
	public static function after_post_meta($meta_id, $post_id, $meta_key, $meta_value ){
	    if($meta_key == 'material_url'){
	        $material = get_post($post_id);
	        if($material->post_status == 'broken'){
		        $material->post_status ='publish';
		        wp_update_post($material);
	        }
	    }
	}

	/**
	 *
	 * @since 0.0.1
	 * @access  public
	 *
	 */
	public static function admin_notices() {
		$class = 'notice notice-error';
		if ( isset($_GET['msg'] ) ) {
			$message =  urldecode( $_GET[ 'msg' ] );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}

	}
	/**
	 * mark_broken_links withs custum post status broken Link
	 */
	static function check_material_url($url, $post_id, $log = false){

	    global $wpdb;

		echo '<li><strong>'.$url.'</strong></li>';

		$request_args = array(
		        'headers' => array(),
				'timeout'           =>  20,
				'sslverify'         =>  false,
				'redirection'       =>  10,
				'user-agent'        =>  'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
				'cookies'           =>  array( 'SSESS16815fd5c783ac7fb5ee431b6360307b'=>'qMAv3LfiPndvEDlDrlf9Nwtmjbi8dPuWXsABpUSI-Yw')
		);

		$request = wp_safe_remote_head(trim($url),$request_args);

		if ( is_wp_error( $request ) || intval($request["response"]["code"])  < 200 || intval($request["response"]["code"]) > 308 ) {

			if(strpos($url,'https:') === false){
				$url = str_replace('http:','https:',$url);

				$request = wp_safe_remote_head(trim($url),$request_args);

				if(is_wp_error( $request )){
					if($log) echo '<li>ERROR : '.$request->get_error_message().' | <a href="'.$url.'">'.$url.'</a></li>';
					if($log) file_put_contents('/tmp/debug_dev.log', ''.$url.'|'.$request->get_error_message()."\n",FILE_APPEND);
					update_post_meta($post_id, 'material_url_error',$request->get_error_message());
					update_post_meta($post_id, 'material_url_code','504');


				}elseif( intval($request["response"]["code"])  < 200 || intval($request["response"]["code"]) > 308){
					if($log) echo '<li>'.$request["response"]["code"].': <a href="'.$url.'">' . $url . '</a></li>';
					update_post_meta($post_id, 'material_url_code',$request["response"]["code"]);
				}else{
					if(isset($request["http_response"]->get_response_object()->history[0])){
						$correct_url = $request["http_response"]->get_response_object()->history[0]->url;
						if(strlen($correct_url)>10 && $correct_url != $url) {
							update_post_meta( $post_id, 'material_url', $correct_url );
							if($log) echo '<li>UPDATE: <a href="'.$correct_url.'">' . $correct_url . '</a></li>';
						}
					}
					return;
				}

				$sql = "UPDATE {$wpdb->posts} SET post_status = 'broken' where ID = %d";
				$query = $wpdb->prepare($sql,$post_id);
				$wpdb->query($query);

			}else{
				if(is_wp_error( $request )){
					if($log) echo '<li>ERROR : '.$request->get_error_message().' | <a href="'.$url.'">'.$url.'</a></li>';
					if($log) file_put_contents('/tmp/debug_dev.log', ''.$url.'|'.$request->get_error_message()."\n",FILE_APPEND);
					update_post_meta($post_id, 'material_url_error',$request->get_error_message());

				}else{
					if($log) echo '<li>'.$request["response"]["code"].': <a href="'.$url.'">' . $url . '</a></li>';
					update_post_meta($post_id, 'material_url_code',$request["response"]["code"]);
				}

				$sql = "UPDATE {$wpdb->posts} SET post_status = 'broken' where ID = %d";
				$query = $wpdb->prepare($sql,$post_id);
				$wpdb->query($query);
			}

		}else{
			//check redirected links
            if(isset($request["http_response"]->get_response_object()->history[0])){
	            $correct_url = $request["http_response"]->get_response_object()->history[0]->url;
	            if(strlen($correct_url)>10 && $correct_url != $url) {
		            update_post_meta( $post_id, 'material_url', $correct_url );
		            if($log) echo '<li>UPDATE: <a href="'.$correct_url.'">' . $correct_url . '</a></li>';
	            }
            }

			$sql = "UPDATE {$wpdb->posts} SET post_status = 'publish'  where post_status = 'broken'  AND ID = %d";
			$query = $wpdb->prepare($sql,$post_id);
			$wpdb->query($query);
            delete_post_meta($post_id,'material_url_error');
            delete_post_meta($post_id,'material_url_code');

		}
	}

	static function mark_broken_links(){

		//file_put_contents('/tmp/debug_dev.log', '');
		$limit = 10;

		set_time_limit(120);

		$offset = isset($_GET['N'])?intval($_GET['N']):0;

		$ms = self::get_material_urls($limit, $offset);

		if(count($ms)<1){
			return;
		}

		//echo '<pre>';
		foreach ($ms as $m){

		    echo '<li><strong>'.$m->url.'</strong></li>';// ob_flush();

			Materialpool_Material::check_material_url($m->url,$m->post_id, true);
			sleep( 1);

		}
		if(isset($_GET['N'])){?>
            <script>
                location.href='https://material.rpi-virtuell.de/test/?N=<?php echo $offset+$limit;?>';
            </script><?php
		}
		die();
	}


	static function cron_check_broken_links($offset = 0){

		if($offset === 0){
		    file_put_contents('/tmp/debug_dev.log','');
	    }

		$limit = 10;

		set_time_limit(300);

		$ms = self::get_material_urls($limit, $offset);

		if(count($ms)<1){
			return;
		}

		//echo '<pre>';
		foreach ($ms as $m){

			Materialpool_Material::check_material_url($m->url,$m->post_id);
			file_put_contents('/tmp/debug_dev.log', "$offset | {$m->post_id}: {$m->url}\n", FILE_APPEND);

		}
		sleep(2);
		self::cron_check_broken_links($offset+$limit);

	}

	static function get_material_urls($limit =10, $offset=0){

		global $wpdb;
		$sql = "select meta.post_id,meta.meta_value url from wp_postmeta meta
                inner JOIN {$wpdb->postmeta} AS spec ON meta.post_id =spec.post_id 
                    and spec.meta_key='material_special' and spec.meta_value = 0
                inner JOIN {$wpdb->posts} AS p ON p.ID = meta.post_id 
                    and post_type='material' and (post_status = 'publish' or post_status = 'broken')
                where meta.meta_key='material_url' 
                    and p.ID NOT IN (
                        select post_id from wp_postmeta meta 
                            where meta_key='material_url_notbroken' and meta_value = 1
                    ) LIMIT $offset,$limit";

		$ms = $wpdb->get_results($sql);

		return $ms;
	}

	/**
	 * @param $atts
     *
     * Shortcode [broken_links type="server_error"]

	 */
    public static function display_broken_link_errors($atts){
	    // "foo = {$atts['foo']}";

        if($atts['type']=='server_error'){
	        global $wpdb;
	        $sql = "select p.ID post_id, m.meta_value url, e.meta_value error  from {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} AS m on p.ID = m.post_id and m.meta_key = 'material_url' 
            INNER JOIN {$wpdb->postmeta} AS e on p.ID = e.post_id and ((e.meta_key = 'material_url_code' and e.meta_value > 499)  OR e.meta_key = 'material_url_error') 
            where post_status = 'broken'";
        }
	    $ms = $wpdb->get_results($sql);
        foreach ($ms as $r){
            $html[] = sprintf('<li><a href="%1$s">%1$s</a> [%2$s] <a class="ui-button button" href="/wp-admin/post.php?action=edit&post=%3$s">Bearbeiten</a></li>', $r->url,$r->error,$r->post_id);
        }
        return implode('', $html);
    }

	/*** end custum post status Broken Link*/



	public static function redirect_materialpool_url(){

	    global $post;

		if(is_single() && $post->post_type == 'material'){
	        if (isset($_GET['direct'])){
	            wp_redirect($_GET['direct']);
	            die();
	        }
	    }

	}


    /**
     * @param string $add_filter
     * @param $add_filter_value
     * @return string
     */
    public static function add_preselect_filters_to_url(string $add_filter = '', $add_filter_value = ''): string
    {
        $filter_options = [
            'fwp_bildungsstufe' => ''
            //TODO: ADD additional preselects here
        ];
        foreach ($filter_options as $filter_option => $value) {
            if (isset($_GET[$filter_option])) {
                $filter_options[$filter_option] = $_GET[$filter_option];
            }
            else{
                unset($filter_options[$filter_option]);
            }
        }
        if (!empty($add_filter) && !empty($add_filter_value)) {
            if (!empty($filter_options[$add_filter]) && !str_contains($filter_options[$add_filter],$add_filter_value) ) {
                $filter_options[$add_filter] .= '%2C' .$add_filter_value ;
            } else {
                $filter_options[$add_filter] = $add_filter_value;

            }
        }

        return add_query_arg($filter_options, home_url() . '/facettierte-suche/');
    }

    /**
     * triggered from Material save action
     * save meta values to post_type themenseite from acf repeaterfield  material_themenseiten
     *
     * @param $post_id
     * @return void
     */
    static function save_material_to_themenseiten($post_id){

        $themenseiten = get_field('material_themenseiten',$post_id);

        foreach ($themenseiten as $themenseite){


            $t_id   = $themenseite['single_themenseite'];
            $group  = $themenseite['single_themengruppe'];


            $themenseite_gruppen = get_field('themengruppen', $t_id);
            foreach ($themenseite_gruppen as $k=>$grp){


                if($grp['gruppe_von_materialien'] === $group){

                    $mats = $grp['material_in_dieser_gruppe'];

                    if(in_array($post_id,$mats)) {
                        //allready exists
                    }else{

                        array_push($mats, $post_id);

                        $post_meta_key = implode('_',['themengruppen', $k, 'material_in_dieser_gruppe']);

                        update_post_meta($t_id,$post_meta_key,$mats);

                    }
                }

            }
        }

    }

}



