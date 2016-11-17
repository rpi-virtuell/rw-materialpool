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
        URL: <?php Materialpool_Material::url_html(); ?><br>
        Kurzbeschreibung: <p><?php Materialpool_Material::shortdecription(); ?> </p><br>
        Beschreibung: <p><?php Materialpool_Material::decription(); ?> </p><br>
        <p>Verfügbarkeit: <?php Materialpool_Material::availability(); ?> </p><br>
        Veröffentlichungsdatum: <?php Materialpool_Material::releasedate(); ?><br>
        Depublizierungsdatum: <?php Materialpool_Material::depublicationdate(); ?><br>
        Wiedervorlagedatum: <?php Materialpool_Material::reviewdate(); ?><br>
        Erstellungsdatum: <?php Materialpool_Material::createdate(); ?><br>
        Cover: <?php Materialpool_Material::picture_html(); ?><br>
<hr>
        Bestandteil eines Werks: <?php Materialpool_Material::werk_html(); ?><br>
        Weitere Bände des Werks (ohne den aktuellen Band) : <?php Materialpool_Material::sibling_volumes_html(); ?><br>
<hr>

        <?php if ( Materialpool_Material::is_werk() ) { ?>
        Dies ist ein Werk. Folgende Bände sind zugeordnet<br>
            <?php Materialpool_Material::volumes_html( true ); ?><br>
        <?php } ?>


        <?php if ( Materialpool_Material::is_part_of_werk() ) { ?>
            Dieser Band ist teil eines Werks. Folgende Bände umfasst das Werk.<br>
            <?php Materialpool_Material::sibling_volumes_html( true ); ?><br>
        <?php } ?>

        <br>
        Verweise: <br>
        <?php Materialpool_Material::verweise_html(); ?>
    </div>
</section>

<?php get_footer( 'materialpool' ); ?>
