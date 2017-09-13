
<?php while ( have_posts() ) : the_post(); ?>
	<div class="entry-content autor">
		<div class="autor-left">
			<?php if(Materialpool_Organisation::get_logo()):?>
				<div class="autor-image" >
					<img src="<?php Materialpool_Organisation::logo(); ?>"><br>
				</div>
			<?php endif; ?>
		</div>
		<div class="autor-content">
			<h2><a href="<?php echo get_permalink( $id); ?>"><?php Materialpool_Organisation::title();?></a></h2>

		</div>
	</div>
<?php endwhile; ?>