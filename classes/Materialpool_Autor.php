<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo post_title beim Speichern aus VornameNachname generieren
 */


class Materialpool_Autor {

	/**
	 *
	 * @since 0.0.1
	 * @access    public
	 * @filters materialpool_autor_posttype_label
	 * @filters materialpool_autor_posttype_args
	 *
	 */
	static public function register_post_type() {
		$labels = array(
			"name"          => __( 'Autoren', Materialpool::$textdomain ),
			"singular_name" => __( 'Autor', 'twentyfourteen' ),
		);

		$args = array(
			"label"               => __( 'Autoren', Materialpool::$textdomain ),
			"labels"              => apply_filters( 'materialpool_autor_posttype_label', $labels ),
			"description"         => "",
			"public"              => true,
			"publicly_queryable"  => true,
			"show_ui"             => true,
			"show_in_rest"        => false,
			"rest_base"           => "",
			"has_archive"         => 'autor',
			"show_in_menu"        => true, //'materialpool',
			"exclude_from_search" => false,
			"capability_type"     => "post",
			"map_meta_cap"        => true,
			"hierarchical"        => false,
			"rewrite"             => array( "slug" => "autor", "with_front" => true ),
			"query_var"           => true,
			"supports"            => false,
		);
		register_post_type( "autor", apply_filters( 'materialpool_autor_posttype_args', $args ) );

	}

