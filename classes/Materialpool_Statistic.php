<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 21.02.17
 * Time: 15:37
 */
class Materialpool_Statistic
{
    /**
     *
     */
    static public function log( $post_id, $post_type  ) {
        global $wpdb;
        $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
        $timestamp = time();

        $wpdb->query( $wpdb->prepare( " 
            INSERT INTO $wpdb->mp_stats 
            (  `object`, `day`, `hour`, `month`, `year`, `posttype`, `dayofweek`  )
            VALUES ( %d,%s,%s,%s,%s,%s, %d  )
            ",
            $post_id,
            date( "d", $timestamp ),
            date( "H", $timestamp ),
            date( "m", $timestamp ),
            date( "Y", $timestamp ),
            $post_type,
            date( "w", $timestamp )
        ));
    }

    /**
     *
     */
    static public function log_autor(  $autor  ) {
        global $wpdb;
        $wpdb->mp_stats_autor = $wpdb->prefix . 'mp_stats_autor';
        $timestamp = time();

        if(!empty($autor)){
        $wpdb->query( $wpdb->prepare( " 
            INSERT INTO $wpdb->mp_stats_autor 
            (  `object`, `day`, `hour`, `month`, `year`, `dayofweek`  )
            VALUES ( %d,%s,%s,%s,%s, %d  )
            ",
            $autor[0],
            date( "d", $timestamp ),
            date( "H", $timestamp ),
            date( "m", $timestamp ),
            date( "Y", $timestamp ),
            date( "w", $timestamp )
        ));
        }
    }

    /**
     *
     */
    static public function log_organisation(  $organisation  ) {
        global $wpdb;
        $wpdb->mp_stats_organisation = $wpdb->prefix . 'mp_stats_organisation';
        $timestamp = time();

        $wpdb->query( $wpdb->prepare( " 
            INSERT INTO $wpdb->mp_stats_organisation 
            (  `object`, `day`, `hour`, `month`, `year`, `dayofweek`  )
            VALUES ( %d,%s,%s,%s,%s, %d  )
            ",
            $organisation[0],
            date( "d", $timestamp ),
            date( "H", $timestamp ),
            date( "m", $timestamp ),
            date( "Y", $timestamp ),
            date( "w", $timestamp )
        ));
    }

    static public function log_api_request( $response, $post, $request  ) {
	    Materialpool_Statistic::log( $post->ID, $post->post_type );
        return $response;
    }

