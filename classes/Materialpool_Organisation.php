<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo cpt auflistung anpassen
 */


class Materialpool_Organisation {

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 * @filters materialpool_organisation_posttype_label
	 * @filters materialpool_organisation_posttype_args
	 *
	 */
	static public function register_post_type() {
		$labels = array(
			"name" => __( 'Organisationen', Materialpool::$textdomain ),
			"singular_name" => __( 'Organisation', Materialpool::$textdomain ),
		);

		$args = array(
			"label" => __( 'Organisationen', Materialpool::$textdomain ),
			"labels" => apply_filters( 'materialpool_organisation_posttype_label', $labels ),
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => "organisation",
			"show_in_menu" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "organisation", "with_front" => true ),
			"query_var" => true,
			"supports" => array( "title" ),
		);
		register_post_type( "organisation", apply_filters( 'materialpool_organisation_posttype_args', $args ) );

	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
     * @filters materialpool_organisation_meta_field
	 *
	 */
	static public function register_meta_fields() {
		$cmb_organisation = new_cmb2_box( array(
			'id'            => 'cmb_organisation',
			'title'         => __( 'Organisationdata', Materialpool::get_textdomain() ),
			'object_types'  => array( 'organisation' ),
			'context'       => 'normal',
			'priority'      => 'core',
			'show_names'    => true,
			'cmb_styles' => true,
			'closed'     => false,
		) );

		$cmb_organisation->add_field( array(
			'name'    => _x( 'URL', 'Organisation Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'Website of organisation', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'organisation_url',
			'type'    => 'text_url',
		) );


        $cmb_organisation->add_field( array(
            'name'    => _x( 'Logo URL', 'Organisation Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'URL of organisation logo', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
            'default' => '',
            'id'      => 'organisation_logo_url',
            'type'    => 'text_url',
        ) );

		$cmb_organisation->add_field( array(
			'name'    => _x( 'ALPIKA', 'Organisation Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'Organisation is ALPIKA', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'organisation_alpika',
			'type'    => 'checkbox',
		) );

		$cmb_organisation->add_field( array(
			'name'     => _x( 'Konfession', 'Organisation Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'     => _x( 'Konfession of organisation', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'id'       => 'organisation_konfession_select',
			'taxonomy' => 'konfession',
			'type'     => 'taxonomy_select',
		) );

        $cmb_organisation->add_field( array(
            'name' => 'Autor',
            'desc' => 'Hier fehlt die zuordnung zu Autoren',
            'type' => 'title',
            'id'   => 'organisation_autor'
        ) );

        $cmb_organisation->add_field( array(
            'name' => 'Material',
            'desc' => 'Hier fehlt die Auflistung von Materialien des Autors',
            'type' => 'title',
            'id'   => 'organisation_material'
        ) );

        $cmb_organisation = apply_filters( 'materialpool_organisation_meta_field', $cmb_organisation);
	}

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function load_template($template) {
        global $post;
	    if ( !is_object( $post ) ) return $template;
        if ($post->post_type == "organisation" && !is_embed() ){
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-organisation.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-organisation.php';
                }
            }
            if ( is_archive() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/archive-organisation.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/archive-organisation.php';
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
    static public function remove_from_bulk_actions( $actions ) {
        unset( $actions[ 'edit' ] );
        return $actions;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_template_check_external_files ( $checkArray ) {
        $checkArray[ 'materialpool/single-organisation.php' ] = Materialpool::$plugin_base_dir . 'templates/single-organisation.php';
        $checkArray[ 'materialpool/archive-organisation.php'] = Materialpool::$plugin_base_dir . 'templates/archive-organisation.php';
        return $checkArray;
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
        unset( $columns );
        $columns = array(
            'organisation-id' => _x( 'ID', 'Organisation list field',  Materialpool::$textdomain ),
            'organisation_title' => _x( 'Organisation', 'Organisation list field',  Materialpool::$textdomain ),
            'organisation_logo_url' => _x( 'Logo', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_views' => _x( 'Views', 'Organisation list field', Materialpool::$textdomain ),
            'material_views' => _x( 'MaterialViews', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_url' => _x( 'URL', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_email'        => _x( 'Email', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_nachricht'    => _x( 'Emailbenachrichtigung', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_konfession' => _x( 'Konfession', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_alpika' => _x( 'ALPIKA', 'Organisation list field', Materialpool::$textdomain ),
            'date' => __('Date'),
            'organisation_autoren' => _x( '#Autoren', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_material' => _x( '#Material', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_owner' => _x( 'Eintrager', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_einverstaendnis' => _x( 'Einverständnis', 'Organisation list field', Materialpool::$textdomain ),
        );
        return $columns;
    }

    /**
     * Add content for the custom columns in list table
     *
     * @since   0.0.1
     * @access	public
     * @var     string  $column_name    name of the current column
     * @var     int     $post_id        ID of the current post
     * @filters materialpool-admin-organisation-pic-class
     */
    static public function cpt_list_column( $column_name, $post_id ) {
        global $wpdb;

        $data = '';

        if ( $column_name == 'organisation_title' ) {
	        $data = Materialpool_Organisation::get_title();
        }
        if ( $column_name == 'organisation-id' ) {
		    $data = $post_id;
	    }
	    if ( $column_name == 'organisation_einverstaendnis' ) {
		    $einverstaendnis = get_metadata( 'post', $post_id, 'einverstaendnis', true );
		    if ( $einverstaendnis == 1 ) {
			    $check = " checked=checked ";
		    } else  {
			    $check = '';
		    }

		    $data = "<div><input data-id=\"". $post_id ."\" class=\"einverstaendnis_organisation\" type='checkbox' $check ></div>";
		    if ( $einverstaendnis == 2 ) {
		    	$data = '<div><i class="fas fa-times"></i> Nein</div>';		    }
	    }
        if ( $column_name == 'organisation_logo_url' ) {
            $url = get_metadata( 'post', $post_id, 'organisation_logo_url', true );
            if ( $url !== false ) {
                $data = "<img src='". $url ."' class='". apply_filters( 'materialpool-admin-organisation-pic-class', 'materialpool-admin-organisation-pic' ) ."'>";
            }
        }
        if ( $column_name == 'organisation_url' ) {
            $data = get_metadata( 'post', $post_id, 'organisation_url', true );
        }

	    if ( $column_name == 'organisation_email' ) {
		    $data = get_metadata( 'post', $post_id, 'organisation_email', true );
	    }
	    if ( $column_name == 'organisation_nachricht' ) {
		    $data = "<div id='organisation_nachricht-". $post_id ."'>";
		    $email = get_metadata( 'post', $post_id, 'organisation_email', true );
		    if ( $email == '' ) {
			    $data .= '<div style="color: red;">Keine Email hinterlegt</div>';
		    } else {
			    $send = get_metadata( 'post', $post_id, 'organisation_email_send', true );
			    $read = get_metadata( 'post', $post_id, 'organisation_email_read', true );

			    if ( $send == '' ) {
				    $data .= '<div>Nicht versendet</div>';
				    $data .= '<div class="row-actions"><span class="edit"><a style="cursor: pointer;" data-id="'. $post_id .'" class="mail_organisation_send">Mail versenden</a></span></div>';
			    }
			    if ( $send != '' && $read == '' ) {
				    $data .= '<div style="color: blue;">Versendet, ungelesen</div>';
			    }
			    if ( $send != '' && $read != '' ) {
				    $data .= '<div style="color: green;">Gelesen</div>';
			    }
		    }
		    $data .= "</div>";
	    }


        if ( $column_name == 'organisation_alpika' ) {
            $alpiika = get_metadata( 'post', $post_id, 'organisation_alpika', true );
            if ( $alpiika == '1' ) {
                $data = "<img src='". Materialpool::$plugin_url ."/assets/alpika.png'>";
            }
        }
        if ( $column_name == 'organisation_konfession' ) {
	        $konfessionen = get_metadata( 'post', $post_id, 'organisation_konfession' );
	        if ( is_array( $konfessionen )) {
		        foreach ( $konfessionen as $konfession ) {
			        $term = get_term( $konfession, "konfession" );
			        $data .=  $term->name. '<br>';
		        }
	        }
        }
        if ( $column_name == 'organisation_autoren' ) {
	        $autors = get_metadata( 'post', $post_id, 'material_autoren' );
	        $data = sizeof( $autors[0] );
        }
        if ( $column_name == 'organisation_material' ) {
	        $autors = get_metadata( 'post', $post_id, 'material_organisation' );
	        $data = sizeof( $autors[0] );
        }
        if ( $column_name == 'organisation_owner' ) {
            $post = get_post( $post_id);
            $user = get_user_by( 'ID', $post->post_author );
            $data = $user->display_name;
        }
        if ( $column_name == 'organisation_views' ) {
            $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
            $query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->mp_stats} WHERE object = %d",
                $post_id
            );
            $results = $wpdb->get_var(  $query );

            $data = $results;
        }
        if ( $column_name == 'material_views' ) {
            $wpdb->mp_stats_organisation = $wpdb->prefix . 'mp_stats_organisation';
            $query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->mp_stats_organisation} WHERE object = %d",
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
     * @access	public
     * @param   array   $columns    array with the default sortable columns
     * @return  array   Array with sortable columns
     */
    static public function cpt_sort_column( $columns ) {
        return array_merge( $columns, array(
            'organisation_url' => 'organisation_url',
            'organisation_alpika' => 'organisation_alpika',
            'organisation_konfession' => 'organisation_konfession',
        ) );
    }


	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function generate_title( $post_id ) {
        global $wpdb;
		$post_type = get_post_type($post_id);
        $post_status = get_post_status ($post_id);
        $post_parent = wp_get_post_parent_id( $post_id );


        if ( "organisation" != $post_type ) return;

		$title = $_POST[ 'pods_meta_organisation_titel' ];

        $wpdb->update(
            $wpdb->posts,
            array(
                'post_title' => $title,
                'post_name' => wp_unique_post_slug( sanitize_title( $title ), $post_id, 'publish', $post_type, $post_parent ),
            ),
            array( 'ID' => $post_id ),
            array(
                '%s',
                '%s'
            ),
            array( '%d' )
        );
        $_POST[ 'post_title'] = $title;

        // Posts suchen die mit dieser Organisation verbunden sind und dort den Organisationenamen neu speichern
        $materialien = $wpdb->get_col( $wpdb->prepare( "SELECT post_id   FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_organisation', $post_id ) );

        foreach ( $materialien as $material_id ) {
	        delete_transient( 'facet_serach2_entry-'.$material_id );
	        delete_transient( 'facet_organisation_entry-'.$material_id );
	        delete_transient( 'rss_material_entry-'.$material_id );
	        	        
            delete_post_meta( $material_id, 'material_organisation_facet' );
            $organisationen = get_metadata( 'post', $material_id, 'material_organisation', false );
            if ( is_array( $organisationen ) ) {
                foreach ( $organisationen as $key => $val ) {
                    $organisationen_ids[] = (int) $val[ 'ID' ];
                }
            } else {
                $organisationen_ids[] = (int) $organisationen;
            }
            foreach ( $organisationen_ids as $organisationen_id ) {
                $organisationen_meta = get_post( $organisationen_id );
                if ( $organisationen_meta->ID == $post_id ) {
                    $orga_title = $title;
                } else {
                    $orga_title = $organisationen_meta->post_title;
                }
                add_post_meta( $material_id, 'material_organisation_facet', $orga_title );
            }
            if ( is_object( FWP() ) ) {
                FWP()->indexer->save_post( $material_id );
            }
	        if ( class_exists( 'FWP_Cache') ) {
		        FWP_Cache()->cleanup();
	        }

            unset ($organisationen_ids );
        }


		// Organisationen suchen die mit diesem Autoren verbunden sind
		$autorn = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value   FROM  $wpdb->postmeta WHERE meta_key = %s and post_id = %s", 'organisation_autor', $post_id ) );

		delete_post_meta( $post_id, 'organisation_autor_facet' );
		foreach ( $autorn as $autor_id ) {
			$autor_meta = get_post( $autor_id );
			$autor_title = $autor_meta->post_title;
			add_post_meta( $post_id, 'organisation_autor_facet', $autor_title );
		}

		delete_post_meta( $post_id, 'organisation_alpika_facet' );
		clean_post_cache( $post_id );

		if( $_POST[ 'pods_meta_organisation_alpika' ] == 1 ){
			add_post_meta( $post_id, 'organisation_alpika_facet', 1 );
		}

		// Konfession der Organisation in term_rel speichern

		wp_delete_object_term_relationships( $post_id, 'konfession' );
		$cats = $_POST[ 'pods_meta_organisation_konfession' ];
		if ( is_array( $cats ) ) {
			foreach ( $cats as $key => $val ) {
				$cat_ids[] = (int) $val;
			}
		}
		if ( !is_array( $cats ) ) {
			$cat_ids[] = (int) $cats;
		}
		wp_set_object_terms( $post_id, $cat_ids, 'konfession', true );



		if ( is_object( FWP() ) ) {
			FWP()->indexer->save_post( $post_id );
		}


	}


	/**
	 * @param $post_id
	 * @access	public
	 *
	 */
	static public function send_mail( $post_id = false ) {

		if ( $post_id === false ) return false;


		// generate Mail
		$sendmail = get_option( 'einstellungen_organisationsmail_aktiv', 0 );
		$email    = get_metadata( 'post', $post_id, 'organisation_email', true );
		if ( $sendmail == 1 && $email != '' ) {
			$send = get_metadata( 'post', $post_id, 'organisation_email_send', true );
			if ( $send == '' ) {
				$subject = get_option( 'einstellungen_organisation_mail_subject', false );
				$content = get_option( 'einstellungen_organisationsmail_content', false );
				if ( $subject && $content ) {
					$content = str_replace( '%material_autor_name%', Materialpool_Autor::get_firstname($post_id) . ' ' . Materialpool_Autor::get_lastname($post_id), $content );
					$content = str_replace( '%materialpool_home%', get_option( 'siteurl' ), $content );
					$content = str_replace( '%material_autor_url%', Materialpool_Autor::autor_check_url($post_id), $content );
					$content = str_replace( '%material_organisation_url%', Materialpool_Organisation::organistion_check_url($post_id), $content );
					$content = str_replace( '%material_last_material%', Materialpool_Autor::last_material_name($post_id), $content );
					$content = str_replace( '%redakteur_name%', Materialpool_Autor::redaktuer_name($post_id), $content );
					$content = str_replace( '%redakteur_reply_email%', 'redaktion@rpi-virtuell.de', $content ); //  Materialpool_Autor::redakteur_email() , $content );

					$headers[] = 'From: Redaktion rpi-virtuell <redaktion@rpi-virtuell.de>';
					$headers[] = 'Reply-To: Redaktion rpi-virtuell <redaktion@rpi-virtuell.de>';
					$headers[] = 'bcc: material@rpi-virtuell.de';
					$mail      = wp_mail( $email, $subject, $content, $headers );
					if ( $mail ) {
						$send = add_metadata( 'post', $post_id, 'organisation_email_send', time() );
					}
				}
			}
		}
	}


/**
	 *
	 * @since 0.0.1
	 * @access	public
	 * @return  string
	 *
	 */
	static public function organistion_check_url($id = 0 ) {
		global $post;
		$id = ($id>0)?$id:$post->ID;
		$hash = get_metadata( 'post', $id, 'organisation_hash', true );
		if ( $hash == '') {
			$hash = wp_hash( 'organisation_hash' . time(). $id ) ;
			add_metadata('post', $id, 'organisation_hash', $hash );
		}

		return get_option( 'siteurl' ) . '/check_organisation/'.$hash ;
	}

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function title() {
        echo Materialpool_Organisation::get_title();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_title() {
        global $post;

        return stripslashes( $post->post_title );
    }


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function title_long() {
        echo Materialpool_Organisation::get_title_long();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_title_long() {
        global $post;

        return stripslashes( get_metadata( 'post', $post->ID, 'organisation_titel_lang', true ) );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function url() {
        echo Materialpool_Organisation::get_url();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-organisation-url
     */
    static public function url_html() {
        $url = Materialpool_Organisation::get_url();
        echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-organisation-url', 'materialpool-template-organisation-url' ) .'">' . $url . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_url() {
        global $post;

        return get_metadata( 'post', $post->ID, 'organisation_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function logo() {
        echo Materialpool_Organisation::get_logo();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-organisation-logo
     *
     */
    static public function logo_html() {
        $url = Materialpool_Organisation::get_logo();
        echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-organisation-logo', 'materialpool-template-organisation-logo' ) .'"/>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_logo() {
        global $post;

        return get_metadata( 'post', $post->ID, 'organisation_logo_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function is_alpika() {
        global $post;

        $alpika = get_metadata( 'post', $post->ID, 'organisation_alpika', true );
        if ( $alpika == '1' ) {
            return true;
        }
        return false;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function konfession() {
        echo Materialpool_Organisation::get_konfession();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_konfession() {
        global $post;

        $konfession = get_metadata( 'post', $post->ID, 'organisation_konfession', true );
        if ( is_array( $konfession ) ) {
            return $konfession[ 'name'];
        }
    }


    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-organisation-material
     */
    static public function material_html () {
        $verweise = Materialpool_Organisation::get_material();
        foreach ( $verweise as $verweis ) {
            $url = get_permalink( $verweis[ 'ID' ] );
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-organisation-material', 'materialpool-template-organisation-material' ) .'">' . $verweis[ 'post_title' ] . '</a><br>';

        }
    }



    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_materialien_html( $max = false) {
        $back = '';
        $verweise = Materialpool_Organisation::get_material();
        $count = 0;
        foreach ( $verweise as $material ) {
            if ( $max != false && $count >= $max ) {
                break;
            }
            $url = get_permalink( $material[ 'ID' ] );
            $back .=  '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-volumes', 'materialpool-template-material-volumes' ) .'">' . $material[ 'post_title' ] . '</a><br>';
            $count++;
        }
        return $back;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_material() {
        global $post;

        return get_metadata( 'post', $post->ID, 'organisation_material', false );
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function autor_html_picture () {
        $verweise = Materialpool_Organisation::get_autor();
        foreach ( $verweise[0] as $verweis ) {
            $url = get_permalink( $verweis );
            $post = get_post( $verweis );
            $logo = get_metadata( 'post', $verweis, 'autor_bild_url', true );
            $vorname = get_metadata( 'post', $verweis, 'autor_vorname', true );
            $nachname = get_metadata( 'post', $verweis, 'autor_nachname', true );

            echo "<div class='materialpool-template-autor-organisation'>";
                if ( $logo != '') {
                    echo '<a href="' . $url . '" style="background-image:url(\'' . $logo . '\')" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'"><img  class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'" src="' . $logo . '"></a>';
                }
                echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-autor', 'materialpool-template-material-autor' ) .'">' . $vorname . ' ' . $nachname .  '</a>';

            echo "</div>";
        }
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_autor() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_autoren', false );
    }


	/**
	 *
	 * @since 0.0.1
	 * @access public
	 * @return number of materials from the current Autor
	 */
	static public function get_count_posts_per_organisation ($autor_id = 0) {
		global $post,$wpdb;

		$autor_id = ($autor_id>0)?$autor_id:$post->ID;
		$query = "select count(pod_id) from {$wpdb->prefix}podsrel where item_id = %d and field_id in ( select ID from wp_posts where post_type ='_pods_field' and post_name='organisation_material')" ;
		$query = $wpdb->prepare($query, $autor_id);
		$count = $wpdb->get_var($query);
		return $count;
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function top_orga() {


		$top_orgaID =  Materialpool_Organisation::get_top_orga_id();
		if ( false === $top_orgaID ) {
			return;
		}
		$top_orga = get_post( $top_orgaID );
		echo $top_orga->post_title;
	}

	/**
	 *
	 * @since 0.0.1
	 * @access public
	 * @filters materialpool-template-material-werk
	 *
	 */
	static public function top_orga_html( $orga_id = 0 ) {
		global $post;

		$top_orgaID = ($orga_id>0)?$orga_id:Materialpool_Organisation::get_top_orga_id( $orga_id );

		if ( $top_orgaID != '' ) {
			$top_orga = get_post( $top_orgaID );
			$url = get_permalink( $top_orgaID );
			echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-organisation-top', 'materialpool-template-organisation-top' ) .'">' . $top_orga->post_title . '</a>';
		}
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function get_top_orga_id( $orga_id = 0 ) {
		global $post;

		$orga_id = ($orga_id>0)?$orga_id:$post->ID;
		$top_orga = get_metadata( 'post', $orga_id, 'top_organisation', true );
		if ( is_array( $top_orga ) ) {
			return ( $top_orga[ "ID" ] );
		} else {
			return false;
		}
	}

	/**
	 *
	 * @since 0.0.1
	 * @access public
	 * @filters materialpool-template-material-verweise
	 */
	static public function bottom_orga_html () {
		$verweise = Materialpool_Organisation::get_bottom_orga();
		foreach ( $verweise as $verweis ) {
			$url = get_permalink( $verweis[ 'ID' ] );
			echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-organisation-bottom', 'materialpool-template-organisation-bottom' ) .'">' . $verweis[ 'post_title' ] . '</a><br>';

		}
	}


	/**
	 *
	 * @since 0.0.1
	 * @access public
	 * @filters materialpool-template-material-verweise
	 */
	static public function get_bottom_orga_ids () {
		$back = array();
		$verweise = Materialpool_Organisation::get_bottom_orga();
		foreach ( $verweise as $verweis ) {
			$back[] = (int) $verweis[ 'ID' ];
		}
		return $back;
	}
	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function get_bottom_orga() {
		global $post;

		return get_metadata( 'post', $post->ID, 'bottom_organisationen', false );
	}
}
