<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


class Materialpool_Contribute {

	/**
	 * Supported Client
	 *
	 * @var     string
	 * @since   0.2
	 * @access  public
	 */
	public static $client 		= 'RPI_MP_Contribute';
	/**
	 * Minimal required client version
	 *
	 * @var     string
	 * @since   0.2
	 * @access  public
	 */
	public static $client_version = '0.1.0';


	/**
	 * Add API Endpoint
	 *
	 * @since   0.1
	 * @access  public
	 * @static
	 * @return void
	 */
	static public function add_endpoint() {
		$endpoint = "mp_contribute";
		add_rewrite_rule( '^'. $endpoint .'/([^/]*)/?', 'index.php/?__rwmpapi=1&data=$1', 'top');
		flush_rewrite_rules();

	}

	/**
	 *
	 * @since   0.1
	 * @access  public
	 * @static
	 * @param $vars *
	 * @return array
	 */
	static public function add_query_vars( $vars ) {
		$vars[] = '__rwmpapi';
		$vars[] = 'data';
		//self::log( "add_query_vars");
		return $vars;
	}

	/**	Sniff Requests
	 *	This is where we hijack all API requests
	 * 	If $_GET['__api'] is set, we kill WP and serve up pug bomb awesomeness
	 *	@return die if API request
	 *
	 * @since   0.1
	 * @access  public
	 * @static
	 */
	static public function parse_request(){
		global $wp;
		//self::log("parse_request");
		if( isset( $wp->query_vars[ '__rwmpapi' ] ) ) {
			Materialpool_Contribute::handle_request();
			exit;
		}
	}


	static public function log($content){
		if ( false  ) {
			file_put_contents( Materialpool::$plugin_base_dir.'/clients.log' , $content ."\n",FILE_APPEND );
		}
	}



