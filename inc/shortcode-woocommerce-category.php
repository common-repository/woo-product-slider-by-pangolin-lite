<?php
function wpspl_woocommerce_product_category( $atts ) {

		extract(shortcode_atts(
			array(
				'column' 		=> '2',
				'card_color' 	=> '#eee',
				'text_color' 	=> '#000',
			), $atts));

		// Product Category Layout 1
		$taxonomyName = "product_cat";
		$prod_categories = get_terms($taxonomyName, array(
		    'orderby'=> 'name',
		    'order' => 'ASC',
		    'hide_empty' => 1
		));

		foreach( $prod_categories as $prod_cat ) :
		    if ( $prod_cat->parent != 0 )
		        continue;
		    $cat_thumb_id 		= get_woocommerce_term_meta( $prod_cat->term_id, 'thumbnail_id', true );
		    $cat_thumb_url 		= wp_get_attachment_image_src( $cat_thumb_id, 'shop_catalog' );
		    $term_link 			= get_term_link( $prod_cat, 'product_cat' );

		    if (empty($cat_thumb_url[0])):
		    	$cat_thumb_url[0] = plugins_url( 'inc/category-image.png', dirname(__FILE__) );
		    endif;
		    ?>

			<div itemprop="category" class="front-product-category__card <?php echo wpspl_column_switcher($column);?> equal-height">
			<div class="front-product-category__card__inner" style="background-color: <?php echo $card_color;?>;">
			<a href="<?php echo $term_link; ?>">
			    <img src="<?php echo $cat_thumb_url[0]; ?>" class="img-responsive" alt="<?php echo $prod_cat->name;?>" itemprop="image" />
			    <h3 class="element-title element-title--sub" style="color: <?php echo $text_color;?>;">
			    	<?php echo $prod_cat->name;?>
			    </h3>
			</a>
			</div>
			</div>
		<?php endforeach;	
}