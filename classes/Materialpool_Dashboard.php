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
		$args = array(
			'page_title' => _x( 'Materialpool', 'Page title', Materialpool::get_textdomain() ),
			'menu_title' => _x( 'Materialpool', 'Menu title', Materialpool::get_textdomain() ),
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
            'mp-search-stat',         // Widget slug.
            'Suchanfragen',         // Title.
            array( 'Materialpool_Dashboard', 'searchwp_widget') // Display function.
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


        wp_enqueue_script( 'dashboard' );
        $title =" Materialpool";
        $classes = "welcome-panel";
        $material = wp_count_posts( 'material');
        $autoren = wp_count_posts( 'autor');
        $orga = wp_count_posts( 'organisation');
        ?>

        <div class="wrap">
            <h1><?php echo esc_html( $title ); ?></h1>

                <div id="welcome-panel" class="<?php echo esc_attr( $classes ); ?>">
                    <div style="float: left; padding-right: 20px;" >
                        Material: <?php echo $material->publish; ?> <br>
                        Autoren: <?php echo $autoren->publish; ?><br>
                        Organisationen: <?php echo $orga->publish; ?><br>
                    </div>
                    <div style="float: left; padding-right: 20px;" >
                        Material mit Wiedervorlage: <?php echo Materialpool_Material::review_count(); ?> <br>
                        Material mit Depublizierung: <?php echo Materialpool_Material::depublication_count(); ?> <br>
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
    static public function searchwp_widget() {
        $stats = new SearchWP_Stats();
        $searchesPerDay = $stats->get_search_counts_per_day( 20, "default" );
        $anzahl = array();
        $datum = array();
        foreach ( $searchesPerDay as $key => $value ) {
            $anzahl[] = $value;
            $datum[] = '"'.$key.'"';
        }
        ?>
        <canvas id="SearchChart" width="400" height="200"></canvas>
        <script>
            jQuery(document).ready(function(){
                var ctx = document.getElementById("SearchChart");
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [ <?php echo implode( ',', $datum ); ?> ],
                        datasets: [{
                            label: '# Suchen',
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
        $stats = new SearchWP_Stats();

        $back = $stats->get_popular_searches(array(
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
        $stats = new SearchWP_Stats();

        $back = $stats->get_popular_searches(array(
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
        $stats = new SearchWP_Stats();

        $back = $stats->get_popular_searches(array(
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
        $stats = new SearchWP_Stats();

        $back = $stats->get_popular_searches(array(
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
        $stats = new SearchWP_Stats();

        $back = $stats->get_popular_searches(array(
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
        $stats = new SearchWP_Stats();

        $back = $stats->get_popular_searches(array(
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
        $result = $wpdb->get_results( $wpdb->prepare( "SELECT * , DATE_FORMAT ( meta_value, '%%d.%%m.%%y' ) AS datum   FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value != '0000-00-00' order by $wpdb->postmeta.meta_value  asc  limit 0, 20" , 'material_depublizierungsdatum' )  );
        foreach ( $result as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Material</th><th style='width: 20%;' >Datum</th></tr>";
            }
            echo "<tr><td><a href='". get_permalink( $obj->ID) ."'>" . $obj->post_title . "</a></td><td>" . $obj->datum ."</td></tr>";
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
        $result = $wpdb->get_results( $wpdb->prepare( "SELECT * , DATE_FORMAT ( meta_value, '%%d.%%m.%%y' ) AS datum   FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value != '0000-00-00' order by $wpdb->postmeta.meta_value  asc  limit 0, 20" , 'material_wiedervorlagedatum' )  );
        foreach ( $result as $obj ) {
            if ($count == 0 ) {
                echo "<table><tr><th style='width: 80%;'>Material</th><th style='width: 20%;' >Datum</th></tr>";
            }
            echo "<tr><td><a href='". get_permalink( $obj->ID) ."'>" . $obj->post_title . "</a></td><td>" . $obj->datum ."</td></tr>";
            $count++;
        }
        if ( $count > 0) {
            echo "</table>";
        }
    }
}
