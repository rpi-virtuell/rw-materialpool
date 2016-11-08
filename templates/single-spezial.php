<?php
/**
 * The Template for displaying all single spezials
 *
 * This template can be overridden by copying it to yourtheme/materialpool/single-spezial.php.
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
        <H1>Spezial</H1>
        Titel: <?php Materialpool_Spezial::title(); ?> <br>

        Content:<br>
        <?php Materialpool_Spezial::content(); ?>
        <br><br>
        Veröffentlichungsdatum: <?php Materialpool_Spezial::releasedate(); ?><br>
        Depublizierungsdatum: <?php Materialpool_Spezial::depublicationdate(); ?><br>
        Wiedervorlagedatum: <?php Materialpool_Spezial::reviewdate(); ?><br>
        Erstellungsdatum: <?php Materialpool_Spezial::createdate(); ?><br>
        Cover: <?php Materialpool_Material::picture_html(); ?><br>
    </div>
</section>

<?php get_footer( 'materialpool' ); ?>
