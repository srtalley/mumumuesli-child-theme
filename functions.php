<?php
// Exit if accessed directly

// www.mumumuesli.com.cdn.cloudflare.net

if ( !defined('ABSPATH')) exit;

// Disable auto updates
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_update_theme', '__return_false' );

/* Add custom functions below */
add_action( 'wp_enqueue_scripts', 'ds_enqueue_assets', 10 );
function ds_enqueue_assets() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', false, HB_THEME_VERSION );
  wp_enqueue_style( 'child-theme', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
  wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/js/main.js', '', '1.1.6', true );

}//end function ds_enqueue_assets

// add_action( 'wp_enqueue_scripts', 'ds_remove_google_fonts', 100 );

// function ds_remove_google_fonts() {
//   wp_dequeue_script( 'vc_google_fonts_roboto100100italic300300italicregularitalic500500italic700700italic900900italic-css');

// }

require_once( dirname( __FILE__ ) . '/includes/class-wc-custom-checkout.php');
require_once( dirname( __FILE__ ) . '/includes/class-woocommerce.php');
require_once( dirname( __FILE__ ) . '/includes/class-woocommerce-orders.php');
require_once( dirname( __FILE__ ) . '/includes/class-woocommerce-ups.php');

function ds_dequeue_improper_parent_style() {
  wp_dequeue_style( 'highend_styles' );
  wp_dequeue_style('highend-style');
}
add_action( 'wp_enqueue_scripts', 'ds_dequeue_improper_parent_style', 20 );

/**
 * Change the hamburger menu to an svg
 */
add_filter( 'highend_hamburger_menu', 'dst_change_hamburger_menu');
function dst_change_hamburger_menu() {  
  return '<img src="' . get_stylesheet_directory_uri() . '/images/hamburger-menu.svg" width="24" height="28" style="margin-top: 28px"></a>';
}

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
          if ( highend_option('hb_woo_notifications') ){
              global $woocommerce;
              if ( !isset($woocommerce) ) {
                return;
              }
              $checkout_url = wc_get_cart_url();
              ?><ul id="hb-woo-notif" data-text="<?php _e('added to cart.', 'hbthemes'); ?>" data-cart-text="<?php _e('Checkout', 'hbthemes'); ?>" data-cart-url="<?php echo $checkout_url; ?>"></ul><?php
          }
      }
  }

// Disable password change notification
add_action('init',function(){
  remove_action( 'after_password_reset', 'wp_password_change_notification' );
});

// Reduce scheduled actions log
add_filter( 'action_scheduler_retention_period', function() { return DAY_IN_SECONDS * 1; } );