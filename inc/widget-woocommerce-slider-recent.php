<?php
/**
 * @package WooCommerce Slider for Products
 * @since 0.0.1
 */

########  WooCommerceRecent Products Widget   ##########

class wpspl_woo_recent_products extends WP_Widget {

	protected $defaults;

	public function __construct() {

		// widget defaults
		$this->defaults = array(
							'title' 			=> __('','woo-product-slider-by-pangolin-lite'),
							'color1' 			=> '',
							'color2' 			=> '',
							'number_of_product' => '',
							'orderby'			=> 'date',
						);

		$widget_slug = 'wpspl_woo_recent_products';

		$widget_ops  	= array(
							'classname' 		=> $widget_slug,
							'description' 		=> __('A list of your recent WooCommerce Products.', 'woo-product-slider-by-pangolin-lite'),
							'customize_selective_refresh' => true,
						);

		$widget_name = __('Pangolin: Woo Product Slider', 'woo-product-slider-by-pangolin-lite');

		parent::__construct($widget_slug, $widget_name, $widget_ops );

		$this->alt_option_name = 'wpspl_woo_recent_products';

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
        $number     = apply_filters( 'widget_number', absint($instance['number_of_product']) );
        $color1 	= ( ! empty( $instance['color1'] ) ) ? $instance['color1'] : '#eeeeee';
		$color2 	= ( ! empty( $instance['color2'] ) ) ? $instance['color2'] : '#333333';
		$orderby 	= isset( $instance['orderby'] ) ? $instance['orderby'] : 'date';
		?>

		<div class="widget__canvas--woo">

        <?php 
        echo $before_widget;
		echo $before_title.$title.$after_title;

		// Check if WooCommerce is activate
		if (wpspl_is_woocommerce_activated()){

		global $woocommerce, $woocommerce_loop;

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $number,
			'orderby' 				=> $orderby,
			'order'   				=> 'ASC',
			'meta_key'     			=> '',
		);

        // The query
        $products = new WP_Query( $args );

        // The loop
        if ( $products->have_posts() ) : ?>
			<section class="shortcode__product-featured row">
			<div class="sc__recent-product__slider col-md-12">
			<ul class="multiple-items">

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

			<li itemscope itemtype="http://schema.org/Product" class="product">
				<div class="product-card__inner" style="background-color:<?php echo $color1; ?>;">
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
							<span itemprop="price" class="price" style="color:<?php echo $color2;?> !important;">
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
			<!-- No Products Found -->
			<section class="container-fluid no-results">
			<div class="row">
				<div class="col-xs-12 no-results__icon">
					<i class="fa fa-info-circle"></i>
				</div>
				<div class="col-xs-12 no-results__info">
					<h2 class="no-results__title">
						<?php esc_html_e( 'No  Products Found! ', 'woo-product-slider-by-pangolin-lite' ); ?>
					</h2>
					<ul>
					<li>
					<?php printf( wp_kses( __( 'Ready to create your first Product? Head over to <a href="%1$s">Add New Product</a>', 'woo-product-slider-by-pangolin-lite' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php?post_type=product' ) ) ); ?>
					</li>
					</ul>
				</div>
			</div>
			</div>
			</section><!-- .no-results .not-found -->

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

		$instance['title'] 				= sanitize_text_field( $new_instance['title'] );
		$instance['number_of_product'] 	= absint( $new_instance['number_of_product'] );
		$instance[ 'color1' ] 			= strip_tags( $new_instance['color1'] );
		$instance[ 'color2' ] 			= strip_tags( $new_instance['color2'] );
		$instance[ 'orderby' ] 			=  $new_instance['orderby'];

		return $instance;
	}


    public function form($instance){

		// Defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title 				= $instance['title'];
		$number_of_product 	= absint($instance['number_of_product']);
		$color1 			= esc_attr( $instance[ 'color1' ] );
		$color2 			= esc_attr( $instance[ 'color2' ] );
		$orderby 			= $instance[ 'orderby'];
		?>

	    <!-- Heading -->
		<label>
			<h3><?php _e('Heading', 'woo-product-slider-by-pangolin-lite') ?></h3>
		</label>

	    <p>
		    <label for="<?php echo $this->get_field_id('title');?>">
		    	<?php _e( 'Title:','pangolin-woo-slider' );?>
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


		<!-- Sort Products by -->
        <p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>">
				<?php _e('Sort Products by', 'woo-product-slider-by-pangolin-lite') ?>
			</label>
			<select
			id="<?php echo $this->get_field_id( 'orderby' ); ?>"
			name="<?php echo $this->get_field_name( 'orderby' ); ?>"
			class="widefat">
				<option value="date"
					<?php if ( 'date' == $instance['orderby'] ) echo 'selected="selected"'; ?>><?php _e('Date', 'woo-product-slider-by-pangolin-lite') ?>
				</option>
				<option value="title"
					<?php if ( 'title' == $instance['orderby'] ) echo 'selected="selected"'; ?>><?php _e('Title', 'woo-product-slider-by-pangolin-lite') ?>
				</option>
                <option value="sales"
                	<?php if ( 'total_sales' == $instance['orderby'] ) echo 'selected="selected"'; ?>><?php _e('Sales', 'woo-product-slider-by-pangolin-lite') ?>
                </option>
                <option value="rating"
                	<?php if ( 'rating' == $instance['orderby'] ) echo 'selected="selected"'; ?>><?php _e('Rating', 'woo-product-slider-by-pangolin-lite') ?>
                </option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number_of_product');?>">
				<?php _e( 'Number of Products to display','woo-product-slider-by-pangolin-lite' );?>
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
			<?php _e( 'Product Card : Background Color', 'woo-product-slider-by-pangolin-lite' ); ?>
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
			<?php _e( 'Product Price : Color' , 'woo-product-slider-by-pangolin-lite'); ?>
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