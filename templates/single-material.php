<?php
/**
 * The Template for displaying all single material
 *
 * This template can be overridden by copying it to yourtheme/materialpool/single-material.php.
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
        <H1>Materialseite</H1>
        Titel: <?php Materialpool_Material::title(); ?> <br>
        Kurzbeschreibung: <p><?php Materialpool_Material::shortdecription(); ?> </p><br>
        Beschreibung: <p><?php Materialpool_Material::decription(); ?> </p><br>
        Veröffentlichungsdatum: <?php Materialpool_Material::releasedate(); ?><br>
        Depublizierungsdatum: <?php Materialpool_Material::depublicationdate(); ?><br>
        Wiedervorlagedatum: <?php Materialpool_Material::reviewdate(); ?><br>
        Erstellungsdatum: <?php Materialpool_Material::createdate(); ?><br>
        Cover: <?php Materialpool_Material::picture_html(); ?><br>
        Bestandteil eines Werks: <?php Materialpool_Material::werk_html(); ?><br>
        Weitere Bände des Werks: <?php Materialpool_Material::sibling_volumes(); ?><br>





    </div>
</section>

<?php get_footer( 'materialpool' ); ?>
