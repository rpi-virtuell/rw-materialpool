<?php
/**
 * The Template for displaying a single organisation
 *
 * This template can be overridden by copying it to yourtheme/materialpool/single-organisation.php.
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
        <H1>Organisationsseite</H1>
        <h2>Name</h2>
        <?php Materialpool_Organisation::title(); ?><br>
        <h2>Daten</h2>
        Web: <?php Materialpool_Organisation::url_html(); ?><br>
        Bild: <?php Materialpool_Organisation::logo_html(); ?><br>
        ALPIKA: <?php if ( Materialpool_Organisation::is_alpika() ) { echo 'Ja'; } else { echo "Nein"; } ?><br>
        Konfession: <?php Materialpool_Organisation::konfession(); ?><br>
        Material dieser Organisation: <br>
        <?php Materialpool_Organisation::material_html(); ?>
    </div>
</section>

<?php get_footer( 'materialpool' ); ?>
