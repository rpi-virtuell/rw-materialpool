<?php while ( have_posts() ) : the_post(); ?>

<?php

if ( false === ( $transient = get_transient( 'facet_serach2_entry-'.$post->ID ) ) ) {
ob_start();

?>
<?php if (get_post_type() == 'themenseite'): ?>

<div class="facet-treffer themenseite">
    <h2><a href="<?php the_permalink(); ?>">Themenseite: <?php the_title(); ?></a></h2>
    <div class="themenseite-treffer">
        <div class="themenseite-thumb">
            <img src="<?php echo catch_thema_image() ?>">
        </div>
        <div class="thema-description">
            <p class="thema-excerpt">
				<?php the_excerpt(); ?>
            </p>
        </div>
        <div class="clear"></div>
    </div>


	<?php else: ?>

    <div class="facet-treffer<?php echo (Materialpool_Material::is_alpika()) ? ' alpika' : ''; ?><?php echo (Materialpool_Material::is_special()) ? ' special' : ''; ?>">
        <div class="facet-treffer-mediatyps">
            <ul>
				<?php $type = Materialpool_Material::get_mediatyps_root();
				foreach ($type as $val) {
					?>
                    <li>
							<span title="<?php echo $val['name']; ?>" class="fa-stack fa-2x">
								<i class="fa fa-circle fa-stack-2x" style="color: <?php echo $val['farbe']; ?>"></i>
								<i class="fa <?php echo $val['icon']; ?> fa-stack-1x icon-weiss"></i>
							</span>
                    </li>
				<?php } ?>
            </ul>
        </div>
        <div class="facet-treffer-content">

            <h2>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php echo Materialpool_Material::rating_facet_html(); ?>
            </h2>

            <p class="search-head">
				<?php if (Materialpool_Material::get_organisation()[0]) {
					echo Materialpool_Material::organisation_facet_html() . '<br>';
				}
				if (Materialpool_Material::get_autor()) {
					echo Materialpool_Material::autor_facet_html();
				}
				?>
            </p>
            <p class="search-description">
				<?php echo Materialpool_Material::cover_facet_html(); ?>
            <p class="material-shortdescription"><?php Materialpool_Material::shortdescription(); ?></p>
			<?php echo wp_trim_words(wp_strip_all_tags(Materialpool_Material::get_description())); ?>
            </p>
            <p><?php
				if ( Materialpool_Material::get_picture_source() != '' ) {
					if ( Materialpool_Material::get_picture_url() != '') {
						echo  "Bildquelle: <a href='". Materialpool_Material::get_picture_url() ."'>".Materialpool_Material::get_picture_source()."</a>";
					} else {
						echo  "Bildquelle: ".Materialpool_Material::get_picture_source();
					}
				} else {
					if ( Materialpool_Material::get_picture_url() != '') {
						$host = parse_url(Materialpool_Material::get_picture_url() );

						if ( $host[ 'host' ] )
							echo  "Bildquelle: <a href='". Materialpool_Material::get_picture_url() ."'>". $host[ 'host' ] ."</a>";
					}
				}
				?>
            </p>
            <div class="facet-tags">
				<?php echo Materialpool_Material::bildungsstufe_facet_html(); ?>
				<?php echo Materialpool_Material::inklusion_facet_html(); ?>

            </div>
            <div style="clear: both;"></div>
            <p class="schlagworte">
                <strong>Schlagworte: </strong> <?php echo Materialpool_Material::get_schlagworte_html(); ?>
        </div>

		<?php endif;
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
		set_transient( 'facet_serach2_entry-'.$post->ID, $buffer );
		} else {
			echo $transient;
		}
		?>
		<?php if (is_user_logged_in() ) { ?>

            <div style="float: right;">

                <span id="themenseitenedit_<?php echo $post->ID; ?>" data-materialid="<?php echo $post->ID; ?>" data-materialtitel="<?php echo $post->post_title; ?>" data-materialurl="<?php echo get_permalink( $post->ID) ; ?>" class="themenseitenedit btn-neutral"><i class="fas fa-ellipsis-v"> </i></span>
            </div>
            <div style="clear: both;"></div>
		<?php } ?>
        <div class="clear"></div>

    </div>


	<?php endwhile; ?>
    <script>
        jQuery(document).ready(function(){

            if (typeof  jQuery.contextMenu != 'undefined' && typeof  themenseiten != 'undefined' && Object.keys(themenseiten).length !== 0) {
                jQuery.contextMenu({
                    selector: '.themenseitenedit',
                    trigger: 'left',
                    build: function ($trigger, e) {
                        var items = new Object();
                        items['titel_' + themenseiten[0].id ] = {name: "Themenseite: " + themenseiten[0].titel}
                        items['sep1'] = "---------";
                        for (var tg in themengruppen) {
                            var material = new Object;
                            material['add_' + themengruppen[tg].id ] = {name: "Material hinzufügen" };
                            material['sep' + tg] = "---------";
                            for (var m in materialien) {
                                if (themengruppen[tg].id == materialien[m].gruppenid) {
                                    material["m_" + materialien[m].materialid] = {name: materialien[m].titel};
                                }
                            }
                            items["tg_" + tg + "_"  + themengruppen[tg].themenid] = {name: themengruppen[tg].titel, items: material};
                        }
                        items['sep2'] = "---------";
                        items['quit'] = {
                            name: "Speichern und Beenden",
                        };
                        return {
                            callback: function (key, opt, $trigger) {
                                res = key.split("_");
                                if ( res[0] == "m") {
                                    // Vorhandenes Material angeklickt, URL holen und dorthin weiterleiten.
                                    db.materialien.where("id").equals( parseInt( res[1] ) ).toArray().then( function (response) {
                                        var win = window.open(response[0].url, '_blank');
                                        win.focus();
                                    });
                                }
                                if ( res[0] == "titel") {
                                    // Vorhandene Themengruppe angeklickt, Themenseiten URL holen und dorthin weiterleiten.
                                    db.themenseiten.where("id").equals( parseInt( res[1] ) ).toArray().then( function (response) {
                                        var win = window.open(response[0].url, '_blank');
                                        win.focus();
                                    });
                                }
                                if ( res[0] == "add") {
                                    // Material einer Themengruppe hinzufügen
                                    var themengruppe = res[1];
                                    var url = opt.$trigger[0].getAttribute('data-materialurl');
                                    var id = opt.$trigger[0].getAttribute('data-materialid');
                                    var titel = opt.$trigger[0].getAttribute('data-materialtitel');
                                    db.materialien.add({materialid: parseInt(id), gruppenid: themengruppe, titel: titel, url: url });
                                    db.materialien.toArray().then( function (response) {
                                        materialien = response;
                                    });
                                }
                                if ( res[0] == "quit") {
                                    // Daten an Materialpool übergeben und DB löschen.
                                    var data = {
                                        'action': 'mp_update_themenseite',
                                        'material': materialien,
                                        'themenseite' : themenseiten,
                                    };
                                    jQuery.post(ajaxurl, data, function(response ) {
                                        ret = response;
                                        db.delete();
                                        themenseiten = new Object();
                                    });
                                }
                            },
                            items: items
                        };
                    }
                });
            };
        });
    </script>