    /**
     *
     */
    static public function material7() {
        global $wpdb;
        $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
        for ( $i = 7; $i >= 0; $i-- ) {
            $tag = idate("d", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $monat = idate("m", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $jahr = idate("Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $query = $wpdb->prepare( "SELECT COUNT(*) as anzahl FROM {$wpdb->mp_stats} WHERE day=%d and month=%d and year=%d and posttype=%s ",
                $tag, $monat, $jahr, 'material'
            );
            $results = (array) $wpdb->get_results(  $query , ARRAY_A );
            $anzahl[] = $results[ 0 ][ 'anzahl' ];
            $datum[] =  '"' . date("d.m.Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y"))) . '"';

        }
        ?>
        <canvas id="MaterialCount7" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("MaterialCount7");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Materialabrufe',
                            data: [<?php echo implode( ',', $anzahl ); ?>],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            });
        </script>

        <?php
    }

    /**
     *
     */
    static public function thema7() {
        global $wpdb;
        $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
        for ( $i = 7; $i >= 0; $i-- ) {
            $tag = idate("d", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $monat = idate("m", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $jahr = idate("Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $query = $wpdb->prepare( "SELECT COUNT(*) as anzahl FROM {$wpdb->mp_stats} WHERE day=%d and month=%d and year=%d and posttype=%s ",
                $tag, $monat, $jahr, 'themenseite'
            );
            $results = (array) $wpdb->get_results(  $query , ARRAY_A );
            $anzahl[] = $results[ 0 ][ 'anzahl' ];
            $datum[] =  '"' . date("d.m.Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y"))) . '"';

        }
        ?>
        <canvas id="ThemenCount7" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("ThemenCount7");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Themenseitenabrufe',
                            data: [<?php echo implode( ',', $anzahl ); ?>],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            });
        </script>

        <?php
    }



    /**
     *
     */
    static public function autoren7() {
        global $wpdb;
        $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
        for ( $i = 7; $i >= 0; $i-- ) {
            $tag = idate("d", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $monat = idate("m", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $jahr = idate("Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $query = $wpdb->prepare( "SELECT COUNT(*) as anzahl FROM {$wpdb->mp_stats} WHERE day=%d and month=%d and year=%d and posttype=%s ",
                $tag, $monat, $jahr, 'autor'
            );
            $results = (array) $wpdb->get_results(  $query , ARRAY_A );
            $anzahl[] = $results[ 0 ][ 'anzahl' ];
            $datum[] =  '"' . date("d.m.Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y"))) . '"';

        }
        ?>
        <canvas id="AutorCount7" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("AutorCount7");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Autorenabrufe',
                            data: [<?php echo implode( ',', $anzahl ); ?>],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            });
        </script>

        <?php
    }

    /**
     *
     */
    static public function organisationen7() {
        global $wpdb;
        $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
        for ( $i = 7; $i >= 0; $i-- ) {
            $tag = idate("d", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $monat = idate("m", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $jahr = idate("Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $query = $wpdb->prepare( "SELECT COUNT(*) as anzahl FROM {$wpdb->mp_stats} WHERE day=%d and month=%d and year=%d and posttype=%s ",
                $tag, $monat, $jahr, 'organisation'
            );
            $results = (array) $wpdb->get_results(  $query , ARRAY_A );
            $anzahl[] = $results[ 0 ][ 'anzahl' ];
            $datum[] =  '"' . date("d.m.Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y"))) . '"';

        }
        ?>
        <canvas id="OrgaCount7" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("OrgaCount7");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Organisationsabrufe',
                            data: [<?php echo implode( ',', $anzahl ); ?>],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            });
        </script>

        <?php
    }



    /**
     *
     */
    static public function material_wochentag() {
        global $wpdb;
        $wochentag = array(
                'Sonntag',
                'Montag',
                'Dienstag',
                'Mittwoch',
                'Donnerstag',
                'Freitag',
                'Sonnabend'
        );
        $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
        for ( $i = 0; $i <7;  $i++ ) {
            $query = $wpdb->prepare( "SELECT COUNT(*) as anzahl FROM {$wpdb->mp_stats} WHERE dayofweek=%d and posttype=%s ",
                 $i, 'material'
            );
            $results = (array) $wpdb->get_results(  $query , ARRAY_A );
            $anzahl[] = $results[ 0 ][ 'anzahl' ];
            $datum[] =  '"' . $wochentag[ $i] . '"';

        }
        ?>
        <canvas id="MaterialWochentag" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("MaterialWochentag");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Material Wochentag',
                            data: [<?php echo implode( ',', $anzahl ); ?>],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            });
        </script>

        <?php
    }

    /**
     *
     */
    static public function material_monate() {
        global $wpdb;
        $wochentag = array(
            'Januar',
            'Februar',
            'März',
            'April',
            'Mai',
            'Juni',
            'Juli',
            'August',
            'September',
            'Oktober',
            'November',
            'Dezember'
        );
        $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
        for ( $i = 0; $i <12;  $i++ ) {
            $query = $wpdb->prepare( "SELECT COUNT(*) as anzahl FROM {$wpdb->mp_stats} WHERE month=%d and posttype=%s ",
                $i+1, 'material'
            );
            $results = (array) $wpdb->get_results(  $query , ARRAY_A );
            $anzahl[] = $results[ 0 ][ 'anzahl' ];
            $datum[] =  '"' . $wochentag[ $i] . '"';

        }
        ?>
        <canvas id="MaterialMonat" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("MaterialMonat");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Material Monat',
                            data: [<?php echo implode( ',', $anzahl ); ?>],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            });
        </script>

        <?php
    }

    /**
     *
     */
    static public function materialautoren7() {
        global $wpdb;
        $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
        for ( $i = 7; $i >= 0; $i-- ) {
            $tag = idate("d", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $monat = idate("m", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
            $jahr = idate("Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));

            $query = $wpdb->prepare( "SELECT object, count( *) as anzahl FROM {$wpdb->mp_stats} WHERE day=%d and month=%d and year=%d  group by object ",
                $tag, $monat, $jahr
            );
            $results = (array) $wpdb->get_results(  $query , ARRAY_A );

            $anzahl[] = $results[ 0 ][ 'anzahl' ];
            $datum[] =  '"' . date("d.m.Y", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y"))) . '"';

        }
        echo "comming soon";
    }


	/**
	 * cronjob
     * calc material, autor und organiastion views
     * write results into the wp_postmeta
     */
    static  public function material_meta_update(){

        global $wpdb;
	    $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';

	    $args = array(
		    'post_type' => 'material',
		    'post_status' => 'publish',
		    'posts_per_page' => -1,
	    );
	    $posts = get_posts($args);
	    foreach ($posts as $post){

		    $query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->mp_stats} WHERE object = %d",
			    $post->ID
		    );
		    $counted = $wpdb->get_var(  $query );

		    update_post_meta($post->ID,'material_views',$counted);

		    //var_dump($counted);

	    }


	    $args = array(
		    'post_type' => 'autor',
		    'post_status' => 'publish',
		    'posts_per_page' => -1,
	    );
	    $autoren = get_posts($args);
	    if(is_array($autoren)){
		    foreach ($autoren as $autor){

			    $as = array(
				    'post_type' => 'material',
				    'post_status' => 'publish',
				    'posts_per_page' => -1,
				    'meta_query' => array(
					    array(
						    'key' => 'material_autoren',
						    'value' => '"'.$autor->ID.'"',
						    'compare' => 'LIKE',
					    )
				    )
			    );
			    $mats = get_posts($as);
			    if(is_array($mats)){
				    update_post_meta($autor->ID, 'autor_material_count', count($mats));

				    $counted = 0;
				    foreach ($mats as $m){
					    $counted = intval(get_post_meta($m->ID,'material_views', true )) + $counted;
				    }
				    update_post_meta($autor->ID, 'autor_material_views', $counted);

				    $query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->mp_stats} WHERE object = %d",
					    $autor->ID
				    );
				    $counted = $wpdb->get_var(  $query );
				    update_post_meta($autor->ID, 'autor_views', $counted);
			    }

		    }
	    }


	    $args = array(
		    'post_type' => 'organisation',
		    'post_status' => 'publish',
		    'posts_per_page' => -1,
	    );
	    $organisationen = get_posts($args);
	    if(is_array($organisationen)){
		    foreach ($organisationen as $organisation){

			    $as = array(
				    'post_type' => 'material',
				    'post_status' => 'publish',
				    'posts_per_page' => -1,
				    'meta_query' => array(
					    array(
						    'key' => 'material_organisation',
						    'value' => '"'.$organisation->ID.'"',
						    'compare' => 'LIKE',
					    )
				    )
			    );
			    $mats = get_posts($as);
			    if(is_array($mats)){
				    update_post_meta($organisation->ID, 'organisation_material_count', count($mats));

				    $counted = 0;
				    foreach ($mats as $m){
					    $counted = intval(get_post_meta($m->ID,'material_views', true )) + $counted;
				    }
				    update_post_meta($organisation->ID, 'organisation_material_views', $counted);

				    $query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->mp_stats} WHERE object = %d",
					    $organisation->ID
				    );
				    $counted = $wpdb->get_var(  $query );
				    update_post_meta($organisation->ID, 'organisation_views', $counted);
			    }

		    }
	    }


    }

}
