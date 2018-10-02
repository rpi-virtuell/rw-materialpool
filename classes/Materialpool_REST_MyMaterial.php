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
		pods_no_conflict_on(  );
		$args = array(
			'post_type'      => 'material',
			'posts_per_page' => $request['per_page'],
			'paged'           => $request[ 'page' ],
		);

		if ( isset ( $request[ 'einrichtung' ] ) ) {
			$args[ 'meta_query' ] = array(
				array(
					'key' => 'material_organisation_facet',
					'value' => self::get_einrichtung_title_by_slug( $request[ 'einrichtung' ] ),
				)
			);
		}

		if ( isset ( $request[ 'autor' ] ) ) {
			$args[ 'meta_query' ] = array(
				array(
					'key' => 'material_autor_facet',
					'value' => self::get_autor_title_by_slug( $request[ 'autor' ] ),
				)
			);
		}

		if ( isset ( $request[ 'bildungsstufe' ] ) ) {
			$args[ 'tax_query' ] = array(
				array(
					'taxonomy' => 'bildungsstufe',
					'field' => 'slug',
					'terms' => $request[ 'bildungsstufe' ] ,
				)
			);
		}

		if ( isset ( $request[ 'altersstufe' ] ) ) {
			$args[ 'tax_query' ] = array(
				array(
					'taxonomy' => 'altersstufe',
					'field' => 'slug',
					'terms' => $request[ 'altersstufe' ] ,
				)
			);
		}

		if ( isset ( $request[ 'medientyp' ] ) ) {
			$args[ 'tax_query' ] = array(
				array(
					'taxonomy' => 'medientyp',
					'field' => 'slug',
					'terms' => $request[ 'medientyp' ] ,
				)
			);
		}
		if ( isset ( $request[ 'inklusion' ] ) ) {
			$args[ 'tax_query' ] = array(
				array(
					'taxonomy' => 'inklusion',
					'field' => 'slug',
					'terms' => $request[ 'inklusion' ] ,
				)
			);
		}

		if ( isset ( $request[ 'schlagwort' ] ) ) {
			$args[ 'tax_query' ] = array(
				array(
					'taxonomy' => 'schlagwort',
					'field' => 'slug',
					'terms' => $request[ 'schlagwort' ] ,
				)
			);
		}

		$materials = get_posts( $args );
		// set max number of pages and total num of posts
		$query = new WP_Query( $args );
		$max_pages = $query->max_num_pages;
		$total = $query->found_posts;


		$data = array();

		if ( $materials ) {
			foreach ( $materials as $material ) :
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

		$data = array(
			'id'      => $item->ID,
			'slug'      => $item->post_name,
			'material_titel' => get_post_meta( $item->ID, 'material_titel', true ),
			'material_beschreibung'    => get_post_meta( $item->ID, 'material_beschreibung', true ),
			'material_kurzbeschreibung'  => get_post_meta( $item->ID, 'material_kurzbeschreibung', true ),
			'material_url' => get_post_meta( $item->ID, 'material_url', true ),
			'material_screenshot'   => get_post_meta( $item->ID, 'material_screenshot', true ),
			'material_altersstufe'   => $asArray,
			'material_medientyp'   => $mtArray,
			'material_bildungsstufe'   => $bsArray,
			'material_schlagworte'   => $swArray,
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
				'default'           => 15,
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