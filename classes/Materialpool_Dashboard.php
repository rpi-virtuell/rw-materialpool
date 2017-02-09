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
        wp_enqueue_script( 'rw-materialpool-chat', Materialpool::$plugin_url . 'js/chart.bundle.min.js' );
?>

        <div class="wrap">

        <?php self::welcome(); ?>

        </div>
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

    /**
     *
     */
	static public function welcome() {
	    $material = wp_count_posts( 'material');
	    $autoren = wp_count_posts( 'autor');
	    $orga = wp_count_posts( 'organisation');
	    ?>
        <div id="welcome-panel" class="welcome-panel">
            <div class="welcome-panel-content">
                <h2>Willkommen im Materialpool!</h2>
                <br>
                Material: <?php echo $material->publish; ?> <br>
                Autoren: <?php echo $autoren->publish; ?><br>
                Organisationen: <?php echo $orga->publish; ?><br>
                <br>
            </div>
        </div>

        <?php self::material_count(); ?>
        <?php self::autor_count(); ?>
        <?php self::organisation_count(); ?>

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
        <canvas id="MaterialChart" width="400" height="100"></canvas>
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
        <canvas id="AutorChart" width="400" height="100"></canvas>
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
        <canvas id="OrgaChart" width="400" height="100"></canvas>
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

}