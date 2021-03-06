<?php

/**
 * @description Manage the plugin settings
 */
class Materialpool_WP_CLI_Command extends WP_CLI_Command {

	static public function convert_themenseiten( $args ) {
		global $wpdb;
		WP_CLI::line('Ermittle Themenseiten mit Materialgruppen...' );
		$prefix = $wpdb->base_prefix;
		$result = $wpdb->get_results("SELECT distinct(pandarf_parent_post_id ) as ID    FROM " . $prefix . "pods_themenseitengruppen");
		foreach ( $result as $obj ) {
			WP_CLI::line('Themenseiten ID ' . $obj->ID );
			WP_CLI::line('- reading data ');
			$result2 = $wpdb->get_results("SELECT *  FROM " . $prefix . "pods_themenseitengruppen where pandarf_parent_post_id = " . $obj->ID ."  order by pandarf_order ");
			WP_CLI::line('- clean up ');
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and ( meta_key like 'themengruppen%' OR meta_key like '_themengruppen%' ) ");
			$counter = 0;
			foreach ( $result2 as $group ) {
				$name = $group->gruppe;
				$info = $group->gruppenbeschreibung;
				$material = $group->auswahl;
				$material_convertet = array_filter( explode(",", $material ) )  ;

				add_post_meta( $obj->ID, 'themengruppen_'.$counter."_gruppe_von_materialien", $name );
				add_post_meta( $obj->ID, '_themengruppen_'.$counter."_gruppe_von_materialien", 'field_5dcbda11857d2' );
				add_post_meta( $obj->ID, 'themengruppen_'.$counter."_infos", $info );
				add_post_meta( $obj->ID, '_themengruppen_'.$counter."_infos", 'field_5dcbda3d857d3' );
				add_post_meta( $obj->ID, 'themengruppen_'.$counter."_material_in_dieser_gruppe", $material_convertet );
				add_post_meta( $obj->ID, '_themengruppen_'.$counter."_material_in_dieser_gruppe", 'field_5dcbda53857d4' );
				WP_CLI::line('- writing group ' . ( $counter +1) );
				$counter++;
			}
			add_post_meta( $obj->ID, 'themengruppen', $counter );
		}
	}

