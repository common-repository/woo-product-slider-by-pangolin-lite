<?php
function wpspl_woocommerce_product( $atts ) {

	global $woocommerce, $woocommerce_loop;

	extract(shortcode_atts(
		array(
			'product' 				=> '6',
			'orderby' 				=> '',
			'order'   				=>	'ASC',
			'card_color' 			=> '#eeeeee',
			'text_color' 			=> '#000000',
		), $atts));

	$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $product,
			'orderby' 				=> $orderby,
			'order'   				=> $order,
			'meta_key'     			=> '',
	);

	ob_start();
	$products = new WP_Query( $args );

	if ( $products->have_posts() ) : ?>

	<section class="shortcode__product-featured">
	<div class="sc__recent-product__slider">
	<ul class="multiple-items">

	<?php while ( $products->have_posts() ) : $products->the_post(); ?>

	<!-- Layout 1 -->
	<li itemscope itemtype="http://schema.org/Product" class="product">
	<div class="product-card__inner" style="background-color:<?php echo $card_color; ?>;">
		<a property="url" href="<?php the_permalink(); ?>">
			<?php woocommerce_show_product_loop_sale_flash();?>
			<?php woocommerce_template_loop_product_thumbnail();?>
		</a>
		<div class="product-card__info">
			<div itemprop="name" class="product-card__info__product">
				<a property="url" href="<?php the_permalink(); ?>">
					<?php woocommerce_template_loop_product_title();?>
				</a>
			</div>
			<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="product-card__info__price">
				<span itemprop="price" class="price" style="color:<?php echo $text_color;?> !important;">
					<?php woocommerce_template_single_price();?>
				</span>
			</div>
			<div class="product-recent__add-cart">
				<?php woocommerce_template_loop_add_to_cart();?>
			</div>
		</div>
	</div>
	</li>

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
	</section>

	<?php endif;
	wp_reset_query();

	return ob_get_clean();
}