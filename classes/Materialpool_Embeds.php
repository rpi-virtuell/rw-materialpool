<?php

/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */

class Materialpool_Embeds
{
	/**
     * uses apply_filters( 'oembed_default_width', int width)
	 * @param $size
	 * @param $url
     *
	 */
    static public function rest_pre_echo_response($data,  $server,  WP_REST_Request  $request ){
	    if( function_exists( 'get_current_screen')){

		    $current_screen = get_current_screen();
		    if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			    $is_block_editor = true;
		    }else{
			    $is_block_editor = false;
		    }



		    if($is_block_editor=== false){
			    $post = get_post(url_to_postid($request->get_param('url')));
			    $data['width'] = 1000;
			    $data['height'] = 700;
			    $data['html'] = str_replace('<blockquote class="wp-embedded-content">',
				    '<blockquote class="wp-embedded-content" style="display: none;">',
				    get_post_embed_html(1000, 750, $post));

		    }

	    }
	    return $data;


    }
	/**
     *
     * @since 0.0.1
     * @access	public
     * @param $html
     * @return string
     */
    static public function site_title_html( $html ) {
	    return '';
        return "<img width='24' height='24' src='". Materialpool::$plugin_url ."/assets/rpi-logo-100-100.jpg' style='float:left; margin-right:10px'>";
    }

    static public function the_permalink( $permalink ) {
    	global $post;
	    if($post->post_type == "material" && is_embed()){
		    return $permalink.'?direct='.Materialpool_Material::get_url();
	    }
	    return $permalink;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @param $output
     * @return string
     */
    static public function the_excerpt_embed( $output ) {
        global $post;

        if ( $post->post_type == 'material' ) {

        	$height = 52;

	        $thumbnail_url = Materialpool_Material::get_cover();

	        $material_url = get_permalink().'?direct='.Materialpool_Material::get_url();





	        $thumbnail_url = !empty($thumbnail_url)? $thumbnail_url : 'https://dev-material.rpi-virtuell.de/wp-content/uploads/2017/04/cropped-library-2128813_1920.jpg';

	        //$thumbnail_url ='#';

	        $output  = '<div style="height:'.($height+8).'vw; overflow:auto;max-width: 99vw">';
	        $output .= '<details><summary style="background-color: #dddddd; border:1px solid #c0c0c0; height:24px">';
	        $output .= '<img width="24" height="24" src="'. Materialpool::$plugin_url .'/assets/rpi-logo-100-100.jpg" style="float:left; margin-right:10px">Infos zum Material';
	        $output .= '</summary><div>';
	        //$output .= '<h3>'.get_the_title().'</h3>';
	        $output .= '<p style="valign: top; font-weight: bold;">';
	        $output .= Materialpool_Material::get_shortdescription() ;
	        $output .= '</p><p>';
	        $output .= Materialpool_Material::get_description(100);
	        $output .= '</p>';
	        $output .= '</div>';
	        $output .= '<div style="border:1px solid #c0c0c0; padding: 0 10px; margin: 5px 0 ">'.self::get_meta(). '</div>';
	        $output .= '<div style="clear: both;"></div>';
	        $output .= '</details>';

	        $output .= '<a href="'.$material_url.'" target="_blank">';
	        $output .= '<span style="display:block; width:100%; height:'.$height.'vw;background-image:url(\''.$thumbnail_url.'\'); background-repeat: no-repeat; background-position: left top; background-size: cover;overflow: hidden;"></span>';
	        $output .= '</a>';
	        $output .= '</div><style>.wp-embed-footer{margin-top: -15px;}</style>';
	        //$output = '';


	        Materialpool_Statistic::log( $post->ID, $post->post_type );
        }
        if ( $post->post_type == 'autor' ) {
            $output= " <div><p style='valign: top;'><img style='width:20%; padding-right: 10px; padding-bottom: 10px;  align: left; float: left;' src='". Materialpool_Autor::get_picture() ."'><strong>Das neueste Material</strong><br><br>". Materialpool_Autor::get_materialien_html( 5 ) ."</p></div><div style='clear: both;'></div>";
	        Materialpool_Statistic::log( $post->ID, $post->post_type );
        }
        if ( $post->post_type == 'organisation' ) {
            $output= " <div><p style='valign: top;'><img style='width:20%; padding-right: 10px; padding-bottom: 10px;  align: left; float: left;' src='". Materialpool_Organisation::get_logo() ."'><strong>Das neueste Material</strong><br><br>". Materialpool_Organisation::get_materialien_html( 5 ) ."</p></div><div style='clear: both;'></div>";
	        Materialpool_Statistic::log( $post->ID, $post->post_type );
        }
        return $output;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     */
    static public function embed_content_meta() {

	    global $post;
	    $material_url = get_permalink().'?direct='.Materialpool_Material::get_url();
	    $material = get_permalink();

	    //echo '<a href class="button" href="'.$material.'" target="_top">Materialpool</a>';


    }

    static public function embed_content() {
        global $post;

        if ( $post->post_type == 'material' ) {


        	$url = Materialpool_Material::get_url();


        }
    }
	static function get_meta() {

    	$output = '';



    	$autor = Materialpool_Material::get_autor_html();

    	if($autor){
			    $output .= '<strong>Von: </strong> '.$autor.'<br />';
	    }

		$medientypen = Materialpool_Material::get_medientypen();
		if ( $medientypen ) {
			$output .= "<strong>Medientype(n):</strong> ".$medientypen;
		}
		$bildungsstufen = Materialpool_Material::get_bildungsstufen();
		if($bildungsstufen){
			$output .= " · <strong>Bildungsstufe(n):</strong> ";
			$output .= $bildungsstufen;

		}

		return $output;

	}

	static public function print_embed_sharing_dialog(){
		if ( is_404() ) {
			return;
		}
		?>
		<div class="wp-embed-share-dialog hidden" role="dialog" aria-label="<?php esc_attr_e( 'Sharing options' ); ?>">
			<div class="wp-embed-share-dialog-content">
				<div class="wp-embed-share-dialog-text">
					<ul class="wp-embed-share-tabs" role="tablist">
						<li class="wp-embed-share-tab-button wp-embed-share-tab-button-wordpress" role="presentation">
							<button type="button" role="tab" aria-controls="wp-embed-share-tab-wordpress" aria-selected="true" tabindex="0"><?php esc_html_e( 'OEmbed' ); ?></button>
						</li>
						<li class="wp-embed-share-tab-button wp-embed-share-tab-button-html" role="presentation">
							<button type="button" role="tab" aria-controls="wp-embed-share-tab-html" aria-selected="false" tabindex="-1"><?php esc_html_e( 'HTML Embed' ); ?></button>
						</li>
					</ul>
					<div id="wp-embed-share-tab-wordpress" class="wp-embed-share-tab" role="tabpanel" aria-hidden="false">
						<input type="text" value="<?php echo get_the_permalink(); ?>" class="wp-embed-share-input" aria-describedby="wp-embed-share-description-wordpress" tabindex="0" readonly/>

						<p class="wp-embed-share-description" id="wp-embed-share-description-wordpress">
							<?php _e( 'Copy and paste this URL into your WordPress site to embed' ); ?>
						</p>
					</div>
					<div id="wp-embed-share-tab-html" class="wp-embed-share-tab" role="tabpanel" aria-hidden="true">
						<textarea class="wp-embed-share-input" aria-describedby="wp-embed-share-description-html" tabindex="0" readonly><?php echo esc_textarea( get_post_embed_html( 600, 400 ) ); ?></textarea>

						<p class="wp-embed-share-description" id="wp-embed-share-description-html">
							<?php _e( 'Copy and paste this code into your site to embed' ); ?>
						</p>
					</div>
				</div>

				<button type="button" class="wp-embed-share-dialog-close" aria-label="<?php esc_attr_e( 'Close sharing dialog' ); ?>">
					<span class="dashicons dashicons-no"></span>
				</button>
			</div>
		</div>
		<?php
	}
}
