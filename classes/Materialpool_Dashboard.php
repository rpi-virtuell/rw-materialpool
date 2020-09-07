<?php
/**
 * The plugin dashboard class.
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 */


class Materialpool_Dashboard {

	/**
	 * Register the plugin dashboard page
	 *
	 * @since   0.0.1
	 * @filter    materialpool_dashboard_page_args
	 *
	 */
	static public function register_dashboard_page() {
	    $count = '';
        $counts = Materialpool_Material::submit_count();
		$counts = $counts + Materialpool_Contribute::submit_count();

		if ( $counts > 0 ) {
		    $count = "<span class='update-plugins count-". $counts . "'><span class='plugin-count'>" . number_format_i18n($counts) . "</span></span>";
		}
		$args = array(
			'page_title' => _x( 'Materialpool', 'Page title', Materialpool::get_textdomain() ),
			'menu_title' => _x( 'Materialpool ' . $count  , 'Menu title', Materialpool::get_textdomain() ),
			'capability' => 'edit_posts',
			'menu_slug' => 'materialpool',
			'callback' => array(
				'Materialpool_Dashboard',
				'dashboard_page_content'
			),
			'icon' => 'dashicons-images-alt2',
			'menu_position' => 25
		);
		$args = apply_filters( 'materialpool_dashboard_page_args', $args );
		add_menu_page(
			$args[ 'page_title' ],
			$args[ 'menu_title' ],
			$args[ 'capability' ],
			$args[ 'menu_slug' ],
			$args[ 'callback' ],
			$args[ 'icon' ],
			$args[ 'menu_position' ]
		);


	}