	static public function is_validate_trustet_client($whitelisted = false){

		$error = $set_key = $error_data= false;
		$error_msg = '';

		date_default_timezone_set('Europe/Berlin');

		//write to logfile;
		$str = '['.date('Y-m-d H:i:s').'] '.$_SERVER['REMOTE_ADDR'];

		self::log($str);

		$user_agent = explode(';',$_SERVER['HTTP_USER_AGENT']);
		if(count($user_agent) < 4){
			self::log( 'invalid user agent .'.$_SERVER['HTTP_USER_AGENT'] );
			$error_msg .= __('Invalid user agent.',Materialpool::get_textdomain()) .' '. $_SERVER['HTTP_USER_AGENT'];
			$error = true;
		}else{
			list($version,$host,$ip,$apikey)= $user_agent;

			$client_arr = explode(' ',$version);
			if( count($client_arr) < 2){
				log ( ' invalid arguments in client version ' );
				$error_msg .= __('Invalid arguments in client version ',Materialpool::get_textdomain());
				$error = true;
			}else{
				list($client_class,$release) = $client_arr;
				if(!version_compare($release,Materialpool_Contribute::$client_version,'>=')){
					self::log ( 'too old client version ' . $release);
					$error_msg .= __('You use a deprecated client',Materialpool::get_textdomain()).
					              ' (version '.$release.')  '. __('Please update your MP Contribute Client plugin. ',Materialpool::get_textdomain()) .
					              Materialpool_Contribute::$client_version;
					$error = true;
				}
			}
			$varstr = $version.'|'.$host.'|'.$ip.'|'.$apikey.'|'.$client_class.'|'.$release;
			self::log($varstr);
		}

		//if whitelisting is active we will check  IP and API-Key
		if	(
			!$error 																&&  //check deeper
			get_site_option('rw_materialpool_contribute_options_whitelist_active') 		&&  //whitelisting active
			// $ip  == $_SERVER['REMOTE_ADDR'] 										&&  //real IP correct
			Materialpool_Contribute::$client == $client_class								//Client corret
		)
		{

			global $wpdb;

			self::log('check  IP and API-Key');

			$whitelist = get_option( 'rw_materialpool_contribute_options_whitelist');
			$whitelist = str_replace(' ','' ,$whitelist);
			$whitelist = str_replace("\r",'' ,$whitelist);
			$whitelist = str_replace("\l",'|' ,$whitelist);
			$whitelist = str_replace("\n",'|' ,$whitelist);

			$whitelist_arr = explode("|",$whitelist);



			//is client from a trusted host or ip ?
			if(in_array($ip ,$whitelist_arr ) || in_array($host ,$whitelist_arr ) ){

				self::log('trusted host '.$host);
				if($whitelisted === true){

					return true;
				}

				//validate the clients api_key
				$host =  $wpdb->_real_escape($host);
				$dbkey = $wpdb->get_var("select post_password from $wpdb->posts where post_title = '".$host."' and post_excerpt='active' and post_type='mp_contribute_key'");

				if(!empty($dbkey) && $dbkey == $apikey){
					self::log('trusted client '.$dbkey);
					return true;
				}else{
					self::log('invalid apikey');
					$error = true;
					$set_key = true;
				}

			}else{
				$error = true;
				$error_msg .= __('Your host ist not allowed to connect to this Auth - Server', Materialpool::get_textdomain());

				$set_key = true;
			}
		}

		//automaticly send a new api key to the client an replace the new one with the existing,
		//this will deactivate the current client for security reason
		if($set_key){

			$existing = get_page_by_title( $host, OBJECT, 'mp_contribute_key' );

			if($existing && $existing->post_excerpt != 'active'){
				$admin = get_userdata( 1 );
				if($admin){
					$admin_email = $admin->user_email;
				}else{
					$admin_email = 'unknown';
				}
				//clienst need to be activatet bei the remote_auth_server admin
				$error_msg .= __('Client is suspended by remote service.', Materialpool::get_textdomain()).' '.
				              __('Please ask for (re)activation').': '.$admin_email.'';

			}else{

				$hash = wp_generate_password( 20, true, true );

				$api_key_entry = array(
					'post_title'    => $host,
					'post_content'  => $ip,
					'post_password'	=> $hash,
					'post_excerpt'	=> 'suspended',
					'post_content'	=> $ip,
					'post_author'   => 1,
					'post_type'		=> 'mp_contribute_key'
				);
				if($existing){
					$api_key_entry['ID'] = $existing->ID;
				}
				// Insert new api-key into the database
				if(!wp_insert_post( $api_key_entry )){
					$error_msg .= "Database Error";
				}

				$error = true;
				$error_msg .= __('Invalid API Key! Your Client is suspended. The Admin of the Auth Service may enable your client again.', Materialpool::get_textdomain());
				$error_data = array('mp_contribute_key'=>$hash);
			}


		}
		if($error){
			$error_msg = __('Attention').': '.$error_msg;
			return new WP_Error( 'rw_materialpool_contribute_error',$error_msg ,$error_data);
		}else{
			return true;
		}


	}

