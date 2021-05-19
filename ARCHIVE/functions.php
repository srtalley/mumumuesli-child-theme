<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/* Add custom functions below */
add_action( 'wp_enqueue_scripts', 'ds_enqueue_assets', 10 );
function ds_enqueue_assets() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', false, HB_THEME_VERSION );
  wp_enqueue_style( 'child-theme', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
  wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/js/main.js', '', '1.1.3', true );


}//end function ds_enqueue_assets

require_once( dirname( __FILE__ ) . '/includes/class-wc-custom-checkout.php');
require_once( dirname( __FILE__ ) . '/includes/class-woocommerce.php');

function ds_dequeue_improper_parent_style() {
  wp_dequeue_style( 'highend_styles' );
  wp_dequeue_style('highend-style');
}
add_action( 'wp_enqueue_scripts', 'ds_dequeue_improper_parent_style', 20 );

function wl ( $log )  {
  if ( is_array( $log ) || is_object( $log ) ) {
      error_log( print_r( $log, true ) );
  } else {
      error_log( $log );
  }
}

    /* 
    * Change the theme's blue banner when adding to cart. 
    * Set it to go to /cart instead of /checkout
    */
    if ( !function_exists('hb_woo_notifications') ) {
      function hb_woo_notifications(){
          if ( hb_options('hb_woo_notifications') ){
              global $woocommerce;
              if ( !isset($woocommerce) ) {
              return;
              }
              $checkout_url = wc_get_cart_url();
              ?><ul id="hb-woo-notif" data-text="<?php _e('added to cart.', 'hbthemes'); ?>" data-cart-text="<?php _e('Checkout', 'hbthemes'); ?>" data-cart-url="<?php echo $checkout_url; ?>"></ul><?php
          }
      }
  }