	/**
	 * Plugin Dashboard Content
	 *
	 * @since   0.0.1
	 */
	static public function dashboard_page_content() {
        global $wp_meta_boxes;
        $screen = get_current_screen();


        wp_enqueue_script( 'rw-materialpool-chart', Materialpool::$plugin_url . 'js/chart.bundle.min.js' );

        require_once(ABSPATH . 'wp-admin/includes/dashboard.php');

        wp_dashboard_setup();

        $wp_meta_boxes['material']['normal']['core'] = array();
        $wp_meta_boxes['material']['side']['core'] = array();
        $wp_meta_boxes['toplevel_page_materialpool']['normal']['core'] = array();
        $wp_meta_boxes['toplevel_page_materialpool']['side']['core'] = array();

        /**
         * Register Material Dashboard Widgets
         */
        wp_add_dashboard_widget(
            'mp-material-count',         // Widget slug.
            'Materialien Anzahl',         // Title.
            array( 'Materialpool_Dashboard', 'material_count') // Display function.
        );
        wp_add_dashboard_widget(
            'mp-autor-count',         // Widget slug.
            'AutorInnen Anzahl',         // Title.
            array( 'Materialpool_Dashboard', 'autor_count') // Display function.
        );
        wp_add_dashboard_widget(
            'mp-orga-count',         // Widget slug.
            'Organisationen Anzahl',         // Title.
            array( 'Materialpool_Dashboard', 'organisation_count') // Display function.
        );
        
        wp_add_dashboard_widget(
            'mp-search-stat-1',         // Widget slug.
            'Suchanfragen Heute',         // Title.
            array( 'Materialpool_Dashboard', 'searchwp_poular_search_1_widget') // Display function.
        );
        wp_add_dashboard_widget(
            'mp-search-stat-7',         // Widget slug.
            'Suchanfragen 7 Tage',         // Title.
            array( 'Materialpool_Dashboard', 'searchwp_poular_search_7_widget') // Display function.
        );
        wp_add_dashboard_widget(
            'mp-search-stat-30',         // Widget slug.
            'Suchanfragen 30 Tage',         // Title.
            array( 'Materialpool_Dashboard', 'searchwp_poular_search_30_widget') // Display function.
        );
        wp_add_dashboard_widget(
            'mp-search-fail-stat-1',         // Widget slug.
            'Fehlgeschlagene Suchanfragen Heute',         // Title.
            array( 'Materialpool_Dashboard', 'searchwp_failed_search_1_widget') // Display function.
        );

        wp_add_dashboard_widget(
            'mp-search-fail-stat-7',         // Widget slug.
            'Fehlgeschlagene Suchanfragen 7 Tage',         // Title.
            array( 'Materialpool_Dashboard', 'searchwp_failed_search_7_widget') // Display function.
        );

        wp_add_dashboard_widget(
            'mp-search-fail-stat-30',         // Widget slug.
            'Fehlgeschlagene Suchanfragen 30 Tage',         // Title.
            array( 'Materialpool_Dashboard', 'searchwp_failed_search_30_widget') // Display function.
        );
        wp_add_dashboard_widget(
            'mp-depublizierungen',         // Widget slug.
            'Depublizierungen',         // Title.
            array( 'Materialpool_Dashboard', 'depublizierungen') // Display function.
        );
        wp_add_dashboard_widget(
            'mp-wiedervorlagen',         // Widget slug.
            'Wiedervorlagen',         // Title.
            array( 'Materialpool_Dashboard', 'wiedervorlage') // Display function.
        );

        wp_add_dashboard_widget(
            'mp-material-7',         // Widget slug.
            'Materialabrufe 7 Tage',         // Title.
            array( 'Materialpool_Statistic', 'material7') // Display function.
        );

        wp_add_dashboard_widget(
            'mp-thema-7',         // Widget slug.
            'Themenseitenabrufe 7 Tage',         // Title.
            array( 'Materialpool_Statistic', 'thema7') // Display function.
        );

        wp_add_dashboard_widget(
            'mp-autoren-7',         // Widget slug.
            'Autorenseitenabrufe 7 Tage',         // Title.
            array( 'Materialpool_Statistic', 'autoren7') // Display function.
        );

        wp_add_dashboard_widget(
            'mp-organisaion-7',         // Widget slug.
            'Organisationsseitenabrufe 7 Tage',         // Title.
            array( 'Materialpool_Statistic', 'organisationen7') // Display function.
        );

        wp_add_dashboard_widget(
            'mp-material-wochentag',         // Widget slug.
            'Materialabrufe nach Wochentag',         // Title.
            array( 'Materialpool_Statistic', 'material_wochentag') // Display function.
        );

        wp_add_dashboard_widget(
            'mp-material-monat',         // Widget slug.
            'Materialabrufe nach Monat',         // Title.
            array( 'Materialpool_Statistic', 'material_monate') // Display function.
        );

        wp_add_dashboard_widget(
            'mp-notcomplete',         // Widget slug.
            'Unvollständiges Material',         // Title.
            array( 'Materialpool_Dashboard', 'not_complete') // Display function.
        );

		wp_add_dashboard_widget(
			'mp-autor',         // Widget slug.
			'Autorenaktivität',         // Title.
			array( 'Materialpool_Dashboard', 'autor') // Display function.
		);


     //   wp_add_dashboard_widget(
     //       'mp-materialautoren7',         // Widget slug.
     //       'Meistgelesene Autoren',         // Title.
     //       array( 'Materialpool_Statistic', 'materialautoren7') // Display function.
     //   );

        wp_enqueue_script( 'dashboard' );
        $title =" Materialpool";
        $classes = "welcome-panel";
        $material = wp_count_posts( 'material');
        $autoren = wp_count_posts( 'autor');
        $orga = wp_count_posts( 'organisation');
        $synonyme = wp_count_posts( 'synonym');
        $themenseiten = wp_count_posts( 'themenseite');
        ?>

        <div class="wrap">
            <h1><?php echo esc_html( $title ); ?></h1>

                <div id="welcome-panel" class="<?php echo esc_attr( $classes ); ?>">
                    <div style="float: left; padding-right: 20px;" >
                        Material: <?php echo $material->publish; ?> <br>
                        Autoren: <?php echo $autoren->publish; ?><br>
                        Organisationen: <?php echo $orga->publish; ?><br>
                        Themenseiten: <?php echo $themenseiten->publish; ?><br>
                        Synonyme: <?php echo $synonyme->publish; ?><br>

                    </div>
                    <div style="float: left; padding-right: 20px;" >
                        Material mit Wiedervorlage: <?php echo Materialpool_Material::review_count(); ?> <br>
                        Material mit Depublizierung: <?php echo Materialpool_Material::depublication_count(); ?> <br>
                        <?php
                        if ( Materialpool_Material::submit_count() > 0 ) {
	                        ?>
                            <a href="<?php echo admin_url("edit.php?post_status=vorschlag&post_type=material"); ?>">Materialvorschläge zur Überprüfung: <?php echo Materialpool_Material::submit_count(); ?></a><br>
	                        <?php
                        }
                        ?>
	                    <?php
	                    if ( Materialpool_Contribute::submit_count() > 0 ) {
		                    ?>
                            <a href="<?php echo admin_url("admin.php?page=rw-materialpool%2Fclasses%2FMaterialpool_Contribute.php2"); ?>">Autorenverknüpfungen zur Überprüfung: <?php echo Materialpool_Contribute::submit_count(); ?></a><br>
		                    <?php
	                    }
	                    ?>
                    </div>

                    <div style="float: left; padding-right: 20px;">
                        <a href="<?php echo admin_url( 'index.php?page=searchwp-stats' ); ?>">Suchstatistik</a><br>
                        <a href="<?php echo admin_url( 'index.php?page=wysija_campaigns' ); ?>">Newsletter</a><br>
                    </div>

                    <div style="clear: both"></div>
                    <br>

                    <?php
                    do_action( 'materialpool_welcome_panel' );
                    ?>
                </div>

            <div id="dashboard-widgets-wrap">
                <?php
                wp_dashboard(); ?>
            </div><!-- dashboard-widgets-wrap -->
        </div><!-- wrap -->
        <?php
	}

