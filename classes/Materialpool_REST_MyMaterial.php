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
						'required' => true
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
			'posts_per_page' => 5, //$request['per_page'],
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
		$data = array(
			'id'      => $item->ID,
			'slug'      => $item->post_name,
			'material_titel' => get_post_meta( $item->ID, 'material_titel', true ),
			'material_beschreibung'    => get_post_meta( $item->ID, 'material_beschreibung', true ),
			'material_kurzbeschreibung'  => get_post_meta( $item->ID, 'material_kurzbeschreibung', true ),
			'material_url' => get_post_meta( $item->ID, 'material_url', true ),
			'material_screenshot'   => get_post_meta( $item->ID, 'material_screenshot', true ),
			'material_altersstufe'   => get_post_meta( $item->ID, 'material_altersstufe', false ),
			'material_medientyp'   => get_post_meta( $item->ID, 'material_medientyp', false ),
			'material_bildungsstufe'   => get_post_meta( $item->ID, 'material_bildungsstufe', false ),

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
				'default'           => 5,
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