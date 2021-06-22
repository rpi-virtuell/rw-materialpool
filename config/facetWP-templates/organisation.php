
<?php while ( have_posts() ) : the_post(); ?>
	<div class="entry-content autor facet-treffer">
		<div class="autor-content">
			<h2><a href="<?php echo get_permalink( ); ?>"><?php Materialpool_Organisation::title();?></a></h2>
			<?php
			$anzahl = Materialpool_Organisation::get_count_posts_per_organisation();
			if ( $anzahl != '' && $anzahl != 0 ) {
				echo "Anzahl Materialien: ".$anzahl."<br>";
			}
			$url = Materialpool_Organisation::get_url();
			if ( '' != $url) {
				?>
				<br>Internet: <a href="<?php echo $url; ?>"><?php echo $url;?></a><br>
				<?php
			}

			?>
		</div>
	</div>
<?php endwhile; ?>