    /**
     * @param string $posttype
     * @param $date
     * @return array
     */
	static public function  count( $posttype = "post", $date ) {
        global $wpdb;

        $query = $wpdb->prepare( "SELECT COUNT(*) as anzahl FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' AND post_date < %s ",
            $posttype, $date
        );
        $results = (array) $wpdb->get_results(  $query , ARRAY_A );
        return $results;
    }


    static public function material_review() {
        ?>
        <div id="material-review-panel" class="welcome-panel material-review-panel">
            <div class="material-review-panel-content">
                <h2>Material auf Wiedervorlage</h2>

            </div>
        </div>

        <?php
    }

    /**
     *
     */
    static public function material_count() {
        for ( $i = 19; $i >= 0; $i-- ) {
            $date = mktime (0, 0,0, date("n"), date("j")- ( $i * 7 ) , date("Y"));
            $back = ( self::count( 'material', date("Y-m-d", $date) ) );
            $anzahl[] = $back[0][ 'anzahl' ];
            $datum[] = '"'.date("d.m", $date).'"';
        }
        ?>
        <canvas id="MaterialChart" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("MaterialChart");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Material',
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
    static public function autor_count() {
        for ( $i = 19; $i >= 0; $i-- ) {
            $date = mktime (0, 0,0, date("n"), date("j")- ( $i * 7 ) , date("Y"));
            $back = ( self::count( 'autor', date("Y-m-d", $date) ) );
            $anzahl[] = $back[0][ 'anzahl' ];
            $datum[] = '"'.date("d.m", $date).'"';
        }
        ?>
        <canvas id="AutorChart" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("AutorChart");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Autoren',
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
    static public function organisation_count() {
        for ( $i = 19; $i >= 0; $i-- ) {
            $date = mktime (0, 0,0, date("n"), date("j")- ( $i * 7 ) , date("Y"));
            $back = ( self::count( 'organisation', date("Y-m-d", $date) ) );
            $anzahl[] = $back[0][ 'anzahl' ];
            $datum[] = '"'.date("d.m", $date).'"';
        }
        ?>
        <canvas id="OrgaChart" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("OrgaChart");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Organisationen',
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
    static public function searchwp_poular_search_1_widget() {
         $back = \SearchWP\Statistics::get_popular_searches(array(
                'days'      => 1,
                'engine'    => 'default',
                'limit'     => 50,
            )

        );
        $count = 0;
        foreach ( $back as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Begriff</th><th style='width: 20%;' >Anzahl</th></tr>";
            }
            echo "<tr><td>" . $obj->query . "</td><td>" . $obj->searchcount ."</td></tr>";
           $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }

    /**
     *
     */
    static public function searchwp_poular_search_7_widget() {


        $back = \SearchWP\Statistics::get_popular_searches(array(
                'days'      => 7,
                'engine'    => 'default',
                'limit'     => 50,
            )

        );
        $count = 0;
        foreach ( $back as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Begriff</th><th style='width: 20%;' >Anzahl</th></tr>";
            }
            echo "<tr><td>" . $obj->query . "</td><td>" . $obj->searchcount ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }

    /**
     *
     */
    static public function searchwp_poular_search_30_widget() {


        $back = \SearchWP\Statistics::get_popular_searches(array(
                'days'      => 30,
                'engine'    => 'default',
                'limit'     => 50,
            )

        );
        $count = 0;
        foreach ( $back as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Begriff</th><th style='width: 20%;' >Anzahl</th></tr>";
            }
            echo "<tr><td>" . $obj->query . "</td><td>" . $obj->searchcount ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }


    /**
     *
     */
    static public function searchwp_failed_search_1_widget() {


        $back = \SearchWP\Statistics::get_popular_searches(array(
                'days'      => 1,
                'engine'    => 'default',
                'limit'     => 50,
                'min_hits'  => false,
                'max_hits'  => 0
            )

        );
        $count = 0;
        foreach ( $back as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Begriff</th><th style='width: 20%;' >Anzahl</th></tr>";
            }
            echo "<tr><td>" . $obj->query . "</td><td>" . $obj->searchcount ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }

    /**
     *
     */
    static public function searchwp_failed_search_7_widget() {

        $back = \SearchWP\Statistics::get_popular_searches(array(
                'days'      => 7,
                'engine'    => 'default',
                'limit'     => 50,
                'min_hits'  => false,
                'max_hits'  => 0
            )

        );
        $count = 0;
        foreach ( $back as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Begriff</th><th style='width: 20%;' >Anzahl</th></tr>";
            }
            echo "<tr><td>" . $obj->query . "</td><td>" . $obj->searchcount ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }

    /**
     *
     */
    static public function searchwp_failed_search_30_widget() {


        $back = \SearchWP\Statistics::get_popular_searches(array(
                'days'      => 30,
                'engine'    => 'default',
                'limit'     => 50,
                'min_hits'  => false,
                'max_hits'  => 0
            )

        );
        $count = 0;
        foreach ( $back as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Begriff</th><th style='width: 20%;' >Anzahl</th></tr>";
            }
            echo "<tr><td>" . $obj->query . "</td><td>" . $obj->searchcount ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }

    /**
     *
     */
    static public function depublizierungen() {
        global $wpdb;
        $count = 0;
        $result = $wpdb->get_results( $wpdb->prepare( "SELECT * , DATE_FORMAT( meta_value, '%%d.%%m.%%y' ) AS datum   FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value != '0000-00-00' AND $wpdb->postmeta.meta_value != ''AND $wpdb->posts.post_status = 'publish' order by $wpdb->postmeta.meta_value  asc  limit 0, 20" , 'material_depublizierungsdatum' )  );
        foreach ( $result as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Material</th><th style='width: 20%;' >Datum</th></tr>";
            }
            echo "<tr><td><a href='". get_edit_post_link( $obj->ID) ."'>" . $obj->post_title . "</a></td><td>" . $obj->datum ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }

    /**
     *
     */
    static public function wiedervorlage() {
        global $wpdb;
        $count = 0;
        $result = $wpdb->get_results( $wpdb->prepare( "SELECT * , DATE_FORMAT( meta_value, '%%d.%%m.%%y' ) AS datum   FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value != '0000-00-00' AND $wpdb->postmeta.meta_value != '' AND $wpdb->posts.post_status = 'publish' order by $wpdb->postmeta.meta_value  asc  limit 0, 20" , 'material_wiedervorlagedatum' )  );
        foreach ( $result as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Material</th><th style='width: 20%;' >Datum</th></tr>";
            }
            echo "<tr><td><a href='". get_edit_post_link( $obj->ID) ."'>" . $obj->post_title . "</a></td><td>" . $obj->datum ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }


    /**
     *
     */
    static public function not_complete() {
        global $wpdb;
        $count = 0;
        $result = $wpdb->get_results("
        SELECT distinct( $wpdb->posts.ID ) , $wpdb->posts.post_title, DATE_FORMAT( post_date, '%d.%m.%y' ) AS datum  FROM 
	$wpdb->posts, $wpdb->postmeta 
WHERE 
	$wpdb->posts.ID = $wpdb->postmeta.post_id AND  
	$wpdb->posts.post_type = 'material' AND
	( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' )  AND
(
	( 
	(
	   not exists( select * from wp_postmeta where meta_key='material_schlagworte' and post_id = wp_posts.ID )
	 OR  
		( 
			wp_postmeta.meta_key = 'material_schlagworte' AND 
			wp_postmeta.meta_value = ''  
		)
		) 
	)
OR
	( 
		$wpdb->postmeta.meta_key = 'material_url' AND 
 			$wpdb->postmeta.meta_value = ''  
	)
OR
	( 
		$wpdb->postmeta.meta_key = 'material_beschreibung' AND 
 			$wpdb->postmeta.meta_value = ''  
	)
OR
	( 
		$wpdb->postmeta.meta_key = 'material_kurzbeschreibung' AND 
 			$wpdb->postmeta.meta_value = ''  
	)
OR	
	( 
	   not exists( select * from $wpdb->postmeta where meta_key='material_medientyp' and post_id = $wpdb->posts.ID )
	 OR  
		( 
			$wpdb->postmeta.meta_key = 'material_medientyp' AND 
			$wpdb->postmeta.meta_value = ''  
		)
	)
	OR	
	( 
	   not exists( select * from $wpdb->postmeta where meta_key='material_bildungsstufe' and post_id = $wpdb->posts.ID )
	 OR  
		( 
			$wpdb->postmeta.meta_key = 'material_bildungsstufe' AND 
			$wpdb->postmeta.meta_value = ''  
		)
	)
)	
order by wp_posts.post_date  asc ") ;
        foreach ( $result as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Material</th><th style='width: 20%;' >Datum</th></tr>";
            }
            echo "<tr><td><a href='". get_edit_post_link( $obj->ID) ."'>" . $obj->post_title . "</a></td><td>" . $obj->datum ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }

    static public function autor() {
        global $wpdb;

	    $result = $wpdb->get_results( $wpdb->prepare( "SELECT distinct( post_author) FROM $wpdb->posts WHERE $wpdb->posts.post_type = %s " , 'material' )  );
        echo "<table  style='width: 100%'><tr><th style='width: 40%'>Autoren</th><th style='width: 20%'>";
        echo date( 'F', mktime(0, 0, 0, date("m")-2  , date("d"), date("Y")) );
        echo "</th><th style='width: 20%'>";
	    echo date( 'F', mktime(0, 0, 0, date("m")-1  , date("d"), date("Y")) );
	    echo "</th><th style='width: 20%'>";
	    echo date( 'F', mktime(0, 0, 0, date("m")  , date("d"), date("Y")) );
	    echo "</th></tr>";
	    foreach ( $result as $obj ) {
           $user = get_user_by( 'ID', $obj->post_author );
           echo "<tr><td>";
           echo $user->display_name;
           echo "</td><td>";
		    $start =  date( 'Y-m-d', mktime(0, 0, 0, date("m")-2  , 0, date("Y")) );
		    $end =  date( 'Y-m-d', mktime(0, 0, 0, date("m")-1  , 1, date("Y")) );
		    $result = $wpdb->get_results( $wpdb->prepare( "SELECT count( post_ID) as anzahl FROM $wpdb->posts , $wpdb->postmeta WHERE $wpdb->posts.post_type = %s  and $wpdb->posts.post_author = %d  and $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s and $wpdb->postmeta.meta_value < %s and $wpdb->postmeta.meta_value > %s and ( $wpdb->posts.post_status = 'publish' or  $wpdb->posts.post_status = 'check')" , 'material', $obj->post_author, 'create_date', $end, $start )  );
		    echo $result[0]->anzahl;
  		    echo "</td><td>";
		    $start =  date( 'Y-m-d', mktime(0, 0, 0, date("m")-1  , 0, date("Y")) );
		    $end =  date( 'Y-m-d', mktime(0, 0, 0, date("m")  , 1, date("Y")) );
		    $result = $wpdb->get_results( $wpdb->prepare( "SELECT count( post_ID) as anzahl FROM $wpdb->posts , $wpdb->postmeta WHERE $wpdb->posts.post_type = %s  and $wpdb->posts.post_author = %d  and $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s and $wpdb->postmeta.meta_value < %s and $wpdb->postmeta.meta_value > %s and ( $wpdb->posts.post_status = 'publish' or  $wpdb->posts.post_status = 'check')" , 'material', $obj->post_author, 'create_date', $end, $start )  );
		    echo $result[0]->anzahl;
		    echo "</td><td>";
		    $start =  date( 'Y-m-d', mktime(0, 0, 0, date("m")  , 0, date("Y")) );
		    $end =  date( 'Y-m-d', mktime(0, 0, 0, date("m")+1  , 1, date("Y")) );
		    $result = $wpdb->get_results( $wpdb->prepare( "SELECT count( post_ID) as anzahl FROM $wpdb->posts , $wpdb->postmeta WHERE $wpdb->posts.post_type = %s  and $wpdb->posts.post_author = %d  and $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s and $wpdb->postmeta.meta_value < %s and $wpdb->postmeta.meta_value > %s and ( $wpdb->posts.post_status = 'publish' or  $wpdb->posts.post_status = 'check')" , 'material', $obj->post_author, 'create_date', $end, $start )  );
		    echo $result[0]->anzahl;
		   echo "</td></tr>";
	    }

        echo "</table>";
    }
}
