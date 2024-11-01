<?php

function wpspl_woocommerce_product_featured( $atts ) {

	global $woocommerce, $woocommerce_loop;

	extract(shortcode_atts(array(
		'card_color' 			=> '#eeeeee',
		'text_color' 			=> '#000000',
		'per_page' 				=> '12',
	), $atts));

	$args = array(
		'post_type'				=> 'product',
		'post_status' 			=> 'publish',
		'ignore_sticky_posts'	=> 1,
		'posts_per_page' 		=> $per_page,
		'meta_key' 				=> '_featured',
		'meta_value' 			=> 'yes',
	);

	ob_start();
	$products = new WP_Query( $args );

	if ( $products->have_posts() ) : ?>
	<section class="shortcode__product-featured">
	<div class="fearured-product__slider">
	<ul class="single-item">

	<?php while ( $products->have_posts() ) : $products->the_post(); ?>

		<!-- Layout 1 -->
		<li itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="col-sm-5 front__product-featured__image" style="background-color:<?php echo $card_color;?>;">
				<a property="url" href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail();?>
				</a>
			</div>


			<div class="col-sm-7 front__product-featured__text" style="background-color:<?php echo $card_color;?>;">
			<div class="row">
			<div class="featured-product-heading clearfix">
				<div class="col-sm-7 col-xs-12 element-title product-featured__title">
					<a property="url" href="<?php the_permalink(); ?>">
						<?php woocommerce_template_single_title();?>
					</a>
				</div>
				<div class="col-sm-5 col-xs-12 product-featured__price" style="color:<?php echo $text_color;?>;">
					<?php woocommerce_template_single_price();?>
				</div>
			</div>
				<div class="col-sm-12 product-featured__description">
					<?php woocommerce_template_single_excerpt();?>
				</div>

				<div class="col-sm-12 product-featured__add-cart">
				<div class="featured__add-cart_button" style="background-color:<?php echo $text_color; ?>;">
					<?php woocommerce_template_loop_add_to_cart();?>
				</div>
				</div>

				</div><!-- front__product-featured__text -->
			</div>
		</li><!-- #product-<?php the_ID(); ?> -->

	<?php endwhile; // end of the loop. ?>
	</ul>
	</div>
	</section>

	<?php else: ?>

	<section class="container-fluid no-results">
	<div class="row">
		<div class="col-md-4 no-results__icon">
			<i class="fa fa-info-circle"></i>
		</div>
		<div class="col-md-8 no-results__info">
			<h2 class="no-results__title"><?php esc_html_e( 'No Featured Products Set! ', 'woo-product-slider-by-pangolin-lite' ); ?></h2>
			<ul>
				<li><?php printf( esc_html__( 'This section will display your Featured WooCommerce Products.', 'woo-product-slider-by-pangolin-lite' ) ); ?></li>
				<li><?php printf( wp_kses( __( 'Ready to set your first Featured Product? Head over to <a href="%1$s">Products</a> and Click on the Star.', 'woo-product-slider-by-pangolin-lite' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'edit.php?post_type=product' ) ) ); ?></li>
				<li><?php printf( esc_html__( 'To Re-order or Hide this section install "Homepage Control" plugin.', 'woo-product-slider-by-pangolin-lite' ) ); ?></li>
			</ul>
		</div>
	</div>
	</section><!-- .no-results .not-found -->

	<?php endif;
	wp_reset_query();

	return ob_get_clean();
}