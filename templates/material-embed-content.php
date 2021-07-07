<?php
/**
 * Contains the post embed content template part
 *
 * When a post is embedded in an iframe, this file is used to create the content template part
 * output if the active theme does not include an material-embed-content.php template.
 *
 * @package WordPress
 * @subpackage Theme_Compat
 * @since 4.5.0
 */
get_header( 'embed' );

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
        ?>
            <div <?php post_class( 'wp-embed' ); ?>>
                <?php

                $thumbnail_url = Materialpool_Material::get_cover();
                $material_url = Materialpool_Material::get_url();

                $ratio = apply_filters( 'embed_materialpool_image_ratio', $ratio= 7/16 );

                $width = 100;
                $height = intval($width * $ratio);
                ?>


                <p class="wp-embed-heading">
                    <a href="<?php echo Materialpool_Material::get_url()  ?>" target="_blank">
                        <?php the_title(); ?>
                    </a>
                </p>
                <details style="border: 1px solid #c0c0c0;">
                    <summary style="background-color: #dddddd">
                        <div style="float: right;margin-top: 0px;"><?php echo print_embed_sharing_button(); ?></div> Informationen zu diesem Material<?php the_embed_site_title(); ?>
                        <div style="clear: both; width: 100%; height: 0px;">&nbsp;</div>
                    </summary>
                    <div style="padding: 10px;">
                        <p></p>
                        <a href="<?php the_permalink(); ?>" target="_top" style="font-weight: bold">
				            <?php the_title(); ?>
                        </a>
			            <?php the_excerpt_embed(); ?>
			            <?php
			            /**
			             * Prints additional content after the embed excerpt.
			             *
			             * @since 4.4.0
			             */
			            do_action( 'embed_content' );
			            ?>

                    </div>
                </details>
                <!--<a href="<?php echo $material_url; ?>" target="ya_blank" style="display: block">-->
                <?php if ( $thumbnail_url && false) : ?>
                        <div class="wp-embed-featured-image" style="width:100%; height:<?php echo $height;?>vw;background-image:url('<?php echo $thumbnail_url;?>'); background-repeat: no-repeat; background-position: center center; background-size: cover;overflow: hidden;"></div>
                <?php else: ?>
                    <div class="wp-embed-featured-image" style="width:100%; height:<?php echo $height;?>vw;background-image:url('https://gutenberg.rpi-virtuell.de/wp-content/uploads/2021/03/024-test.png'); background-repeat: no-repeat; background-position: center center; background-size: cover;overflow: hidden;"></div>
                <?php endif; ?>
                <!--</a>-->
                <div class="wp-embed-footer" style="margin-top: -20px">

                    <div class="wp-embed-meta"><?php
                        /**
                         * Prints additional meta content in the embed template.
                         *
                         * @since 4.4.0
                         */
                        do_action( 'embed_content_meta' );
                        ?>
                    </div>
                </div>
            </div>
        <?php
	endwhile;
else :
	get_template_part( 'embed', '404' );
endif;

get_footer( 'embed' );

