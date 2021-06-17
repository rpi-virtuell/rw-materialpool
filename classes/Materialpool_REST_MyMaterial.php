<?php



class Materialpool_REST_MyMaterial extends WP_REST_Controller {

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version = '1';
		$namespace = 'mymaterial/v' . $version;
		$base = 'material';
		register_rest_route( $namespace, '/' . $base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'args'                => array(
					'page' => array (
						'required' => false
					),
					'per_page' => array (
						'required' => false
					),
				),
			),
		) );

	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {

		$per_page = intval($request['per_page'])<>0?$request['per_page']:10;
		$page = isset( $request['paged'] ) ? (int) $request['paged'] : false;
		if(!$page){
			$page = isset( $request['page'] ) ? (int) $request['page'] : 1;
		}
		$args = array(
			'post_type'      => 'material',
			'post_status' => 'publish',
			'posts_per_page' =>  $per_page,
			'order_by' => 'post_date',
			'order' => 'DESC',
			'paged'           => $request[ 'page' ],
		);
		$facets = array();
		
		if ( isset( $request[ 'suche' ] ) ) {
			
			//$args[ 's' ] = $request[ 'suche' ];
		   $facets[ 'suche']=$request[ 'suche' ];
		}

		// ID Liste anhand Facetten holen.
		if ( isset ($request[ 'bildungsstufe' ]) and $request[ 'bildungsstufe' ] != '' ) {
			$facets[ 'bildungsstufe']= explode( ',', $request[ 'bildungsstufe' ] );
		}
		if ( isset ($request[ 'altersstufe' ]) and $request[ 'altersstufe' ] != '' ) {
			$facets[ 'altersstufe']= explode( ',', $request[ 'altersstufe' ] );
		}
		if ( isset ($request[ 'medientyp' ]) and $request[ 'medientyp' ] != '' ) {
			$facets[ 'medientyp']= explode( ',', $request[ 'medientyp' ] );
		}
		if ( isset ($request[ 'inklusion' ]) and $request[ 'inklusion' ] != '' ) {
			$facets[ 'inklusion']= explode( ',', $request[ 'inklusion' ] );
		}
		if ( isset ($request[ 'schlagwort' ]) and $request[ 'schlagwort' ] != '' ) {
			$facets[ 'schlagwort']= explode( ',', $request[ 'schlagwort' ] );
		}
		if ( isset ($request[ 'schlagworte' ]) and $request[ 'schlagworte' ] != '' ) {
			$facets[ 'schlagworte']= explode( ',', $request[ 'schlagworte' ] );
		}
		if ( isset ($request[ 'kompetenz' ]) and $request[ 'kompetenz' ] != '' ) {
			$facets[ 'kompetenz']= explode( ',', $request[ 'kompetenz' ] );
		}
		if ( isset ($request[ 'einrichtung' ]) and $request[ 'einrichtung' ] != '' ) {
			$facets[ 'einrichtung']= explode( ',', $request[ 'einrichtung' ] );
		}
		if ( isset ($request[ 'autor' ]) and $request[ 'autor' ] != '' ) {
			$facets[ 'autor']= explode( ',', $request[ 'autor' ] );
		}
		if ( isset ($request[ 'lizenz' ]) and $request[ 'lizenz' ] != '' ) {
			$facets[ 'lizenz']= explode( ',', $request[ 'lizenz' ] );
		}
		if ( isset ($request[ 'sprache' ]) and $request[ 'sprache' ] != '' ) {
			$facets[ 'sprache']= explode( ',', $request[ 'sprache' ] );
		}
		if ( isset ($request[ 'vorauswahl' ]) and $request[ 'vorauswahl' ] != '' ) {
			$facets[ 'vorauswahl']= explode( ',', $request[ 'vorauswahl' ] );
		}

		foreach ( $facets as $facet_name => $facet_value ) {


			$facet = FWP()->helper->get_facet_by_name( $facet_name );
			if ( false !== $facet ) {
				$facet['selected_values'] = (array) $facet_value;
				$valid_facets[ $facet_name ] = $facet;
				FWP()->facet->facets[ $facet_name ] = $facet;
			}
		}

        // Get bucket of post IDs
        FWP()->facet->query_args = $args;
        FWP()->facet->settings = array('first_load' => true);
        $post_ids = FWP()->facet->get_filtered_post_ids();

        // SQL WHERE used by facets
        $where_clause = ' AND post_id IN (' . implode( ',', $post_ids ) . ')';

        // Check if empty
        if ( 0 === $post_ids[0] && 1 === count( $post_ids ) ) {
            $post_ids = [];
        }



        // get_where_clause() needs "found_posts" (keep this BELOW the empty check)
        FWP()->facet->query = (object) [ 'found_posts' => count( $post_ids ) ];

        // Get valid facets and their values
        foreach ( $facets as $facet_name => $facet ) {
            $args = [
                'facet' => $facet,
                'where_clause' => $where_clause,
                'selected_values' => $facet['selected_values'],
            ];

            $facet_data = [
                'name'          => $facet['name'],
                'label'         => $facet['label'],
                'type'          => $facet['type'],
                'selected'      => $facet['selected_values'],
            ];

        }

        $total_rows = count( $post_ids );

        // Paginate?
        if ( 0 < $per_page ) {
            $total_pages = ceil( $total_rows / $per_page );

            if ( $page > $total_pages ) {
                $post_ids = [];
            }
            else {
                $offset = ( $per_page * ( $page - 1 ) );

                $post_ids = array_slice( $post_ids, $offset, $per_page );

            }
        }
        else {
            $total_pages = ( 0 < $total_rows ) ? 1 : 0;
        }
		$total = count( $post_ids );

		$data = array();

		if(count($post_ids)>0){
			if ( ! empty( $args['s'] ) ) {
				$materials = new SWP_Query( array( 's' => $args['s'], 'posts_per_page' => -1, 'post__in' => $post_ids,'engine'   => 'default') );
			} else {
				$materials = new WP_Query( array( 'post__in' => $post_ids,
				                                  'post_type' => 'material',
				                                  'posts_per_page' => -1
					)
				);
			}
			if ( $materials->posts ) {
				foreach ( $materials->posts as $material ) :
					$itemdata = $this->prepare_item_for_response( $material, $request );
					$data[] = $this->prepare_response_for_collection( $itemdata );
				endforeach;
			}
		}


		$response = new WP_REST_Response($data, 200);

		$response->header( 'X-WP-Total', $total );
		$response->header( 'X-WP-TotalPages', $total_pages );

		return $response;

		//return $data;
	}


	/**
	 * Prepare the item for the REST response
	 *
	 * @param mixed $item WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Altersstufe
		$asArray = array();
		$as =  get_post_meta( $item->ID, 'material_altersstufe', false );
		if ( is_array( $as ) ) {
			foreach ( $as[0] as $asitem ) {
				$to = get_term_by( 'term_taxonomy_id', $asitem );
				$asArray[] = array(
					'name' => $to->name,
					'term_id' => $to->term_id,
				);

			}
		} else {
			$to = get_term_by( 'term_taxonomy_id', $as );
			$asArray[] = array(
				'name' => $to->name,
				'term_id' => $to->term_id,
			);
		}

		// Bildungsstufe
		$bsArray = array();
		$bs =  get_post_meta( $item->ID, 'material_bildungsstufe', false );
		if ( is_array( $bs ) ) {
			foreach ( $bs[0] as $bsitem ) {
				$to = get_term_by( 'term_taxonomy_id', $bsitem );
				$bsArray[] = array(
					'name' => $to->name,
					'term_id' => $to->term_id,
				);

			}
		} else {
			$to = get_term_by( 'term_taxonomy_id', $bs );
			$bsArray[] = array(
				'name' => $to->name,
				'term_id' => $to->term_id,
			);
		}

		// Medientyp
		$mtArray = array();
		$mt =  get_post_meta( $item->ID, 'material_medientyp', false );
		if ( is_array( $mt ) ) {
			foreach ( $mt[0] as $mtitem ) {
				$to = get_term_by( 'term_taxonomy_id', $mtitem );
				$mtArray[] = array(
					'name' => $to->name,
					'term_id' => $to->term_id,
				);

			}
		} else {
			$to = get_term_by( 'term_taxonomy_id', $mt );
			$mtArray[] = array(
				'name' => $to->name,
				'term_id' => $to->term_id,
			);
		}

		// Schlagworte
		$swArray = array();
		$sw =  get_post_meta( $item->ID, 'material_schlagworte', false );
		if ( is_array( $sw ) ) {
			foreach ( $sw[0] as $ix => $switem ) {
				$so = get_term_by( 'term_taxonomy_id', $switem );
				$swArray[] = array(
					'name' => $so->name,
					'term_id' => $so->term_id,
				);

			}
		} else {
			$so = get_term_by( 'term_taxonomy_id', $sw );
			$swArray[] = array(
				'name' => $so->name,
				'term_id' => $so->term_id,
			);
		}
		// Rubrik
		$ruArray = array();
		$ru =  get_post_meta( $item->ID, 'material_rubrik', false );
		if ( is_array( $ru ) ) {
			foreach ( $ru[0] as $ruitem ) {
				$so = get_term_by( 'term_taxonomy_id', $ruitem );
				$ruArray[] = array(
					'name' => $so->name,
					'term_id' => $so->term_id,
				);

			}
		} else {
			$so = get_term_by( 'term_taxonomy_id', $ru );
			$ruArray[] = array(
				'name' => $so->name,
				'term_id' => $so->term_id,
			);
		}

		// kompetenz
		$kuArray = array();
		$ku =  get_post_meta( $item->ID, 'material_kompetenz', false );
		if ( is_array( $ku ) ) {
			foreach ( $ku[0] as $kuitem ) {
				$so = get_term_by( 'term_taxonomy_id', $kuitem );
				$kuArray[] = array(
					'name' => $so->name,
					'term_id' => $so->term_id,
				);

			}
		} else {
			$so = get_term_by( 'term_taxonomy_id', $ku );
			$kuArray[] = array(
				'name' => $so->name,
				'term_id' => $so->term_id,
			);
		}

		// Autoren
		$auArray = array();
		$au =  get_post_meta( $item->ID, 'material_autoren', true );

		if ( is_array( $au ) ) {
			foreach ( $au as $auitem ) {
				$author = get_post_meta($auitem,'autor_vorname', true) . " " . get_post_meta($auitem,'autor_nachname', true);
				$auArray[] = array(
					'post_id' => $auitem,
					'name' => $author,
				);

			}
		} else {
			$author = get_post_meta($au,'autor_vorname', true) . " " . get_post_meta($au,'autor_nachname', true);
			$auArray[] = array(
				'post_id' => $au,
				'name' => $author,
			);

		}
		$au =  get_post_meta( $item->ID, 'material_autor_interim', true );
		if ( $au !=  '' ) {
			$auArray[] = array(
				'name' => $au,
			);
		}

		$cover_id = get_post_meta( $item->ID, 'material_cover', true );
		if($cover_id){
			$thumbnail_url = get_the_guid($cover_id);
		}else{
			$thumbnail_url =get_post_meta( $item->ID, 'material_cover_url', true )?get_post_meta( $item->ID, 'material_cover_url', true ): get_post_meta( $item->ID, 'material_screenshot', true );
		}

		$data = array(
			'id'      => $item->ID,
			'slug'      => $item->post_name,
			'date' => $item->post_date,
			'material_titel' => get_post_meta( $item->ID, 'material_titel', true ),
			'material_beschreibung'    => get_post_meta( $item->ID, 'material_beschreibung', true ),
			'material_kurzbeschreibung'  => get_post_meta( $item->ID, 'material_kurzbeschreibung', true ),
			'material_url' => get_post_meta( $item->ID, 'material_url', true ),
			'material_screenshot'   =>  $thumbnail_url,
			'material_altersstufe'   => $asArray,
			'material_medientyp'   => $mtArray,
			'material_bildungsstufe'   => $bsArray,
			'material_schlagworte'   => $swArray,
			'material_rubrik'   => $ruArray,
			'material_review_url' => get_permalink( $item->ID ),
			'material_autoren'   => $auArray,
			'material_kompetenzen'   => $kuArray,
			'material_jahr'   => get_post_meta( $item->ID, 'material_jahr', true ),
			'parent' =>get_post_meta( $item->ID, 'material_werk', true )
		);

		return $data;
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'page'     => array(
				'description'       => 'Current page of the collection.',
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'description'       => 'Maximum number of items to be returned in result set.',
				'type'              => 'integer',
				'default'           => 100,
				'sanitize_callback' => 'absint',
			),

		);
	}

	public function get_autor_title_by_slug($slug){
		$posts = get_posts(array(
			'name' => $slug,
			'posts_per_page' => 1,
			'post_type' => 'autor',
			'post_status' => 'publish'
		));

		if(! $posts ) {
			return false;
		}

		return $posts[0]->post_title;
	}

	public function get_einrichtung_title_by_slug($slug){
		$posts = get_posts(array(
			'name' => $slug,
			'posts_per_page' => 1,
			'post_type' => 'organisation',
			'post_status' => 'publish'
		));

		if(! $posts ) {
			return false;
		}

		return $posts[0]->post_title;
	}


}
