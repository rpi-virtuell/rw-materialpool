<?php while ( have_posts() ): the_post(); ?>
	<span class="thema-title"><a href="<?php the_permalink(); ?>"><?php $title = get_the_title(); $title = str_replace( 'Themenseite ', '', $title); echo $title; ?></a></span>
<?php endwhile; ?>
