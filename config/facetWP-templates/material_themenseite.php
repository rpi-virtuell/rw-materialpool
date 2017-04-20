<?php while ( have_posts() ) : the_post(); ?>
    <?php
    if ( false === ( $transient = get_transient( 'facet_themenseite_entry-'.$post->ID ) ) ) {
        ob_start();

        ?>
    <div class="facet-treffer<?php echo (Materialpool_Material::is_alpika())?' alpika':'';?><?php echo (Materialpool_Material::is_special())?' special':'';?>">
        <div class="facet-treffer-content">
            <?php if (Materialpool_Material::cover_facet_html()  && !in_array(strrchr(Materialpool_Material::get_url(),'.'),array('.pdf','.docx','.doc','.odt')) ):?>
                <div class="material-cover">
                    <?php echo  Materialpool_Material::cover_facet_html(); ?>
                </div>
            <?php endif; ?>
            <p class="material-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
            <p class="search-description">
                <?php Materialpool_Material::shortdescription(); ?>
                <?php //echo wp_trim_words(  wp_strip_all_tags ( Materialpool_Material::get_description() )) ; ?>
            </p>
            <div class="facet-tags">
                <?php echo Materialpool_Material::get_bildungsstufen(); ?>
            </div>
            <p class="search-head">
                <?php if(Materialpool_Material::get_organisation()[0]){
                    echo Materialpool_Material::organisation_facet_html().'<br>';
                }
                if(Materialpool_Material::get_autor()) {
                    echo Materialpool_Material::autor_facet_html();
                }
                ?>
            </p>
        </div><div class="clear"></div>

    </div>
        <?php
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
        set_transient( 'facet_themenseite_entry-'.$post->ID, $buffer );
    } else {
        echo $transient;
    }
    ?>
<?php endwhile; ?>