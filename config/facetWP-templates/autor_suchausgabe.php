
<?php while ( have_posts() ) : the_post(); ?>
	<div class="entry-content autor">
		<div class="autor-left">
			<?php if(Materialpool_Autor::get_picture()):?>
				<div class="autor-image" >
					<?php Materialpool_Autor::picture_html(); ?><br>
				</div>
			<?php endif; ?>
		</div>
		<div class="autor-content">
			<h2><a href="<?php echo get_permalink( $id); ?>"><?php Materialpool_Autor::firstname();?> <?php Materialpool_Autor::lastname();?></a></h2>
			<h4>Wirkungsbereich</h4>
			<?php Materialpool_Autor::organisationen_html(); ?>

		</div>
	</div>
<?php endwhile; ?>