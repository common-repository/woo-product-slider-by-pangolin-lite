<?php
########  WooCommerce  Products Category  ##########

class wpspl_woo_product_category extends WP_Widget {

	protected $defaults;

	public function __construct() {

		// widget defaults
		$this->defaults = array(
			'title' 					=> __('','woo-product-slider-by-pangolin-lite'),
			'card_color' 				=> '',
			'text_color' 				=> '',
			'number_of_column' 			=> 1,
		);

		$widget_slug = 'wpspl_woo_product_category';

		$widget_ops  = array(
			'classname' => $widget_slug,
			'description' => __('A list of your WooCommerce Product Categories', 'woo-product-slider-by-pangolin-lite'),
			'customize_selective_refresh' => true,
		);

		$widget_name = __('Pangolin: WooCommerce Product Category', 'woo-product-slider-by-pangolin-lite');

		parent::__construct($widget_slug, $widget_name, $widget_ops );
		$this->alt_option_name = 'pangolin_woo_product_category';

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
        $card_color = ( ! empty( $instance['card_color'] ) ) ? $instance['card_color'] : '#eeeeee';
		$text_color = ( ! empty( $instance['text_color'] ) ) ? $instance['text_color'] : '#333333';
		$column 	= isset( $instance['number_of_column'] ) ? absint($instance['number_of_column']) : '1';

		?>

		<div class="widget__canvas--woo">

        <?php 
        echo $before_widget;
		echo $before_title.$title.$after_title;

		// Check if WooCommerce is activate
		if (is_woocommerce_activated()){

		
		//Layout 1


		$taxonomyName = "product_cat";
		$prod_categories = get_terms($taxonomyName,
			array(
			    'orderby'		=> 'name',
			    'order' 		=> 'ASC',
			    'hide_empty' 	=> 1
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
			    <img  src="<?php echo $cat_thumb_url[0]; ?>" class="img-responsive" alt="<?php echo $prod_cat->name; ?>" itemprop="image" />
			    <h3 class="element-title element-title--sub" style="color: <?php echo $text_color;?>;">
			    	<?php echo $prod_cat->name;?>
			    </h3>
			</a>
			</div>
			</div>
		<?php endforeach;?>

		<?php wp_reset_query();

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
		</div>
		<?php }

        echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] 				= sanitize_text_field( $new_instance['title'] );
		$instance[ 'card_color' ] 		= strip_tags( $new_instance['card_color'] );
		$instance[ 'text_color' ] 		= strip_tags( $new_instance['text_color'] );
		$instance['number_of_column'] 	= absint($new_instance['number_of_column']);

		return $instance;
	}


    public function form($instance){

		// Defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title 							= sanitize_text_field($instance['title']);
		$card_color 					= esc_attr( $instance[ 'card_color' ] );
		$text_color 					= esc_attr( $instance[ 'text_color' ] );
		$column 						= absint($instance['number_of_column']);
		?>

	    <!-- Heading -->
        <p>
			<label>
				<h3><?php _e('Heading', 'woo-product-slider-by-pangolin-lite') ?></h3>
			</label>
		</p>


	    <p>
		    <label for="<?php echo $this->get_field_id('title');?>">
		    	<?php _e( 'Title of Product Category Section ','woo-product-slider-by-pangolin-lite' );?>
		    </label>

		    <input
			    class="widefat"
			    id="<?php echo $this->get_field_id('title');?>"
			    name="<?php echo $this->get_field_name('title');?>"
			    value="<?php if(isset($title)) echo esc_attr($title);?>"/>
		</p>

		<!-- Layout Heading -->
        <p>
			<label>
				<h3><?php _e('Layouts', 'woo-product-slider-by-pangolin-lite') ?></h3>
			</label>
		</p>

		<!-- Number of Column-->
        <p>
			<label for="<?php echo $this->get_field_id( 'woo-product-slider-by-pangolin-lite' ); ?>">
				<?php _e('Display in Column:', 'pangolin-woo-slider') ?>
			</label>
			<select
			id="<?php echo $this->get_field_id( 'number_of_column' ); ?>"
			name="<?php echo $this->get_field_name( 'number_of_column' ); ?>"
			class="widefat">
				<option value="1"
					<?php if ( '1' == $instance['number_of_column'] ) echo 'selected="selected"'; ?>><?php _e('1 Column', 'woo-product-slider-by-pangolin-lite') ?>
				</option>
				<option value="2"
					<?php if ( '2' == $instance['number_of_column'] ) echo 'selected="selected"'; ?>><?php _e('2 Column', 'woo-product-slider-by-pangolin-lite') ?>
				</option>
                <option value="3"
                	<?php if ( '3' == $instance['number_of_column'] ) echo 'selected="selected"'; ?>><?php _e('3 Column', 'woo-product-slider-by-pangolin-lite') ?>
                </option>
                <option value="4"
                	<?php if ( '4' == $instance['number_of_column'] ) echo 'selected="selected"'; ?>><?php _e('4 Column', 'woo-product-slider-by-pangolin-lite') ?>
                </option>
			</select>
		</p>

		<!-- Style -->
        <p>
			<label>
				<h3><?php _e('Color', 'woo-product-slider-by-pangolin-lite') ?></h3>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'card_color' ); ?>">
			<?php _e( 'Background Color : Category Card', 'woo-product-slider-by-pangolin-lite' ); ?>
			</label></br>
			<input
				type="text"
				name="<?php echo $this->get_field_name( 'card_color' ); ?>"
				class="color-picker"
				id="<?php echo $this->get_field_id( 'card_color' ); ?>"
				value="<?php echo $card_color; ?>"
				data-default-color="#fff" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'text_color' ); ?>">
			<?php _e( 'Text Color : Category Content', 'woo-product-slider-by-pangolin-lite' ); ?>
			</label></br>
			<input
				type="text"
				name="<?php echo $this->get_field_name( 'text_color' ); ?>"
				class="color-picker"
				id="<?php echo $this->get_field_id( 'text_color' ); ?>"
				value="<?php echo $text_color; ?>"
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