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
		if( isset( $wp->query_vars[ '__rwmpapi' ] ) ) {
			Materialpool_Contribute::handle_request();
			exit;
		}
	}


	static public function log($content){
		if ( WP_DEBUG  ) {
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


		$request = json_decode( stripslashes( $wp->query_vars[ 'data' ] ) );
		if( ! $request || !isset($request->cmd) || !isset($request->data) ) {

			Materialpool_Contribute::send_response('Please send commands in json.');

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
			self::log('say_hello');


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

	public static function settings_init() {

		register_setting( 'rw_materialpool_contribute_options', 'rw_materialpool_contribute_options_whitelist_active' );
		register_setting( 'rw_materialpool_contribute_options', 'rw_materialpool_contribute_options_whitelist' );

	}


	public static function options_page() {
		add_submenu_page(
			'materialpool',
			_x('Materialpool Settings', Materialpool::$textdomain, 'Page Title' ),
			_x('Settings', Materialpool::$textdomain, 'Menu Title' ),
			'manage_options',
			__FILE__,
			array( 'Materialpool_Contribute', 'create_options' )
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


}



