<?php
/**
 * The Template for displaying s single author
 *
 * This template can be overridden by copying it to yourtheme/materialpool/single-autor.php.
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
        <H1>Autorenseite</H1>
        <h2>Name</h2>
        <?php Materialpool_Autor::firstname(); ?> <?php Materialpool_Autor::lastname(); ?><br>
        <h2>Daten</h2>
        Web: <?php Materialpool_Autor::url_html(); ?><br>
        BuddyPress: <?php Materialpool_Autor::buddypress_html(); ?><br>
        Email: <?php Materialpool_Autor::email_html(); ?><br>
        Bild: <?php Materialpool_Autor::picture_html(); ?><br>
        <br>
        <br>
        Organisationen des Autors:<br>
        <?php Materialpool_Autor::organisationen_html(); ?>
        <br>
        <br>
        Material des Autors:<br>
        <?php Materialpool_Autor::materialien_html(); ?>

    </div>
</section>

<?php get_footer( 'materialpool' ); ?>
