<?php
########  WooCommerceRecent Featured Products Slider Widget   ##########

class wpspl_woo_featured_products extends WP_Widget {

	protected $defaults;

	public function __construct() {

		// widget defaults
		$this->defaults = array(
				'title' 			=> __('','woo-product-slider-by-pangolin-lite'),
				'color1' 			=> '',
				'color2' 			=> '',
				'number_of_product' => 1,
		);

		$widget_slug = 'wpspl_woo_featured_products';

		$widget_ops  = array(
			'classname' => $widget_slug,
			'description' => __('Your Featured WooCommerce Products on Wide Slider.', 'woo-product-slider-by-pangolin-lite'),
			'customize_selective_refresh' => true,
		);

		$widget_name = __('Pangolin: Wide Featured Product Slider', 'woo-product-slider-by-pangolin-lite');

		parent::__construct($widget_slug, $widget_name, $widget_ops );
		$this->alt_option_name = 'wpspl_woo_featured_products';

		add_action( 'admin_enqueue_scripts', array( $this, 'wpspl_enqueue_scripts' ) );
		add_action( 'admin_footer-widgets.php', array( $this, 'wpspl_print_scripts' ), 9999 );
	}

	public function wpspl_enqueue_scripts( $hook_suffix ) {
		if ( 'widgets.php' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'underscore' );
	}


