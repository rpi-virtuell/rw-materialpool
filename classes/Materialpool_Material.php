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
     * @access	public
     *
     */
    static public function load_template($template) {
        global $post;

        if ($post->post_type == "material"){
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
            return $template_path;
        }
        return $template;
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
        $columns[ 'organisation-name' ] = _x( 'Organisation', 'Organisation list field',  Materialpool::$textdomain );
        $columns[ 'autor-name' ] = _x( 'Autoren', 'Organisation list field',  Materialpool::$textdomain );
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
        if ( $column_name == 'organisation-name' ) {
            $data = "name der zugeordneten Orgas";
        }
        if ( $column_name == 'autor-name' ) {
            $data = "name der zugeordneten Autoren";
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
            'taxonomy-lizenz' => 'taxonomy-lizenz',
            'taxonomy-verfuegbarkeit' => 'taxonomy-verfuegbarkeit',
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

		if ( "material" != $post_type ) return;

		$title = $_POST[ 'pods_meta_material_titel' ];

        $wpdb->update(
            $wpdb->posts,
            array(
                'post_title' => $title,
                'post_name' => sanitize_title( $title )
            ),
            array( 'ID' => $post_id ),
            array(
                '%s',
                '%s'
            ),
            array( '%d' )
        );

        $_POST[ 'post_title'] = $title;
	}

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function title() {
        echo Materialpool_Material::get_title();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_title() {
        global $post;

        return $post->post_title;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function shortdecription() {
        echo Materialpool_Material::get_shortdescription();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_shortdescription() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_kurzbeschreibung', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function decription() {
        echo Materialpool_Material::get_description();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_description() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_beschreibung', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function releasedate() {
        echo Materialpool_Material::get_releasedate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_releasedate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_veroeffentlichungsdatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function depublicationdate() {
        echo Materialpool_Material::get_depublicationdate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_depublicationdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_depublizierungsdatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function reviewdate() {
        echo Materialpool_Material::get_reviewdate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_reviewdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_wiedervorlagedatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function createdate() {
        echo Materialpool_Material::get_createdate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_createdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_erstellungsdatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function url() {
        echo Materialpool_Material::get_url();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-material-url
     */
    static public function url_html() {
        $url = Materialpool_Material::get_url();
        echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-url', 'materialpool-template-material-url' ) .'">' . $url . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_url() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function picture() {
        echo Materialpool_Material::get_picture();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-material-picture
     *
     */
    static public function picture_html() {
        $pic  = Materialpool_Material::get_picture();
	    if ( is_array( $pic ) ) {
		    $url = wp_get_attachment_url( $pic[ 'ID' ] );
		    echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
	    }
	    $url = Materialpool_Material::get_picture_url();
	    if ( $url != '') {
		    echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
	    }

    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_picture() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_cover', true );
    }

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function get_picture_url() {
		global $post;

		return get_metadata( 'post', $post->ID, 'material_cover_url', true );
	}


    /**
     *
     * @since 0.0.1
     * @access	public
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
     * @access	public
     *
     */
    static public function get_werk_id() {
        global $post;

	    $werk = get_metadata( 'post', $post->ID, 'material_werk', true );
		if ( is_array( $werk ) ) {
		    return ( $werk["ID"] );
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
        global $wpdb;
        $result = $wpdb->get_row( $wpdb->prepare("SELECT count(post_id) as count FROM $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_werk', $post->ID ) );
        if ( is_object( $result)  && $result->count == 0 ) {
            return false;
        } else {
            return true;
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
        global $wpdb;
        $result = $wpdb->get_row( $wpdb->prepare("SELECT count(post_id) as count FROM $wpdb->postmeta WHERE meta_key = %s and post_id = %s", 'material_werk', $post->ID ) );
        if ( is_object( $result)  && $result->count == 0 ) {
            return false;
        } else {
            return true;
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
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s order by post_title asc" , 'material_werk', $post->ID ) );
            foreach ( $result as $material ) {
                echo $material->post_title . '<br>';
            }
        }
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
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s order by post_title asc" , 'material_werk', $post->ID ) );
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
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s order by post_title asc" , 'material_werk', $werk ) );
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
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s order by post_title asc" , 'material_werk', $werk ) );
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
	static public function verweise ( $selfinclude = false ) {
		$verweise = Materialpool_Material::get_verweise();
		foreach ( $verweise as $verweis ) {
			echo $verweis[ 'post_title' ] . '<br>';
		}
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
	 * @access	public
	 *
	 */
	static public function get_verweise() {
		global $post;

		return get_metadata( 'post', $post->ID, 'material_verweise', false );
	}

}
