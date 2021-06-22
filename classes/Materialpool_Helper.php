<?php
/**
 *
 * @since      0.1.0
 * @package    Materialpool
 * @author     Joachim Happel
 *
 */



class Materialpool_Helper {

	static public function ac_column_value_icons( $value, $id, AC\Column $column ) {

	    if ( $column instanceof ACP\Column\Post\Status ) {

		    $status = get_post_status($id);

		    if($value == '&ndash;' &&  $status == 'broken'){
			    $value = '<span class="dashicons dashicons-editor-unlink"></span>';
		    }
		    if($value == '&ndash;' && $status == 'vorschlag'){
			    $value = '<span class="dashicons dashicons-plus-alt"></span>';
		    }
		    if($value == '&ndash;' && $status == 'check'){
			    $value = '<span class="dashicons dashicons-plus-alt"></span>';
		    }


	    }

	    return $value;
    }


	static function repair_all(){

		remove_filter('acf/update_value/name=material_autoren', 'bidirectional_acf_update_value', 10);
		remove_filter('acf/update_value/name=material_organisation', 'bidirectional_acf_update_value', 10);
		remove_filter('acf/update_value/name=autor_organisation', 'bidirectional_acf_update_value', 10);

		set_time_limit( 0 );

		self::sync_material();
		//self::sync_autoren();
		self::sync_organisations_autoren();


	}


	static function remove_post_meta_without_posts(){
		global $wpdb;
		$sql = "DELETE from wp_postmeta where wp_postmeta.post_id NOT IN (select ID from wp_posts)";
		$wpdb->get_results($sql);

	}