	public function wpspl_print_scripts() {?>
		<script>
			( function( $ ){
				function initColorPicker( widget ) {
					widget.find( '.color-picker' ).wpColorPicker( {
						change: _.throttle( function() { // For Customizer
							$(this).trigger( 'change' );
						}, 3000 )
					});
				}

				function onFormUpdate( event, widget ) {
					initColorPicker( widget );
				}

				$( document ).on( 'widget-added widget-updated', onFormUpdate );

				$( document ).ready( function() {
					$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
						initColorPicker( $( this ) );
					} );
				} );
			}( jQuery ) );
		</script>
		<?php
	}

	public function widget($args, $instance){


		extract($args);
		$instance 	= wp_parse_args( (array) $instance, $this->defaults );

	    $title      = apply_filters( 'widget_title', sanitize_text_field($instance['title']) );
        $number     = apply_filters( 'widget_number', $instance['number_of_product'] );
        $color1 	= $instance['color1'];
		$color2 	= $instance['color2'];
		?>

		<div class="widget__canvas--woo">

        <?php 
        echo $before_widget;
		echo $before_title.$title.$after_title;

		if (wpspl_is_woocommerce_activated()){

		global $product, $woocommerce_loop;

		$args = array(
			'post_type' 		=> 'product',
			'post_status' 		=> 'publish',
			'meta_key' 			=> '_featured',
			'meta_value' 		=> 'yes',
			'posts_per_page' 	=> $number,
		);

        // The query
        $products = new WP_Query( $args );

        // The loop
        if ( $products->have_posts() ) : ?>
		<section class="shortcode__product-featured">
		<div class="fearured-product__slider">
		<ul class="single-item">

		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

		<li itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="row">
			<div class="col-sm-5 front__product-featured__image" style="background-color:<?php echo $color1;?>;">
				<a property="url" href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail();?>
				</a>
			</div>


			<div class="col-sm-7 front__product-featured__text" style="background-color:<?php echo $color1;?>;">
			<div class="row">
			<div class="featured-product-heading clearfix">
				<div class="col-sm-7 col-xs-12 element-title product-featured__title">
					<a property="url" href="<?php the_permalink(); ?>">
						<?php woocommerce_template_single_title();?>
					</a>
				</div>
				<div class="col-sm-5 col-xs-12 product-featured__price" style="color:<?php echo $color2;?>;">
					<?php woocommerce_template_single_price();?>
				</div>
			</div>
				<div class="col-sm-12 product-featured__description">
					<?php woocommerce_template_single_excerpt();?>
				</div>

				<div class="col-sm-12 product-featured__add-cart">
				<div class="featured__add-cart_button" style="background-color:<?php echo $color2;?> !important;">
					<?php woocommerce_template_loop_add_to_cart();?>
				</div>
				</div>

				</div><!-- front__product-featured__text -->
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
					<li>
					<?php printf( esc_html__( 'This widget will display your Featured WooCommerce Products.', 'woo-product-slider-by-pangolin-lite' ) ); ?>
					</li>
					<li>
					<?php printf( wp_kses( __( 'Ready to set your first Featured Product? Head over to <a href="%1$s">Products</a> and Click on the Star.', 'woo-product-slider-by-pangolin-lite' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'edit.php?post_type=product' ) ) ); ?>
					</li>
				</ul>
				</div>
			</div>
			</section><!-- .no-results .not-found -->
			</div>

		<?php endif;
		wp_reset_query();

		}else{?>
		<!-- WooCommerce Not Found -->
		<section class="container-fluid no-results">
		<div class="row">
			<div class="col-xs-12 no-results__icon">
				<i class="fa fa-info-circle"></i>
			</div>
			<div class="col-xs-12 no-results__info">
				<h2 class="no-results__title">
					<?php esc_html_e( 'WooCommerce Not Found !', 'woo-product-slider-by-pangolin-lite' ); ?>
				</h2>
				<ul>
					<li>
					<?php esc_html_e( 'Make Sure You Have WooCommerce Installed & Activated', 'woo-product-slider-by-pangolin-lite' ); ?>
					</li>
				</ul>
			</div>
			</div>
			</section>
		<?php }

        echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['number_of_product'] = sanitize_text_field( $new_instance['number_of_product'] );

		$instance[ 'color1' ] = strip_tags( $new_instance['color1'] );
		$instance[ 'color2' ] = strip_tags( $new_instance['color2'] );

		return $instance;
	}


    public function form($instance){

		// Defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = sanitize_text_field($instance['title']);
		$number_of_product = absint($instance['number_of_product']);
		$color1 = esc_attr( $instance[ 'color1' ] );
		$color2 = esc_attr( $instance[ 'color2' ] );
		?>

	    <!-- Heading -->
		<label>
			<h3><?php _e('Heading', 'woo-product-slider-by-pangolin-lite') ?></h3>
		</label>


	    <p>
		    <label for="<?php echo $this->get_field_id('title');?>">
		    	<?php _e( 'Title:','woo-product-slider-by-pangolin-lite' );?>
		    </label>

		    <input
			    class="widefat"
			    id="<?php echo $this->get_field_id('title');?>"
			    name="<?php echo $this->get_field_name('title');?>"
			    value="<?php if(isset($title)) echo esc_attr($title);?>"/>
		</p>

		<!-- Product Settings -->
		<label>
			<h3><?php _e('Product Settings', 'woo-product-slider-by-pangolin-lite') ?></h3>
		</label>

		<p>
			<label for="<?php echo $this->get_field_id('number_of_product');?>">
				<?php _e( 'Number of Featured Products to display','woo-product-slider-by-pangolin-lite' );?>
			</label>

			<input
				class="widefat"
				type="number"
				min="1"
				id="<?php echo $this->get_field_id('number_of_product');?>"
				name="<?php echo $this->get_field_name('number_of_product');?>"
				value="<?php echo esc_attr($number_of_product); ?>"/>
		</p>

		<!-- Color -->
		<label>
			<h3><?php _e('Color', 'woo-product-slider-by-pangolin-lite') ?></h3>
		</label>

		<p>
			<label for="<?php echo $this->get_field_id( 'color1' ); ?>">
			<?php _e( 'Card Background Color : Product Card', 'woo-product-slider-by-pangolin-lite' ); ?>
			</label></br>
			<input
				type="text"
				name="<?php echo $this->get_field_name( 'color1' ); ?>"
				class="color-picker"
				id="<?php echo $this->get_field_id( 'color1' ); ?>"
				value="<?php echo $color1; ?>"
				data-default-color="#fff" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'color2' ); ?>">
			<?php _e( 'Button Background Color : Add To Cart', 'woo-product-slider-by-pangolin-lite' ); ?>
			</label></br>
			<input
				type="text"
				name="<?php echo $this->get_field_name( 'color2' ); ?>"
				class="color-picker"
				id="<?php echo $this->get_field_id( 'color2' ); ?>"
				value="<?php echo $color2; ?>"
				data-default-color="#f00" />
		</p>

		<!-- Pro -->
		<label>
			<h4>
			<a class="pangolin--pro" href="http://www.pangolinthemes.com" target="_blank">
				<?php _e('Unlock all features!', 'woo-product-slider-by-pangolin-lite') ?>
			</a>
			</h4>
		</label>

		<?php
	}
}