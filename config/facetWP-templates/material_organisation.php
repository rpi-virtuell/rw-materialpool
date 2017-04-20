<?php while ( have_posts() ) : the_post(); ?>
<?php
if ( false === ( $transient = get_transient( 'facet_organisation_entry-'.$post->ID ) ) ) {
ob_start();

?>
    <div class="facet-treffer">
        <div class="facet-treffer-mediatyps">
            <ul>
                <?php $type = Materialpool_Material::get_mediatyps_root();
                foreach ( $type as $val ) {
                    ?>
                    <li>
                    <span title="<?php echo $val[ 'name' ]; ?>" class="fa-stack fa-2x">
                        <i  class="fa fa-circle fa-stack-2x" style="color: <?php echo $val[ 'farbe' ]; ?>"></i>
                        <i class="fa <?php echo $val[ 'icon' ]; ?> fa-stack-1x icon-weiss"></i>
                    </span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="facet-treffer-content">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php echo Materialpool_Material::rating_facet_html(); ?></h2>
            <p class="search-head">
                <?php echo Materialpool_Material::organisation_facet_html(); ?><br>
                <?php echo Materialpool_Material::autor_facet_html(); ?>
            </p>
            <p class="search-description">
                <?php echo  Materialpool_Material::cover_facet_html(); ?><?php Materialpool_Material::shortdescription(); ?><br>
                <?php echo wp_trim_words(  wp_strip_all_tags ( Materialpool_Material::get_description() )) ; ?>
            </p>
            <div class="facet-tags">
                <?php echo Materialpool_Material::bildungsstufe_facet_html(); ?>
                <?php echo Materialpool_Material::inklusion_facet_html(); ?>

            </div><div style="clear: both;"></div>
            <p class="schlagworte"><strong>Schlagworte: </strong> <?php echo Materialpool_Material::get_schlagworte_html(); ?>

        </div><div class="clear"></div>

    </div>
    <?php
    $buffer = ob_get_contents();
    ob_end_clean();
    echo $buffer;
    set_transient( 'facet_organisation_entry-'.$post->ID, $buffer );
} else {
    echo $transient;
}
    ?>
<?php endwhile; ?>