	/** Handle Requests
	 *	This is where we send off for an intense pug bomb package
	 *
	 * @since   0.1
	 * @access  public
	 * @static
	 * @return  void
	 */
	static protected function handle_request(){
		global $wp;

		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$request = json_decode( stripslashes( urldecode( $wp->query_vars['data'] ) ) );
		}
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$request =  $_POST ;
 			$request =  json_decode( stripslashes( urldecode( $request[0] ) ) );
		}

		self::log(json_encode($request));
		if( ! $request || !isset($request->cmd) || !isset($request->data) ) {
			Materialpool_Contribute::send_response('Please send commands in json. ' . json_last_error_msg() );
		} else {
			//validate client
			$valid = self::is_validate_trustet_client();

			if(is_wp_error($valid)){

				self::log('validation error message: '.$valid->get_error_message() );

				if ($valid->get_error_data()) {
					$response[ 'errors' ]['data'] = $valid->get_error_data();
					$response[ 'errors' ]['message']=$valid->get_error_message();

				}else{
					$response[ 'errors' ] = $valid->get_error_message();
				}

				$response[ 'message' ] = false;
				$response[ 'data' ] = false;

				self::send_response(
					$response[ 'message' ],
					$response[ 'data' ],
					$response[ 'errors' ]
				);
				exit;
			}
            self::log('apply_filter: rw_materialpool_contribute_cmd_parser');
			apply_filters( 'rw_materialpool_contribute_cmd_parser', $request );
		}
	}


	/**
	 *
	 * @param $msg
	 * @param string $data
	 */
	static protected function send_response($msg, $data = '',$errors = false){
		$response[ 'message' ] = $msg;
		if( $data ) {
			$response['data'] = $data;
		}
		$response['errors'] = $errors;

		header('content-type: application/json; charset=utf-8');
		echo json_encode( $response )."\n";
		self::log('send_response:'.json_encode( $response )."\n---------------------------------------------------------------");
		exit;
	}


	/**
	 * Implements a ping command, to check if rw_auth server is responding
	 *
	 * @since 0.1.3
	 * @param $request
	 *
	 * @return mixed
	 */
	static public function cmd_ping( $request ) {
		if ( 'ping' == $request->cmd ) {
			Materialpool_Contribute::send_response( json_encode( array( 'answer' => 'pong' ) ) );
		}
		return $request;
	}


	/**
	 * Check and validate Connection
	 *
	 * @hook    rw_remote_auth_server_cmd_parser
	 * @param   $request
	 * @return  mixed
	 */
	static public function cmd_say_hello( $request ) {

		self::log('cmd:'. $request->cmd);

		if ( 'say_hello' == $request->cmd ) {


			if(get_site_option('rw_remote_auth_server_options_whitelist_active')){
				$message = __('Connection established. Everything works fine. ', Materialpool::get_textdomain());
				$notice = 'success';
			}else{
				$message = __('Connected', Materialpool::get_textdomain());
				$notice = 'warning';
			}


			$data = array(
				'notice'=> $notice,
				'answer' => $message
			);


			Materialpool_Contribute::send_response(
				$request->cmd,
				$data
			);

		}
		return $request;
	}

	/**
	 * @param   $request
	 * @return  mixed
	 */
	static public function cmd_list_authors( $request ) {
		if ( 'list_authors' == $request->cmd ) {
			self::log('list_authors');

			$args = array(
				'post_type'=> 'autor',
				'orderby' => 'post_title',
				'order' => 'asc',
				'post_status' => 'publish',
				'posts_per_page'=> -1
			);
			$autors = get_posts( $args );

			foreach ($autors as $autor ) {
				$message[] = array(
					'id' => $autor->ID,
					'name' => $autor->post_title,
				);
			}
			$data = array(
				'notice'=> "ok",
				'answer' => $message
			);

			Materialpool_Contribute::send_response(
				$request->cmd ,
				$data,
				false
			);

		}
		return $request;
	}

	/**
	 * @param   $request
	 * @return  mixed
	 */
	static public function cmd_list_bildungsstufen( $request ) {
		if ( 'list_bildungsstufen' == $request->cmd ) {
			self::log('list_bildungsstufen');


			$terms = get_terms( array (
				'taxonomy' => 'bildungsstufe',
			));



			$data = array(
				'notice'=> "ok",
				'answer' => $terms,
			);

			Materialpool_Contribute::send_response(
				$request->cmd ,
				$data,
				false
			);

		}
		return $request;
	}


	/**
	 * @param   $request
	 * @return  mixed
	 */
	static public function cmd_list_altersstufen( $request ) {
		if ( 'list_altersstufen' == $request->cmd ) {
			self::log('list_altersstufen');


			$terms = get_terms( array (
				'taxonomy' => 'altersstufe',
			));

			$data = array(
				'notice'=> "ok",
				'answer' => $terms,
			);

			Materialpool_Contribute::send_response(
				$request->cmd ,
				$data,
				false
			);

		}
		return $request;
	}


	/**
	 * @param   $request
	 * @return  mixed
	 */
	static public function cmd_list_medientypen( $request ) {
		if ( 'list_medientypen' == $request->cmd ) {
			self::log('list_medientypen');


			$terms = get_terms( array (
				'taxonomy' => 'medientyp',
			));

			$data = array(
				'notice'=> "ok",
				'answer' => $terms,
			);

			Materialpool_Contribute::send_response(
				$request->cmd ,
				$data,
				false
			);

		}
		return $request;
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return post_id | false
	 */
	static function get_post_id_by_meta($key, $value) {
		global $wpdb;
		$meta = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='".$wpdb->escape($key)."' AND meta_value='".$wpdb->escape($value)."'");
		if (is_array($meta) && !empty($meta) && isset($meta[0])) {
			$meta = $meta[0];
		}
		if (is_object($meta)) {
			return $meta->post_id;
		}
		else {
			return false;
		}
	}

	/**
	 * @param   $request
	 * @return  mixed
	 */
	static public function cmd_send_post( $request ) {

		if ( 'send_post' == $request->cmd ) {

		    self::log('cmd->send_post');

			global $wpdb;

			$data = false;

			$user =  base64_decode( $request->data->material_user );

            $status = false;
			$query = $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'autor_hash' and meta_value = %s",
				$user
			);
			//self::log('Query1: ' .$query  );
			$autor = $wpdb->get_var(  $query );

			if ( $autor != null ) {



				$material_id      = $request->data->material_id ;
				$url              = base64_decode( $request->data->material_url );
				$title            = base64_decode( $request->data->material_title );
				$shortdescription = base64_decode( $request->data->material_shortdescription );
				$description      = base64_decode( $request->data->material_description );
				$keywords         = base64_decode( $request->data->material_interim_keywords );
				$altersstufe      = base64_decode( $request->data->material_altersstufe );
				$bildungsstufe    = base64_decode( $request->data->material_bildungsstufe );
				$material_screenshot_url = base64_decode( $request->data->material_screenshot );
				$medientyp        = base64_decode( $request->data->material_medientyp );

				self::log( "screen:" . $material_screenshot_url.':' );
				$material_cover_url = '';
				$material_screenshot = '';
				if ( $material_screenshot_url ==  '') {
					$material_screenshot = "https://s.wordpress.com/mshots/v1/" . urlencode( $url ) . "?w=400&h=300";
				} else {
					$material_cover_url = $material_screenshot_url;
				}
				self::log( "screen:" . $material_screenshot.':' );

				// URL Check

                $url         = urldecode( $url );

				self::log('$material_$url: '. $url);

                if(!$material_id){
	                $material_id = self::get_post_id_by_meta('material_url', $url);
                }

				$description = json_decode($description,JSON_HEX_TAG );



				self::log('$description: '. $description);
				self::log('$material_id: '. $material_id);

				$medien = json_decode( $medientyp );

				self::log('medien: '.json_encode($medien));

				$tempArr = array();
				foreach ( $medien as $item ) {
					$term = get_term_by( 'name', $item, 'medientyp' );
					$tempArr[] = $term->term_id;
				}
				self::log('medientypes: '.json_encode($tempArr));
				self::log('$material_id: '.$material_id);
                self::log('medientyp: '.json_encode($medientyp));


				if(!$material_id){

                        $material_id = wp_insert_post( array(
                                'post_author'                  => $autor,
                                'post_title'                    => $title,
                                'post_type'                     => "material",
                        ));

                        update_field( 'material_special',0, $material_id );
                        update_field('material_url',$url, $material_id );

                        update_field('material_titel',$title, $material_id );
                        update_field('material_kurzbeschreibung',$shortdescription, $material_id );
                        update_field('material_beschreibung',$description, $material_id );
                        update_field('material_cover_url',$material_cover_url, $material_id );
                        update_field('material_schlagworte_interim',$keywords, $material_id );
                        update_field('material_screenshot',$material_screenshot, $material_id );
					    update_field('material_autoren',array($autorid), $material_id );

                        $query = $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'autor_status' and meta_value = 'ok' and user_id = %s",
                            $autor
                        );
                        //self::log( 'Query2: ' . $query );
                        $autor = $wpdb->get_var( $query );
                        if ( $autor != null ) {
                            $query = $wpdb->prepare( "SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'autor_link' and  user_id = %s",
                                $autor
                            );
                            self::log( 'Query3: ' . $query );
                            $autorid = $wpdb->get_var( $query );

                        }
					    update_field('material_autoren',array($autorid), $material_id );


					    $alter = json_decode( $altersstufe );
                        self::log('alter : ' .$alter  );
                        $tempArr = array();
                        foreach ( $alter as $item ) {
                            self::log('item: ' .$item  );
                            $term = get_term_by( 'name', $item, 'altersstufe' );
	                        wp_set_post_terms( $material_id, $term->term_id ,'altersstufe');
                            $tempArr[] = $term->term_id;
                        }
                        update_field( 'material_altersstufe', $tempArr, $material_id );
                        unset ( $tempArr );
                        $bildung = json_decode( $bildungsstufe );
                        self::log('bildungsstufe : ' .$bildungsstufe  );
                        $tempArr = array();
                        foreach ( $bildung as $item ) {
                            self::log('bildungsstufe item: ' .$item  );
                            $term = get_term_by( 'name', $item, 'bildungsstufe' );
                            self::log('bildungsstufe cat: ' .$term->term_id  );
	                        wp_set_post_terms( $material_id, $term->term_id ,'bildungsstufe');
                            $tempArr[] = $term->term_id;
                        }
                        update_field( 'material_bildungsstufe', $tempArr, $material_id );
                        unset ( $tempArr );
                        $medien = json_decode( $medientyp );
                        $tempArr = array();
                        foreach ( $medien as $item ) {
                            $term = get_term_by( 'name', $item, 'medientyp' );
	                        wp_set_post_terms( $material_id, $term->term_id,'medientyp' );
                            $tempArr[] = $term->term_id;
                        }
                        update_field( 'material_medientyp', $tempArr, $material_id );
                        unset ( $tempArr );
                        // remove Pods Handverlesen default
                        //$pod->remove_from( 'material_vorauswahl', 2206 );

                        $post_type   = get_post_type( $material_id );
                        $post_parent = wp_get_post_parent_id( $material_id );
                        $post_name   = wp_unique_post_slug( sanitize_title( $title ), $material_id, 'publish', $post_type, $post_parent );

                        wp_publish_post( $material_id );

                        // remove FacetCache
                        if ( class_exists( 'FacetWP_Cache' )) {
                            $facecache = new FacetWP_Cache();
                            $facecache->cleanup( 'all' );
                        }
                        wp_cache_flush(); // to get new permalink
                        $data = array(
                            'notice'=> true,
                            'answer' => array(
                                    'status' => true,
                                    'url' => get_permalink( $material_id),
                                    'id' =>  $material_id,
                            ),
                        );

                        self:self::log(json_encode($data));

                        Materialpool_Contribute::send_response(
                            $request->cmd ,
                            $data,
                            false
                        );
                        return $request;

                }else{

                    update_field('material_titel',$title, $material_id );
	                update_field('material_kurzbeschreibung',$shortdescription, $material_id );
	                update_field('material_beschreibung',$description, $material_id );
	                update_field('material_cover_url',$material_cover_url, $material_id );
	                update_field('material_schlagworte_interim',$keywords, $material_id );
	                update_field('material_screenshot',$material_screenshot, $material_id );

	                if ( class_exists( 'FacetWP_Cache' )) {
		                $facecache = new FacetWP_Cache();
		                $facecache->cleanup( 'all' );
	                }
	                wp_cache_flush(); // to get new permalink
	                $data = array(
		                'notice'=> true,
		                'answer' => array(
			                'status' => true,
			                'url' => get_permalink( $material_id),
			                'id' =>  $material_id,
		                ),
	                );

	                Materialpool_Contribute::send_response(
		                $request->cmd ,
		                $data,
		                false
	                );
	                return $request;
                }
			}


			$data = array(
				'notice'=> $data,
				'answer' => $data,
			);

			Materialpool_Contribute::send_response(
				$request->cmd ,
				$data,
				false
			);

		}
		return $request;
	}


	public static function settings_init() {

		register_setting( 'rw_materialpool_contribute_options', 'rw_materialpool_contribute_options_whitelist_active' );
		register_setting( 'rw_materialpool_contribute_options', 'rw_materialpool_contribute_options_whitelist' );

	}


	public static function options_page() {
		add_submenu_page(
			'materialpool',
			_x('Materialpool Settings', Materialpool::$textdomain, 'Page Title' ),
			_x('Verknüpfte Blogs', Materialpool::$textdomain, 'Menu Title' ),
			'manage_options',
			__FILE__ . '1',
			array( 'Materialpool_Contribute', 'create_options' )
		);


		$count = '';
		$counts = Materialpool_Contribute::submit_count();

		if ( $counts > 0 ) {
			$count = "<span class='update-plugins count-". $counts . "'><span class='plugin-count'>" . number_format_i18n($counts) . "</span></span>";
		}
		add_submenu_page(
			'materialpool',
			_x('Materialpool Autoren Verknüpfungen', Materialpool::$textdomain, 'Page Title' ),
			_x('Autoren Verknüpfungen' . $count , Materialpool::$textdomain, 'Menu Title' ),
			'manage_options',
			__FILE__. '2',
			array( 'Materialpool_Contribute', 'create_autor_options' )
		);


	}


	/**
	 * Generate the options page for the plugin
	 *
	 * @since   0.1
	 * @access  public
	 * @static
	 *
	 * @return  void
	 */
	static public function create_autor_options() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
	    ?>
        <div class="wrap"  id="rwremoteauthserveroptions">
            <h2><?php _e( 'Autorenverknüpfungen', Materialpool::$textdomain ); ?></h2>
            <p><?php _e( 'Verknüpfungen von Entfernten Blogautoren zu Mateerialpool Autoren.', Materialpool::$textdomain ); ?></p>
            <form method="POST" action="options.php"><fieldset class="widefat">
                    <?php
                    $args = array(
                        'meta_query'     => array(
                            array(
                                'key'     => 'autor_link',
                                'compare' => 'EXISTS',
                            ),
                        ),
                    );

                    $user_query = new WP_User_Query( $args );

                    $treffer = $user_query->results;

                    $counter = 0;
                    foreach ($treffer as $user ) {
                        if ( $counter == 0 ) {
                            echo "<table>";
                            echo "<tr><th>User</th><th>Autor</th><th>Status</th><th>Aktionen</th></tr>";
                        }
                        $counter++;
                        $user_id = $user->data->ID;
                        $name = $user->data->user_login;
                        $user_link = get_edit_user_link( $user_id );
                        $autor = get_user_meta( $user_id, 'autor_link', true );
                        $autor_link = '';
                        $autor_frontend = get_permalink( $autor );
                        $autor_post = get_post( $autor );
                        $autor_name = $autor_post->post_title;
                        $status = get_user_meta( $user_id, 'autor_status', true );
	                    $action = '';
	                    switch ( $status ) {
                            case 'pending' :
                                $status_output = "wartend";
                                $action .= "<a data-user=\"{$user_id}\" data-autor=\"{$autor}\" data-action=\"add\" class=\"button contribute\" >Zulassen</a> ";
	                            $action .= "<a data-user=\"{$user_id}\" data-autor=\"{$autor}\" data-action=\"del\" class=\"button contribute\" >Löschen</a>";
                                break;
                            case 'ok' :
                                $status_output = "zugelassen";
	                            $action .= "<a data-user=\"{$user_id}\" data-autor=\"{$autor}\" data-action=\"del\" class=\"button contribute\" >Löschen</a>";
                                break;
                            case 'forbidden' :
                                $status_output = "verboten";
	                            $action .= "<a data-user=\"{$user_id}\" data-autor=\"{$autor}\" data-action=\"del\" class=\"button contribute\" >Löschen</a>";
                                break;
                            default:
                                $status_output = "unbekannt";
	                            $action .= "<a data-user=\"{$user_id}\" data-autor=\"{$autor}\" data-action=\"del\" class=\"button contribute\" >Löschen</a>";
                                break;
                        }

                        ?>
                            <tr id="user-autor">
                                <td><?php echo $name; ?> ( <a href="<?php echo $user_link; ?>" target="_blank">Ansehen</a> )</td>
                                <td><?php echo $autor_name; ?> ( <a href="<?php echo $autor_frontend; ?>" target="_blank">Ansehen</a> ) </td>
                                <td><?php echo $status_output; ?></td>
                                <td><?php echo $action; ?> </td>
                            </tr>
                        <?php
                    }
                    if ( $counter > 0 ) {
                        echo "<table>";
                    }

                    ?>

            </form>
        </div>
        <?php
	}

	/**
	 * Generate the options page for the plugin
	 *
	 * @since   0.1
	 * @access  public
	 * @static
	 *
	 * @return  void
	 */
	static public function create_options() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		?>
		<div class="wrap"  id="rwremoteauthserveroptions">
			<h2><?php _e( 'Remote Contribute Server Options', Materialpool::$textdomain ); ?></h2>
			<p><?php _e( 'Settings for Remote Contribute Server', Materialpool::$textdomain ); ?></p>
			<form method="POST" action="options.php"><fieldset class="widefat">
					<?php
					settings_fields( 'rw_materialpool_contribute_options' );
					//List all clients
					Materialpool_Contribute_Clients::display_clients();

					?>
					<h2>
						<?php echo __('Settings');?>
					</h2>
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="rw_materialpool_contribute_options_whitelist_active"><?php _e( 'Whitelist active', Materialpool::$textdomain ); ?></label>
							</th>
							<td>
								<label for="rw_materialpool_contribute_options_whitelist_active">
									<input id="rw_materialpool_contribute_options_whitelist_active" type="checkbox" value="1" <?php if ( get_option( 'rw_materialpool_contribute_options_whitelist_active' ) == 1 ) echo " checked "; ?>   name="rw_materialpool_contribute_options_whitelist_active">
									<?php _e( 'Activate the whitelist. Only whitelisted hosts can access the API.', Materialpool::$textdomain); ?></label>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="rw_materialpool_contribute_options_whitelist"><?php _e( 'Whitelist', Materialpool::$textdomain ); ?></label>
							</th>
							<td>
								<textarea rows="3" cols="15" aria-describedby="whitelist-description" id="rw_remote_auth_server_options_whitelist" name="rw_materialpool_contribute_options_whitelist" class="large-text code"><?php echo get_option( 'rw_materialpool_contribute_options_whitelist'); ?></textarea>
								<p id="whitelist-description" class="description"><?php _e( 'Whitelisted hosts can access the API. One hostname or ip per line.', Materialpool::$textdomain); ?></p>
							</td>
						</tr>
					</table>

					<br/>
					<input type="submit" class="button-primary" value="<?php _e('Save Changes' )?>" />
			</form>
		</div>
		<?php

	}

	static public function edit_user_profile( $profiluser ) {
	    $hash = get_user_meta( $profiluser->data->ID, 'autor_hash', true );
	    if ( $hash != '' ) {
		    ?>
            <h2>Materialeinlieferung</h2>
            <table class="form-table">
                <tr>
                    <th>Materialkey</th>
                    <td>
                        <p>
                            <label for="configstr"><?php _e( 'Kopiere den folgenden Code und füge ihn in deinem Blog unter Benutzer &gt; Dein Profil  in das Feld Materialkey im Abschnitt Materialpool ein, um Blogbeiträge an den Materialpool zu übermitteln.', Materialpool::$textdomain ); ?></label><br>
                            <input id="configstr" type="text" class="regular-text" value="<?php echo $hash; ?>">
                        </p>
                    </td>
                </tr>
            </table>
		    <?php
	    }
    }

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 * @return  int
	 */
	static public function submit_count() {
		$args = array(
			'meta_query'     => array(
				array(
					'key'     => 'autor_link',
					'compare' => 'EXISTS',
				),
			),
		);

		$user_query = new WP_User_Query( $args );

		$treffer = $user_query->results;

		$counter = 0;
		foreach ($treffer as $user ) {
			$user_id        = $user->data->ID;
			$status         = get_user_meta( $user_id, 'autor_status', true );
			if ( $status == 'pending' ) {
			    $counter++;
            }
		}
		return $counter;

	}


}