	/**
	 *
	 * @since 0.0.1
	 * @access    public
	 * @filters materialpool_autor_meta_field
	 *
	 */
	static public function register_meta_fields() {
		$cmb_author = new_cmb2_box( array(
			'id'           => 'cmb_autor',
			'title'        => __( 'Autor', Materialpool::get_textdomain() ),
			'object_types' => array( 'autor' ),
			'context'      => 'normal',
			'priority'     => 'core',
			'show_names'   => true,
			'cmb_styles'   => true,
			'closed'       => false,
		) );


		$cmb_author->add_field( array(
			'name'    => _x( 'Firstname', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'firstname of author', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'autor_firstname',
			'type'    => 'text',
		) );

		$cmb_author->add_field( array(
			'name'    => _x( 'lastname', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'lastname of author', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'autor_lastname',
			'type'    => 'text',
		) );

		$cmb_author->add_field( array(
			'name'    => _x( 'URL', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'Website of author', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'autor_url',
			'type'    => 'text_url',
		) );

		$cmb_author->add_field( array(
			'name'    => _x( 'RPI BuddyPress Username', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'username of author on rpi buddypress', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'autor_buddypress',
			'type'    => 'text',
		) );

		$cmb_author->add_field( array(
			'name'    => _x( 'Email', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'email of author (for gravatar)', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'autor_email',
			'type'    => 'text_email',
		) );

		$cmb_author->add_field( array(
			'name'    => _x( 'Picture URL', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'URL of author picture', 'Author Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'autor_picture_url',
			'type'    => 'text_url',
		) );

		$cmb_author->add_field( array(
			'name' => 'Organisaion',
			'desc' => 'Hier fehlt die zuordnung zu Organisationen',
			'type' => 'title',
			'id'   => 'autor_orga'
		) );

		$cmb_author->add_field( array(
			'name' => 'Material',
			'desc' => 'Hier fehlt die Auflistung von Materialien des Autors',
			'type' => 'title',
			'id'   => 'autor_material'
		) );

		$cmb_author = apply_filters( 'materialpool_autor_meta_field', $cmb_author );
	}

	/**
	 *
	 * @since 0.0.1
	 * @access    public
	 */
	static public function load_template( $template ) {
		global $post;

		if ( $post->post_type == "autor" && ! is_embed() ) {
			if ( is_single() ) {
				if ( $theme_file = locate_template( array( 'materialpool/single-autor.php' ) ) ) {
					$template_path = $theme_file;
				} else {
					$template_path = Materialpool::$plugin_base_dir . 'templates/single-autor.php';
				}
			}
			if ( is_archive() ) {
				if ( $theme_file = locate_template( array( 'materialpool/archive-autor.php' ) ) ) {
					$template_path = $theme_file;
				} else {
					$template_path = Materialpool::$plugin_base_dir . 'templates/archive-autor.php';
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
	 * @access    public
	 *
	 */
	static public function add_template_check_external_files( $checkArray ) {
		$checkArray['materialpool/single-autor.php']  = Materialpool::$plugin_base_dir . 'templates/single-autor.php';
		$checkArray['materialpool/archive-autor.php'] = Materialpool::$plugin_base_dir . 'templates/archive-autor.php';

		return $checkArray;
	}

	/**
	 * Change the columns for list table
	 *
	 * @since   0.0.1
	 * @access    public
	 * @var     array $columns Array with columns
	 * @return  array
	 */
	static public function cpt_list_head( $columns ) {
		unset( $columns );
		$columns = array(
			'autor-id'           => _x( 'ID', 'Autor list field', Materialpool::$textdomain ),
			'autor_bild_url'     => _x( 'Picture', 'Autor list field', Materialpool::$textdomain ),
			'autor_nachname'     => _x( 'Lastname', 'Autor list field', Materialpool::$textdomain ),
			'autor_vorname'      => _x( 'Firstname', 'Autor list field', Materialpool::$textdomain ),
			'autor_views'        => _x( 'Views', 'Autor list field', Materialpool::$textdomain ),
			'material_views'     => _x( 'MaterialViews', 'Autor list field', Materialpool::$textdomain ),
			'autor_buddypress'   => _x( 'BuddyPress', 'Autor list field', Materialpool::$textdomain ),
			'autor_email'        => _x( 'Email', 'Autor list field', Materialpool::$textdomain ),
			'autor_nachricht'    => _x( 'Emailbenachrichtigung', 'Autor list field', Materialpool::$textdomain ),
			'date'               => __( 'Date' ),
			'autor_organisation' => _x( 'Organisationen', 'Autor list field', Materialpool::$textdomain ),
			'autor_material'     => _x( '#Material', 'Autor list field', Materialpool::$textdomain ),
			'autor_owner'        => _x( 'Eintrager', 'Autor list field', Materialpool::$textdomain ),
			'autor_einverstaendnis' => _x( 'Einverständnis', 'Autor list field', Materialpool::$textdomain ),
		);

		return $columns;
	}

	/**
	 * Add content for the custom columns in list table
	 *
	 * @since   0.0.1
	 * @access    public
	 * @var     string $column_name name of the current column
	 * @var     int $post_id ID of the current post
	 * @filters materialpool-admin-autor-pic-class
	 */
	static public function cpt_list_column( $column_name, $post_id ) {
		global $wpdb;

		$data = '';
		if ( $column_name == 'autor-id' ) {
			$data = $post_id;
		}
		if ( $column_name == 'autor_bild_url' ) {
			$url = get_metadata( 'post', $post_id, 'autor_bild_url', true );
			if ( $url !== false ) {
				$data = "<img src='" . $url . "' class='" . apply_filters( 'materialpool-admin-autor-pic-class', 'materialpool-admin-autor-pic' ) . "'>";
			}
		}
		if ( $column_name == 'autor_vorname' ) {
			$data = get_metadata( 'post', $post_id, 'autor_vorname', true );
		}
		if ( $column_name == 'autor_nachname' ) {
			$data = get_metadata( 'post', $post_id, 'autor_nachname', true );
		}
		if ( $column_name == 'autor_buddypress' ) {
			$data = get_metadata( 'post', $post_id, 'autor_buddypress', true );
		}
		if ( $column_name == 'autor_email' ) {
			$data = get_metadata( 'post', $post_id, 'autor_email', true );
		}
		if ( $column_name == 'autor_nachricht' ) {
			$data = "<div id='autor_nachricht-". $post_id ."'>";
			$email = get_metadata( 'post', $post_id, 'autor_email', true );
			if ( $email == '' ) {
				$data .= '<div style="color: red;">Keine Email hinterlegt</div>';
			} else {
				$send = get_metadata( 'post', $post_id, 'autor_email_send', true );
				$read = get_metadata( 'post', $post_id, 'autor_email_read', true );

				if ( $send == '' ) {
					$data .= '<div>Nicht versendet</div>';
					$data .= '<div class="row-actions"><span class="edit"><a  style="cursor: pointer;" data-id="'. $post_id .'" class="mail_autor_send">Mail versenden</a></span></div>';
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

		if ( $column_name == 'autor_organisation' ) {
			$organisationen = get_metadata( 'post', $post_id, 'material_organisation' );
			if ( is_array( $organisationen[0] )) {
				foreach ( $organisationen[0] as $organisation ) {
					$post = get_post( $organisation );
					$data .= '<a href="/wp-admin/edit.php?post_type=material&all_posts=1&ORGA_FILTER_FIELD_NAME=' .  $post->ID . '">' . $post->post_title .'</a><br>';

				}
			}
		}
		if ( $column_name == 'autor_einverstaendnis' ) {
			$einverstaendnis = get_metadata( 'post', $post_id, 'einverstaendnis', true );
            if ( $einverstaendnis == 1 ) {
                $check = " checked=checked ";
            } else  {
                $check = '';
            }

            $data = "<div><input data-id=\"". $post_id ."\" class=\"einverstaendnis_autor\" type='checkbox' $check ></div>";
		}
		if ( $column_name == 'autor_material' ) {
			$autors = get_metadata( 'post', $post_id, 'material_autoren' );
			$data = sizeof( $autors[0] );
		}
		if ( $column_name == 'autor_owner' ) {
			$post = get_post( $post_id );
			$user = get_user_by( 'ID', $post->post_author );
			$data = $user->display_name;
		}
		if ( $column_name == 'autor_views' ) {
			$wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
			$query          = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->mp_stats} WHERE object = %d",
				$post_id
			);
			$results        = $wpdb->get_var( $query );

			$data = $results;
		}
		if ( $column_name == 'material_views' ) {
			$wpdb->mp_stats_autor = $wpdb->prefix . 'mp_stats_autor';
			$query                = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->mp_stats_autor} WHERE object = %d",
				$post_id
			);
			$results              = $wpdb->get_var( $query );

			$data = $results;
		}

		echo $data;
	}

	/**
	 *
	 * @since 0.0.1
	 * @access    public
	 *
	 */
	static public function remove_from_bulk_actions( $actions ) {
		unset( $actions['edit'] );

		return $actions;
	}

	/**
	 * Set the sortable columns
	 *
	 * @since   0.0.1
	 * @access    public
	 *
	 * @param   array $columns array with the default sortable columns
	 *
	 * @return  array   Array with sortable columns
	 */
	static public function cpt_sort_column( $columns ) {
		return array_merge( $columns, array(
			'autor_nachname'   => 'autor_nachname',
			'autor_vorname'    => 'autor_vorname',
			'autor_buddypress' => 'autor_buddypress',
			'autor_email'      => 'autor_email',
		) );
	}

	/**
	 *
	 * @since 0.0.1
	 * @access    public
	 *
	 */
	static public function generate_title( $post_id ) {
		global $wpdb;

		$post_type   = get_post_type( $post_id );
		$post_status = get_post_status( $post_id );
		$post_parent = wp_get_post_parent_id( $post_id );

		if ( "autor" != $post_type ) {
			return;
		}
		$anmerkung = '';
		$firstname = $_POST['pods_meta_autor_vorname'];
		$lastname  = $_POST['pods_meta_autor_nachname'];
		$anmerkung = $_POST['pods_meta_autor_uniq'];
		if ( $anmerkung != '' ) {
			$anmerkung = ' - ' . $anmerkung;
		}
		$intern_name = $firstname . ' ' . $lastname . $anmerkung;
		$name        = $firstname . ' ' . $lastname;

		$wpdb->update(
			$wpdb->posts,
			array(
				'post_title' => $intern_name,
				'post_name'  => wp_unique_post_slug( sanitize_title( $name ), $post_id, 'publish', $post_type, $post_parent ),
			),
			array( 'ID' => $post_id ),
			array(
				'%s',
				'%s'
			),
			array( '%d' )
		);
		$_POST['post_title'] = $name;

		// Posts suchen die mit diesem Autoren verbunden sind und dort den Autorennamen neu speichern
		$materialien = $wpdb->get_col( $wpdb->prepare( "SELECT post_id   FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_autoren', $post_id ) );

		foreach ( $materialien as $material_id ) {
			delete_post_meta( $material_id, 'material_autor_facet' );
			$autoren = get_metadata( 'post', $material_id, 'material_autoren', false );
			if ( is_array( $autoren ) ) {
				foreach ( $autoren as $key => $val ) {
					$autoren_ids[] = (int) $val['ID'];
				}
			} else {
				$autoren_ids[] = (int) $autoren;
			}
			foreach ( $autoren_ids as $autoren_id ) {
				$autoren_meta = get_post( $autoren_id );
				if ( $autoren_meta->ID == $post_id ) {
					$autoren_title = $name;
				} else {
					$autoren_title = $autoren_meta->post_title;
				}
				add_post_meta( $material_id, 'material_autor_facet', $autoren_title );
			}
			if ( is_object( FWP() ) ) {
				FWP()->indexer->save_post( $material_id );
			}
			unset ( $autoren_ids );
		}

		// Organisationen suchen die mit diesem Autoren verbunden sind
		$organisationen = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value   FROM  $wpdb->postmeta WHERE meta_key = %s and post_id = %s", 'autor_organisation', $post_id ) );

		delete_post_meta( $post_id, 'autor_organisation_facet' );
		foreach ( $organisationen as $organisationen_id ) {
			$organisation_meta  = get_post( $organisationen_id );
			$organisation_title = $organisation_meta->post_title;
			add_post_meta( $post_id, 'autor_organisation_facet', $organisation_title );
		}
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
		$sendmail = get_option( 'einstellungen_autorenmail_aktiv', 0 );
		$email    = get_metadata( 'post', $post_id, 'autor_email', true );
		if ( $sendmail == 1 && $email != '' ) {
			$send = get_metadata( 'post', $post_id, 'autor_email_send', true );

			if ( $send == '' ) {
				$subject = get_option( 'einstellungen_autorenmail_subject', false );
				$content = get_option( 'einstellungen_autorenmail_content', false );
				if ( $subject && $content ) {
					$content = str_replace( '%material_autor_name%', Materialpool_Autor::get_firstname($post_id)  . ' ' . Materialpool_Autor::get_lastname($post_id ), $content );
					$content = str_replace( '%materialpool_home%', get_option( 'siteurl' ), $content );
					$content = str_replace( '%material_autor_url%', Materialpool_Autor::autor_check_url( $post_id ) , $content );

					$content = str_replace( '%material_last_material%', Materialpool_Autor::last_material_name( $post_id ) , $content );
					$content = str_replace( '%redakteur_name%', Materialpool_Autor::redaktuer_name($post_id ) , $content );
					$content = str_replace( '%redakteur_reply_email%',  'redaktion@rpi-virtuell.de'  , $content ); //Materialpool_Autor::redakteur_email() , $content );
					

					$headers[] = 'From: Redaktion rpi-virtuell <redaktion@rpi-virtuell.de>';
					$headers[] = 'Reply-To: Redaktion rpi-virtuell <redaktion@rpi-virtuell.de>';
					$headers[] = 'bcc: material@rpi-virtuell.de';
					$mail = wp_mail( $email, $subject, $content , $headers );
					if ( $mail ) {
						$send = add_metadata( 'post', $post_id, 'autor_email_send', time() );
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
	static public function last_material_name( $id = 0 ) {
		global $post;
		global $wpdb;
		$id = ($id>0)?$id:$post->ID;
		$result = $wpdb->get_var( $wpdb->prepare( "SELECT $wpdb->posts.id  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.id = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = 'material_autoren' AND $wpdb->postmeta.meta_value = %s AND $wpdb->posts.post_status = 'publish' ORDER BY $wpdb->posts.post_date " , $id) );
		if ( ! is_wp_error( $result) && $result !== false ) {
			$material = get_post( $result);
			return $material->post_title;
		}
		return '';
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 * @return  string
	 *
	 */
	static public function redaktuer_name( $id = 0  ) {
		global $post;
		$id = ($id>0)?$id:$post->ID;

		$p = get_post( $id );
		$user_id = $p->post_author;
		$user = get_user_by( 'id', $user_id );
		return $user->user_nicename;
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 * @return  string
	 *
	 */
	static public function redakteur_email( $id = 0 ) {
		global $post;
		$id = ($id>0)?$id:$post->ID;
		$p = get_post( $id );
		$user_id = $p->post_author;
		$user = get_user_by( 'id', $user_id );
		return $user->user_email;
	}



	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 * @return  string
	 *
	 */
	static public function autor_check_url( $id = 0 ) {
		global $post;
		$id = ($id>0)?$id:$post->ID;
		$hash = get_metadata( 'post', $id, 'autor_hash', true );
		if ( $hash == '') {
			$hash = wp_hash( 'autor_hash' . time(). $id ) ;
			add_metadata('post', $id, 'autor_hash', $hash );
		}

		return get_option( 'siteurl' ) . '/check_autor/'.$hash ;
	}

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function firstname( $id = 0 ) {
        echo Materialpool_Autor::get_firstname( $id );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_firstname( $id = 0 ) {
        global $post;
	    $id = ($id>0)?$id:$post->ID;

        return get_metadata( 'post', $id, 'autor_vorname', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function lastname( $id = 0  ) {
        echo Materialpool_Autor::get_lastname( $id );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_lastname(  $id = 0) {
        global $post;
	    $id = ($id>0)?$id:$post->ID;
        return get_metadata( 'post', $id, 'autor_nachname', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function url( $id = 0) {
        echo Materialpool_Autor::get_url( $id  );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-autor-url
     */
    static public function url_html( $id = 0) {
        $url = Materialpool_Autor::get_url( $id );
        echo '<a href="' . $url . '" class="'. apply_filters( '', 'materialpool-template-autor-url' ) .'">' . $url . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_url( $id = 0 ) {
        global $post;
		$id = ($id>0)?$id:$post->ID;
        return get_metadata( 'post', $id, 'autor_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function buddypress() {
        echo Materialpool_Autor::get_buddypress();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-buddypress-member-url
     * @filters materialpool-template-autor-buddypress-url
     */
    static public function buddypress_html() {
        $name = Materialpool_Autor::get_buddypress();
        echo '<a href="' . apply_filters( 'materialpool-buddypress-member-url', Materialpool::$buddypress_member_url ) . $name  . '" class="'. apply_filters( 'materialpool-template-autor-buddypress-url', 'materialpool-autor-buddypress-url' ) .'">' . $name . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_buddypress() {
        global $post;

        return get_metadata( 'post', $post->ID, 'autor_buddypress', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function email() {
        echo Materialpool_Autor::get_email();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-autor-email
     */
    static public function email_html() {
        $email = Materialpool_Autor::get_email();
        echo '<a href="mailto:' . $email . '" class="'. apply_filters( 'materialpool-template-autor-email', 'materialpool-autor-email' ) .'">' . $email . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_email() {
        global $post;

        return get_metadata( 'post', $post->ID, 'autor_email', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function picture() {
        echo Materialpool_Autor::get_picture();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-autor-pic
     *
     */
    static public function picture_html() {
        $url = Materialpool_Autor::get_picture();
        echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-autor-pic', 'materialpool-autor-pic' ) .'"/>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_picture() {
        global $post;

        return get_metadata( 'post', $post->ID, 'autor_bild_url', true );
    }

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function get_organisationen() {
		global $post;

		return get_metadata( 'post', $post->ID, 'material_organisation', false );
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function organisationen_html() {
		$organistionen = Materialpool_Autor::get_organisationen();
		foreach ( $organistionen[0] as $organisation ) {
			$url = get_permalink( $organisation );
			$post = get_post( $organisation );
			echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-volumes', 'materialpool-template-material-volumes' ) .'">' . $post->post_title . '</a><br>';

		}
	}


    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-verweise
     */
    static public function organisation_html_cover () {
        $organistionen = Materialpool_Autor::get_organisationen();
        foreach ( $organistionen[0] as $organisation ) {
            $url = get_permalink( $organisation );
            $post = get_post( $organisation );
            $logo = get_metadata( 'post', $organisation, 'organisation_logo_url', true );
            echo "<div class='materialpool-template-autor-organisation'>";
            if ( $logo != '') {
                echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-organisation-logo' ) .'" style="background-image: url(\''. $logo .'\')"><img src="' . $logo . '"></a>';
            }
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-organisation' ) .'">' . $post->post_title . '</a><br>';
            echo "</div>";
        }
    }



	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function get_materialien() {
		global $post;

		return get_metadata( 'post', $post->ID, 'autor_material', false );
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function materialien_html() {
		$materialien = Materialpool_Autor::get_materialien();
		foreach ( $materialien as $material ) {
			$url = get_permalink( $material );
			$post = get_post( $material );
			echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-volumes', 'materialpool-template-material-volumes' ) .'">' . $post->post_title . '</a><br>';

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
        $materialien = Materialpool_Autor::get_materialien();
        $count = 0;
        foreach ( $materialien as $material ) {
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
     * @access public
     * @filters materialpool-template-material-verweise
     */
    static public function materialien_html_cover () {
        $materialien = Materialpool_Autor::get_materialien();
        foreach ( $materialien as $material ) {
            $url = get_permalink( $material[ 'ID' ] );
            $logo = Materialpool_Material::cover_facet_html( $material[ 'ID' ] );
            echo "<div class='materialpool-template-autor-material'>";
            echo "<div class='materialpool-template-autor-material-logo'>";
            if ( $logo != '') {
                echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-autor-cover-', 'materialpool-template-material-material-cover' ) .'">' . $logo . '</a><br>';
            }
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-autor', 'materialpool-template-material-autor' ) .'">' . $material[ 'post_title' ] . '</a><br>';
            echo "</div>";
            echo "</div>";
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @return number of materials from the current Autor
     */
    static public function get_count_posts_per_autor ($autor_id = 0) {
	    global $post;
	    $autor_id = ($autor_id>0)?$autor_id:$post->ID;
	    $autors = get_metadata( 'post', $autor_id, 'material_autoren' );
	    $data = sizeof( $autors[0] );


        return $data;

    }

	/**
	 *
	 * @since 0.0.1
	 * @access public
	 * @param int $autor_id
	 */
    static public function autor_request_button2( $autor_id = 0 ) {
	    global $post;

	    $autor_id = ($autor_id>0)?$autor_id:$post->ID;

	    $user = get_current_user_id();
		if ( $user == 0 ) {     // Benutzer nicht angemeldet.
			return;
		}
		// Hat User schon eine Autorenverknüpfung gestellt?
	    if ( get_user_meta( $user, 'autor_link', true ) != '' ) {
			return;
	    }
	    // Ist Autor schon mit einem User verknüpft?
	    if ( get_post_meta( $autor_id, 'user_link', true ) != '' ) {
			return;
	    }
    	?>
	    <div id="autor-subscription2" data-autor="<?php echo $autor_id; ?>" data-user="<?php echo $user; ?>">

	    </div>
		<?php


	}

	/**
	 *
	 * @since 0.0.1
	 * @access public
	 * @param int $autor_id
	 */
	static public function autor_request_button( $autor_id = 0 ) {
		global $post;

		$autor_id = ($autor_id>0)?$autor_id:$post->ID;

		$user = get_current_user_id();
		if ( $user == 0 ) {     // Benutzer nicht angemeldet.
			return;
		}
		// Hat User schon eine Autorenverknüpfung gestellt?
		if ( get_user_meta( $user, 'autor_link', true ) != '' ) {
			return;
		}
		// Ist Autor schon mit einem User verknüpft?
		if ( get_post_meta( $autor_id, 'user_link', true ) != '' ) {
			return;
		}
		?>
        <div id="autor-subscription" data-autor="<?php echo $autor_id; ?>" data-user="<?php echo $user; ?>">

        </div>
		<?php


	}

	/**
	 *
	 * @since 0.0.1
	 * @access public
	 */
	static public function shortcode_register_autor() {
		if ( ! is_user_logged_in() ) {
		    return;
		}

		$userID = get_current_user_id();
		$back = <<<END

<div class="materialpool-vorschlag">
    Ich möchte als AutorIn im Materialpool geführt werden.<br><br>
    <div class="materialpoolautorvorname">
        Vorname: <input type="text" id="materialpoolautorvorname" >
    </div>
    <div class="materialpoolautorname">
        Name: <input type="text" id="materialpoolautorname">
    </div>    
    <div class="materialpoolautoremail">
        E-Mail: <input type="text" id="materialpoolautoremail">
    </div>    
    <input type="hidden" id="materialpoolautorid"  value="$userID">
    <br>
    <button class="materialpoolautorregister">Absenden</button>
    <div class="materialpoolautorhinweis">
        <button class="materialpoolautorregister2" style="display: none;"></button>
    </div>
</div>
END;


		return $back;
    }
}
