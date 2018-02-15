<?php

/**
 * Class RW_Remote_Auth_Server_Options
 *
 * Manage APY-Keys for  RW_Remote_Auth_Clients
 *
 * Author: Joachim Happel
 * Date: 26.02.2016
 * Time: 11:09
 *
 * @since 0.2.0
 */
class Materialpool_Contribute_Clients
{

	public static $post_type = 'mp_contribute_key';
	public static $post_type_slug = false;
	/**
	 *
	 * @useaction init
	 */
	public static function init(){

		/* MUST be called on the init action/callback (will not work properly if not called at right time/place */

		add_action('init', array('Materialpool_Contribute_Clients' , 'create_apikey_cpt' ) );

	}

	/**
	 * get clients domain
	 */
	static public function detect_server_client(){

		$str = $_SERVER['HTTP_USER_AGENT'];
		$str .= "|";
		$str .= $_SERVER['REMOTE_ADDR'];
		$str .= "\n";
		file_put_contents( Materialpool::$plugin_dir.'/clients.log', $str,FILE_APPEND );
		//die();
	}

	/**
	 * Create custom Post Type to store Cients and API Keys
	 */
	static public function create_apikey_cpt(){
		$args = array(
			'labels' => array(
				'name' => __('Server Clients'),
				'singular_name' => __('Server Client')
			),
			'public' => false, /* shows in admin on left menu etc */
			'has_archive' => false,

		);

		register_post_type( self::$post_type, $args);

	}

	/**
	 *  run on deactivation (however it doesn't seem to delete custom posts )
	 */
	static public function uninstall_apikey_cpt() {

		global $wp_post_types;

		$post_type = self::$post_type;
		$slug = self::$post_type_slug;

		if (isset($wp_post_types[$post_type])) {

			unset($wp_post_types[$post_type]);

			$slug = (!$slug ) ? 'edit.php?post_type=' . $post_type : $slug;
			remove_menu_page($slug);
		}

	}



	protected static function toggle_host_link($id,$current_status){

		?> <a href="<?php print wp_nonce_url(admin_url('admin.php?page=rw-materialpool%2Fclasses%2FMaterialpool_Contribute.php&id='.$id.'&status='.$current_status), 'toggle_host', 'rw-remote-auth-server_clients_nonce');?>"
		      class="button button-primary"><?php echo ($current_status == 'active')?  __('Disable'):__('Enable');  ?></a> <?php
	}
	protected static function toggle_host($id,$current_status){

		if( !is_nan($id) < 1 && is_string($current_status) ){
			//error
		}{
			$status = ($current_status == 'suspended')? 'active':'suspended';
			$args = array(
				'ID'=>$id,
				'post_excerpt'=>$status,
			);
			wp_update_post($args);
		}




	}
	static public function display_clients(){
		if (isset($_GET['rw-remote-auth-server_clients_nonce']) && wp_verify_nonce($_GET['rw-remote-auth-server_clients_nonce'], 'toggle_host')){
			self::toggle_host($_GET['id'],$_GET['status'] );
		}
		?>
		<hr style="margin-top:30px">
		<h1>Clients</h1>
		<style>
			table.rw_remote_auth_server{
				margin-left: 218px;
			}
			.rw_remote_auth_server th{
				border:0;
				background-color: lightgrey;
				border-right:1px solid #666666;
				border-bottom:1px solid #666666;
				font-weight: bolder;
			}
			.rw_remote_auth_server td{
				border-right:1px solid #666666;
				border-bottom:1px solid #666666;
			}
			.rw_remote_auth_server .client {
				font-size: 1.3em;
				line-height: 1.3em;
				min-width:380px;
			}
			.rw_remote_auth_server .status {
				width:50px;
			}
			.rw_remote_auth_server .ip {
				width:50px;
			}
			.rw_remote_auth_server .action {
				width:150px;
			}
			.rw_remote_auth_server .active .status{
				background-color: lightgreen;
			}
			.rw_remote_auth_server .suspended .status{
				background-color: red;
				color:white;
			}
		</style>
		<table class="rw_remote_auth_server" cellpadding="4" cellspacing="0" >
			<tr>
				<th>
					Domain
				</th>
				<th>
					IP
				</th>
				<th colspan="2">
					Status
				</th>
			</tr>
			<?php

			$args = array(
				'post_type'=> self::$post_type,
				'orderby' => 'IP',
				'post_status' => 'any',
				'posts_per_page'=> -1
			);
			$myposts = get_posts( $args );

			foreach ( $myposts as $post ) :
				?>
				<tr class="<?php echo $post->post_excerpt; ?>">
					<td class="client">
						<?php echo $post->post_title; ?>
					</td>
					<td class="ip">
						<?php echo $post->post_content; ?>
					</td>
					<td class="status">
						<?php echo $post->post_excerpt; ?>
					</td>
					<td class="action">
						<?php echo Materialpool_Contribute_Clients::toggle_host_link($post->ID, $post->post_excerpt ); ?>
					</td>
				</tr>
			<?php endforeach;
			?>
		</table>
		<hr style="margin-bottom:30px">
		<?php

	}

}