	static public function convert_material( $args ) {
		global $wpdb;
		WP_CLI::line( 'Ermittle Materialien ...' );
		$prefix = $wpdb->base_prefix;
		$result = $wpdb->get_results("SELECT  ID    FROM " . $wpdb->posts . " WHERE post_type = 'material'  ");
		$counter = 0;
		foreach ( $result as $obj ) {
			$counter++;
			WP_CLI::line('Material ID ' . $obj->ID );
			WP_CLI::line('- convert bildungsstufen ');
			$bildung = get_post_meta( $obj->ID, 'material_bildungsstufe', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_bildungsstufe'  ");
			add_post_meta( $obj->ID, 'material_bildungsstufe' , $bildung );
			add_post_meta( $obj->ID, '_material_bildungsstufe', 'field_5dbc8a128988b' );

			WP_CLI::line('- convert altersstufen ');
			$altersstufe = get_post_meta( $obj->ID, 'material_altersstufe', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_altersstufe'  ");
			add_post_meta( $obj->ID, 'material_altersstufe' , $altersstufe );
			add_post_meta( $obj->ID, '_material_altersstufe', 'field_5dbc8a9ea8d52' );

			WP_CLI::line('- convert medientyp ');
			$medientyp = get_post_meta( $obj->ID, 'material_medientyp', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_medientyp'  ");
			add_post_meta( $obj->ID, 'material_medientyp' , $medientyp );
			add_post_meta( $obj->ID, '_material_medientyp', 'field_5dbc8bed9f213' );

			WP_CLI::line('- convert autoren ');
			$autoren = get_post_meta( $obj->ID, 'material_autoren', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_autoren'  ");
			add_post_meta( $obj->ID, 'material_autoren' , $autoren );
			add_post_meta( $obj->ID, '_material_autoren', 'field_5dbc83e609b8b' );

			WP_CLI::line('- convert organisationen ');
			$organisation = get_post_meta( $obj->ID, 'material_organisation', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_organisation'  ");
			add_post_meta( $obj->ID, 'material_organisation' , $organisation );
			add_post_meta( $obj->ID, '_material_organisation', 'field_5dbc87636419f' );


		}
		WP_CLI::line( $counter . ' Materialien konvertiert' );
	}

	static public function convert_autor( $args ) {
		global $wpdb;
		WP_CLI::line( 'Ermittle Autoren ...' );
		$prefix = $wpdb->base_prefix;
		$result = $wpdb->get_results("SELECT  ID    FROM " . $wpdb->posts . " WHERE post_type = 'autor'  ");
		$counter = 0;
		foreach ( $result as $obj ) {
			$counter++;
			WP_CLI::line('Autor ID ' . $obj->ID );
			WP_CLI::line('- convert material ');
			$autoren = get_post_meta( $obj->ID, 'autor_material', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_autoren'  ");
			add_post_meta( $obj->ID, 'material_autoren' , $autoren );
			add_post_meta( $obj->ID, '_material_autoren', 'field_5db183a04c9c1' );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'autor_material'  ");

			WP_CLI::line('- convert organisationen ');
			$organisation = get_post_meta( $obj->ID, 'autor_organisation', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_organisation'  ");
			add_post_meta( $obj->ID, 'material_organisation' , $organisation );
			add_post_meta( $obj->ID, '_material_organisation', 'field_5db183394c9c0' );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'autor_organisation'  ");
		}
		WP_CLI::line( $counter . ' Autoren konvertiert' );
	}

	static public function convert_organisation( $args ) {
		global $wpdb;
		WP_CLI::line( 'Ermittle Organisationen ...' );
		$prefix = $wpdb->base_prefix;
		$result = $wpdb->get_results("SELECT  ID    FROM " . $wpdb->posts . " WHERE post_type = 'organisation'  ");
		$counter = 0;
		foreach ( $result as $obj ) {
			$counter++;
			WP_CLI::line('Organisation ID ' . $obj->ID );
			WP_CLI::line('- convert autor ');
			$autoren = get_post_meta( $obj->ID, 'organisation_autor', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_autoren'  ");
			add_post_meta( $obj->ID, 'material_autoren' , $autoren );
			add_post_meta( $obj->ID, '_material_autoren', 'field_5db183a04c9c1' );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'organisation_autor'  ");

			WP_CLI::line('- convert material ');
			$organisation = get_post_meta( $obj->ID, 'organisation_material', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_organisation'  ");
			add_post_meta( $obj->ID, 'material_organisation' , $organisation );
			add_post_meta( $obj->ID, '_material_organisation', 'field_5db183394c9c0' );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'organisation_material'  ");
		}
		WP_CLI::line( $counter . ' Organisationen konvertiert' );
	}

	static public function convert_material2( $args ) {
		global $wpdb;
		WP_CLI::line( 'Ermittle Materialien ...' );
		$prefix = $wpdb->base_prefix;
		$result = $wpdb->get_results("SELECT  ID    FROM " . $wpdb->posts . " WHERE post_type = 'material'  ");
		$counter = 0;
		foreach ( $result as $obj ) {
			$counter++;
			WP_CLI::line('Material ID ' . $obj->ID );
			WP_CLI::line('- convert Kompetenzen ');
			$kompetenz = get_post_meta( $obj->ID, 'material_kompetenz', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_kompetenz'  ");
			add_post_meta( $obj->ID, 'material_kompetenz' , $kompetenz );
			add_post_meta( $obj->ID, '_material_kompetenz', 'field_5dce78682efe3' );
		}
		WP_CLI::line( $counter . ' Materialien konvertiert' );
	}


	static public function convert_material3( $args ) {
		global $wpdb;
		WP_CLI::line( 'Ermittle Materialien ...' );
		$prefix = $wpdb->base_prefix;
		$result = $wpdb->get_results("SELECT  ID    FROM " . $wpdb->posts . " WHERE post_type = 'material'  ");
		$counter = 0;
		foreach ( $result as $obj ) {
			$counter++;
			WP_CLI::line('Material ID ' . $obj->ID );
			WP_CLI::line('- convert autor facet ');
			$autoren = get_post_meta( $obj->ID, 'material_autoren', false );
			if ( is_array( $autoren[0] )) {
				foreach ( $autoren[0] as $autor ) {
					add_post_meta( $obj->ID, 'material_autoren_facet_view', $autor );
				}
			}
			WP_CLI::line('- convert organisation facet ');
			$organisationen = get_post_meta( $obj->ID, 'material_organisation', false );
			if ( is_array( $organisationen[0] )) {
				foreach ( $organisationen[0] as $organisation ) {
					add_post_meta( $obj->ID, 'material_organisation_facet_view', $organisation );
				}
			}

		}
		WP_CLI::line( $counter . ' Materialien konvertiert' );
	}


	static public function convert_material4( $args ) {
		global $wpdb;
		WP_CLI::line( 'Ermittle Materialien ...' );
		$prefix = $wpdb->base_prefix;
		$result = $wpdb->get_results("SELECT  ID    FROM " . $wpdb->posts . " WHERE post_type = 'material' ");
		$counter = 0;
		foreach ( $result as $obj ) {
			$counter++;
			WP_CLI::line('Material ID ' . $obj->ID );
			WP_CLI::line('- convert schlagworte ');
			$kompetenz = get_post_meta( $obj->ID, 'material_schlagworte', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'material_schlagworte'  ");
			add_post_meta( $obj->ID, 'material_schlagworte' , $kompetenz );
			add_post_meta( $obj->ID, '_material_schlagworte', 'field_5dbc888798a2f' );

		}
		WP_CLI::line( $counter . ' Materialien konvertiert' );
	}

	static public function convert_material5( $args ) {
		global $wpdb;
		WP_CLI::line( 'Ermittle Materialien ...' );
		$prefix = $wpdb->base_prefix;
		$result = $wpdb->get_results("SELECT  ID    FROM " . $wpdb->posts . " WHERE post_type = 'material'  and post_status = 'publish' ");
		$counter = 0;
		foreach ( $result as $obj ) {
			$counter++;
			$werk = get_post_meta( $obj->ID, 'material_werk', false );
			if (!empty ($werk )) {
				WP_CLI::line('Werk ' . $obj->ID );

			    $wpdb->update( $wpdb->posts, array( 'post_parent' => $werk ), array( 'ID' => $obj->ID) );

			}
			unset ( $werk );
			$band = get_post_meta( $obj->ID, 'material_band', false );
			var_dump( $band );
			if ( is_array( $band )) {
				foreach ($band as $key ) {
					WP_CLI::line('Band ' . $obj->ID );
					$wpdb->update( $wpdb->posts, array( 'post_parent' => $key ), array( 'ID' => $obj->ID) );
					update_field('material_werk', $key, $obj->ID);
				}
			} else {
				if ( $band != '' ) {
					WP_CLI::line('Band ' . $obj->ID );
					$wpdb->update( $wpdb->posts, array( 'post_parent' => $key ), array( 'ID' => $obj->ID) );
					update_field( 'material_werk', $band, $obj->ID );
				}
			}
			unset ( $band);
		}
		WP_CLI::line( $counter . ' Materialien konvertiert' );
	}

	static public function convert_startseite( $args ) {
		global $wpdb;
		WP_CLI::line( 'Startseite ...' );
		$prefix = $wpdb->base_prefix;
		$result = $wpdb->get_results("SELECT  ID    FROM " . $wpdb->posts . " WHERE post_type = 'page'  ");
		$counter = 0;
		foreach ( $result as $obj ) {
			$aktuell = get_post_meta( $obj->ID, 'startseite_aktuell', false );
			$res = $wpdb->get_results("DELETE FROM " . $prefix . "postmeta where post_id =  " . $obj->ID . " and meta_key = 'startseite_aktuell'  ");
			add_post_meta( $obj->ID, 'startseite_aktuell' , $aktuell );
			add_post_meta( $obj->ID, '_startseite_aktuell', 'field_5e2ab46872c55' );
		}
		WP_CLI::line( ' Startseite konvertiert' );
	}

	static public function convert_themenseiten2( $args ) {
		global $wpdb;
		WP_CLI::line('Ermittle Themenseiten mit Materialgruppen...' );
		$result = $wpdb->get_results("SELECT  ID    FROM " . $wpdb->posts . " WHERE post_type = 'themenseite' ");
		foreach ( $result as $obj ) {
			WP_CLI::line('Themenseite ' . $obj->ID );
			$counter =  get_post_meta( $obj->ID, 'themengruppen', false );
			add_post_meta( $obj->ID, '_themengruppen', 'field_5dcbd9475d2ca' );

			for ( $i = 0; $i < $counter[0] ; $i++ ) {
				WP_CLI::line('  - gruppe ' . $i );
				add_post_meta( $obj->ID, 'themengruppen_'.$i."_", '' );
				add_post_meta( $obj->ID, '_themengruppen_'.$i."_", 'field_5e1464123c271' );
			}
		}
	}

	static public function depublizierung() {
	    Materialpool_Material::depublizierung();
    }
}


WP_CLI::add_command( 'materialpool convert themenseiten', array( 'Materialpool_WP_CLI_Command','convert_themenseiten' ) );
WP_CLI::add_command( 'materialpool convert themenseiten2', array( 'Materialpool_WP_CLI_Command','convert_themenseiten2' ) );
WP_CLI::add_command( 'materialpool convert material', array( 'Materialpool_WP_CLI_Command','convert_material' ) );
WP_CLI::add_command( 'materialpool convert material2', array( 'Materialpool_WP_CLI_Command','convert_material2' ) );
WP_CLI::add_command( 'materialpool convert material3', array( 'Materialpool_WP_CLI_Command','convert_material3' ) );
WP_CLI::add_command( 'materialpool convert material4', array( 'Materialpool_WP_CLI_Command','convert_material4' ) );
WP_CLI::add_command( 'materialpool convert material5', array( 'Materialpool_WP_CLI_Command','convert_material5' ) );
WP_CLI::add_command( 'materialpool convert autor', array( 'Materialpool_WP_CLI_Command','convert_autor' ) );
WP_CLI::add_command( 'materialpool convert organisation', array( 'Materialpool_WP_CLI_Command','convert_organisation' ) );
WP_CLI::add_command( 'materialpool convert startseite', array( 'Materialpool_WP_CLI_Command','convert_startseite' ) );
WP_CLI::add_command( 'materialpool depublizierung', array( 'Materialpool_WP_CLI_Command','depublizierung' ) );