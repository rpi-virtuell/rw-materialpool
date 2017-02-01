<?php
/**
 * The Template for displaying s single Themenseite
 *
 * This template can be overridden by copying it to yourtheme/materialpool/single-themenseite.php.
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 * @version    0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_header( 'materialpool' ); ?>
<section id="primary" class="content-area">
    <div id="content" class="site-content" role="main">
        <?php
        if ( have_posts() ) :
        /* Start the Loop */
        while ( have_posts() ) : the_post(); ?>
            <H1><?php the_title();?></H1>
            <div><?php the_content(); ?></div>


        <?php
        endwhile;
        endif;
        ?>
    </div>
</section>

<?php get_footer( 'materialpool' ); ?>
