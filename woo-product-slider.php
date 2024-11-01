<?php
/**
 * Plugin Name: Woo Product Slider by Pangolin - Lite
 * Plugin URI: http://www.pangolinthemes.com/woocommerce-product-slider
 * Description: An elegant WooCommerce product slider (Widget & Shortcode).
 * Author: Pangolin Themes
 * Author URI: http://www.pangolinthemes.com
 * Version: 1.01
 * Text Domain: woo-product-slider-by-pangolin-lite
 *
 *
 * WooCommerce Slider by Pangolin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WooCommerce Slider by Pangolin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Digital Downloads. If not, see <http://www.gnu.org/licenses/>.
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


define( 'WPSPL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

include( WPSPL_PLUGIN_DIR . 'inc/widget-woocommerce-slider-recent.php'      );
include( WPSPL_PLUGIN_DIR . 'inc/widget-woocommerce-slider-featured.php'    );
include( WPSPL_PLUGIN_DIR . 'inc/widget-woocommerce-category.php'           );
include( WPSPL_PLUGIN_DIR . 'inc/shortcode-woocommerce-slider-recent.php'   );
include( WPSPL_PLUGIN_DIR . 'inc/shortcode-woocommerce-slider-featured.php' );
include( WPSPL_PLUGIN_DIR . 'inc/shortcode-woocommerce-category.php'        );

add_action('wp_enqueue_scripts', 'wpspl_scripts' );

function wpspl_scripts() {
	wp_enqueue_style( 'wpspl-library', plugins_url('/lib/css/libraries.css', __FILE__), array(), null, 'all' );
  	wp_enqueue_style( 'wpspl-core-style', plugins_url('/lib/css/bellini-woocommerce.css', __FILE__), array(), null, 'all' );
	wp_enqueue_script( 'wpspl-library-js', plugins_url('/lib/js/library.js', __FILE__) , array('jquery'), null, true );
	wp_enqueue_script( 'wpspl-core-js', plugins_url('/lib/js/pangolin.js', __FILE__) , array('jquery'), null, true );
}

function wpspl_column_switcher($column){
	if ($column == 1){
		return 'col-sm-12';
	}elseif($column == 2){
		return 'col-sm-6';
	}elseif($column == 3){
		return 'col-sm-4';
	}else{
		return 'col-sm-3';
	}
}

if ( ! function_exists( 'wpspl_is_woocommerce_activated' ) ) {
	function wpspl_is_woocommerce_activated() {
		return class_exists( 'woocommerce' ) ? true : false;
	}
}


add_action( 'widgets_init', 'wpspl_register_woo_slider');

function wpspl_register_woo_slider(){
    register_widget('wpspl_woo_recent_products');
    register_widget('wpspl_woo_featured_products');
    register_widget('wpspl_woo_product_category');
}


add_shortcode('woo-product-featured', 'wpspl_woocommerce_product_featured');
add_shortcode('woo-product', 'wpspl_woocommerce_product');
add_shortcode('woo-product-category', 'wpspl_woocommerce_product_category');

add_action('admin_enqueue_scripts', 'wpspl_admin_theme_style');

function wpspl_admin_theme_style() {
  echo '<style>

  .widget-liquid-right h3{
  	font-size:1.4em;
  	border-top:1px solid #eee !important;
  	padding-top:2em;
  	color:#6F7677;
  }

  .widget-inside{color:#6F7677;}
  .pangolin--pro{color:#8BC34A; text-decoration:none;}
  .pangolin--pro:hover{color:#009688;}
  </style>';
}