	static function sync_material(){

		/**
		 * alle zugeordneten Organisation updaten
		 */

		$args = array(
			'post_type' => 'material',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'material_organisation',
					'value' => '',
					'compare' => '!=',
				)
			)
		);
		$materialien = get_posts($args);


		foreach($materialien as $material){

			$material_id = $material->ID;

			system("echo ---------- " .$material_id);

			$organisation = get_field('material_organisation',$material_id, false);

			if(!is_array($organisation)){
				return;
			}


			foreach($organisation as $oid){



				$mats = get_field('material_organisation', $oid, false);


				if(! is_array($mats) ){

					if(intval($mats)>0){
						$mats=array($mats);
					}else{
						$mats=array();
					}
				}

				if(!in_array($material_id, $mats)){


					$mats[] = $material_id;

				}

				update_field('organisation_material_count', count($mats), $oid);
				update_field('material_organisation', $mats, $oid);
				update_field('material_organisation', $organisation, $material_id);

				system("echo " .$material_id);

			}
		}


		/**
		 * alle zugehörigen autoren updaten
		 */
		$args = array(
			'post_type' => 'material',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'material_autoren',
					'value' => '',
					'compare' => '!=',
				)
			)
		);




		$materialien = get_posts($args);

		foreach($materialien as $material){

			$material_id = $material->ID;
			system("echo ---------- " .$material_id);

			$autoren = get_field('material_autoren',$material_id, false);

			if(!is_array($autoren)){
				return;
			}


			foreach($autoren as $aid){



				$mats = get_field('material_autoren', $aid, false);


				if(! is_array($mats) ){

					if(intval($mats)>0){
						$mats=array($mats);
					}else{
						$mats=array();
					}
				}

				if(!in_array($material_id, $mats)){


					$mats[] = $material_id;

				}

				update_field('autor_material_count', count($mats), $aid);
				update_field('material_autoren', $mats, $aid);
				update_field('material_autoren', $autoren, $material_id);
				system("echo " .$material_id);
			}
		}



	}



	static function sync_autoren() {

		$args    = array(
			'post_type'      => 'autor',
			'post_status'    => 'publish',
			'posts_per_page' => 1000,
		);
		$autoren = get_posts( $args );
		if ( is_array( $autoren ) ) {
			foreach ( $autoren as $autor ) {
				system("echo ".self::sync_autor_material($autor));
			}
		}

	}

	static function sync_organisations_autoren() {

		$args = array(
			'post_type' => 'organisation',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);
		$organisationen = get_posts($args);
		if(is_array($organisationen)) {
			foreach ( $organisationen as $organisation ) {
		//		system("echo " .self::sync_organisation_material($organisation));
				system("echo " .self::sync_organisation_autor($organisation));

			}
		}

		return;


	}

	static function sync_autor_material(WP_POST $post ){

		if($post->post_type !== 'autor') return;

		$args = array(
			'post_type' => 'material',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'material_autoren',
					'value' => '"'.$post->ID.'"',
					'compare' => 'LIKE',
				)
			)
		);




		$materialien = get_posts($args);

		foreach($materialien as $material){

			$material_id = $material->ID;
			$autoren = get_field('material_autoren',$material_id, false);

			if(!is_array($autoren)){
				return;
			}


			foreach($autoren as $aid){



				$mats = get_field('material_autoren', $aid, false);


				if(! is_array($mats) ){

					if(intval($mats)>0){
						$mats=array($mats);
					}else{
						$mats=array();
					}
				}

				if(!in_array($material_id, $mats)){


					$mats[] = $material_id;

				}

				update_field('autor_material_count', count($mats), $aid);
				update_field('material_autoren', $mats, $aid);
				update_field('material_autoren', $autoren, $material_id);

			}
		}

		return $post->ID;
	}

	static function sync_organisation_material(WP_POST $post ){

		if($post->post_type !== 'organisation') return;

		$args = array(
			'post_type' => 'material',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'material_organisation',
					'value' => '"'.$post->ID.'"',
					'compare' => 'LIKE',
				)
			)
		);




		$materialien = get_posts($args);

		foreach($materialien as $material){

			$material_id = $material->ID;
			$organisation = get_field('material_organisation',$material_id, false);

			if(!is_array($organisation)){
				return;
			}


			foreach($organisation as $oid){



				$mats = get_field('material_organisation', $oid, false);


				if(! is_array($mats) ){

					if(intval($mats)>0){
						$mats=array($mats);
					}else{
						$mats=array();
					}
				}

				if(!in_array($material_id, $mats)){


					$mats[] = $material_id;

				}

				update_field('organisation_material_count', count($mats), $oid);
				update_field('material_organisation', $mats, $oid);
				update_field('material_organisation', $organisation, $material_id);

			}
		}
		return $post->ID;

	}


	static function sync_organisation_autor(WP_POST $post ){

		if($post->post_type !== 'organisation') return;

		$testautor = get_post('49028',);
		if(get_post_meta($testautor->ID,'material_organisation',true)){
			$args = array(
				'post_type' => 'autor',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key' => 'material_organisation',
						'value' => '"'.$post->ID.'"',
						'compare' => 'LIKE',
					),
					array(
						'key' => 'material_organisation',
						'value' => $post->ID,
						'compare' => '=',
					)
				)
			);

		}else{
			$args = array(
				'post_type' => 'autor',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key' => 'autor_organisation',
						'value' => '"'.$post->ID.'"',
						'compare' => 'LIKE',
					)
				)
			);

		}


		$organisation_autoren = array();





		$autoren = get_posts($args);
		foreach($autoren as $autor){

			$organisation_autoren[] = $autor->ID;

			$o_arr = get_field('autor_organisation',$autor->ID, false);
			if(!$o_arr){
				$o_arr = get_field('material_organisation',$autor->ID, false);
			}

			if(is_array($o_arr)){
				update_field('autor_organisation', $o_arr, $autor->ID );
			}else{
				update_field('autor_organisation', array($post->ID),   $autor->ID);
			}
			update_field('_autor_organisation', 'field_5db183394c9c0', $autor->ID );
			delete_post_meta($autor->ID,  'material_organisation');
			delete_post_meta($autor->ID,  '_material_organisation');


		}
		update_field('autor_organisation', $organisation_autoren, $post->ID );
		update_field('_autor_organisation', 'field_5dcd86fc4f69b', $post->ID );

		delete_post_meta($post->ID,  'material_autoren');
		delete_post_meta($post->ID,  '_material_autoren');

		return $post->ID;

	}

}
