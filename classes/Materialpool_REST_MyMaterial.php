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
		$args = array(
			'post_type'      => 'material',
			'posts_per_page' => $request['per_page'],
			'paged'           => $request[ 'page' ],
		);
		if ( isset( $request[ 'suche' ] ) ) {
			$args[ 's' ] = $request[ 'suche' ];
		}

		// ID Liste anhand Facetten holen.
		$facets = array();
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

		$data = array(
			'facets' => $facets,
			'query_args' => array(
				'post_type' => 'material',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'paged' => -1,
			),
			'settings' => [
				'first_load' => true
			]
		);

		$url = 'https://material.rpi-virtuell.de/wp-json/facetwp/v1/fetch';
		$response = wp_remote_post( $url, [
			'body' => [ 'data' => json_encode( $data ) ]
		]);
		$ids = json_decode( $response[ 'body'] );


		//$materials = get_posts( $args );
		if ( ! empty( $args['s'] ) ) {
			$materials = new SWP_Query( array( 's' => $args['s'], 'posts_per_page' => -1, 'post__in' => $ids->results,'engine'   => 'default') );
		} else {
			$materials = new WP_Query( array( 'post__in' => $ids->results, 'posts_per_page' => -1, 'post_type' => 'material') );
		}
		// set max number of pages and total num of posts

//		$query = new SWP_Query( array ( 's' => $request[ 'suche' ],  'post__in' => $ids->results, 'engine' => 'default' ) );

//		view raw
//		$max_pages = $query->max_num_pages;
//		$total = $query->found_posts;

		$data = array();
		if ( $materials->posts ) {
			foreach ( $materials->posts as $material ) :
				$itemdata = $this->prepare_item_for_response( $material, $request );
				$data[] = $this->prepare_response_for_collection( $itemdata );
			endforeach;
		}

		//$data = rest_ensure_response( $data );

		$response = new WP_REST_Response($data, 200);

		$response->header( 'X-WP-Total', $total );
		$response->header( 'X-WP-TotalPages', $max_pages );

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
			foreach ( $as as $asitem ) {
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
			foreach ( $bs as $bsitem ) {
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
			foreach ( $mt as $mtitem ) {
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
			foreach ( $sw as $switem ) {
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
			foreach ( $ru as $ruitem ) {
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
			foreach ( $ku as $kuitem ) {
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
		$au =  get_post_meta( $item->ID, 'material_autor_facet', false );
		if ( is_array( $au ) ) {
			foreach ( $au as $auitem ) {
				$auArray[] = array(
					'name' => $auitem,
				);

			}
		} else {
			$auArray[] = array(
				'name' => $au,
			);
		}
		$au =  get_post_meta( $item->ID, 'material_autor_interim', true );
		if ( $au !=  '' ) {
			$auArray[] = array(
				'name' => $au,
			);
		}

		$data = array(
			'id'      => $item->ID,
			'slug'      => $item->post_name,
			'date' => $item->post_date,
			'material_titel' => get_post_meta( $item->ID, 'material_titel', true ),
			'material_beschreibung'    => get_post_meta( $item->ID, 'material_beschreibung', true ),
			'material_kurzbeschreibung'  => get_post_meta( $item->ID, 'material_kurzbeschreibung', true ),
			'material_url' => get_post_meta( $item->ID, 'material_url', true ),
			'material_screenshot'   =>  get_post_meta( $item->ID, 'material_cover_url', true )?get_post_meta( $item->ID, 'material_cover_url', true ): get_post_meta( $item->ID, 'material_screenshot', true ),
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