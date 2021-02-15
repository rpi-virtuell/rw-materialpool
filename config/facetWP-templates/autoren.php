
<?php while ( have_posts() ) : the_post(); ?>
    <div class="entry-content autor facet-treffer">
        <div class="autor-content autor-archiv">
            <h2><a href="<?php echo get_permalink( $id); ?>"><?php Materialpool_Autor::firstname();?> <?php Materialpool_Autor::lastname();?></a></h2>
			<?php

			$anzahl = Materialpool_Autor::get_count_posts_per_autor();
			if ( $anzahl != '' && $anzahl != 0 ) {
				echo "Anzahl der Beiträge im Materialpool: ".$anzahl."<br>";
			}
			$url = Materialpool_Autor::get_url();
			if ( '' != $url) {
				?>
                <br>Internet: <a href="<?php echo $url; ?>"><?php echo $url;?></a><br>
				<?php
			}

			$organisationen =  Materialpool_Autor::get_organisationen();

			if ( is_array( $organisationen ) && $organisationen != false ) {
				echo "<br>Arbeitet mit bei:<br>";
				foreach ( $organisationen as $organisation ) {
					$url = get_permalink( $organisation );
					$post = get_post( $organisation);
					echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-volumes', 'materialpool-template-material-volumes' ) .'">' . $post->post_title . '</a><br>';
				}
			}
			?>
        </div>
    </div>
<?php endwhile; ?>
