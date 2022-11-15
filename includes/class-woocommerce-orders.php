<?php 

namespace MuMuMuesli\Theme;

class MuMuMuesli_WooCommerce_Orders {

  
    public function __construct() {

    // removed shortcode: [reviews-summary product_name="Mu Mu Muesli"]

        if(class_exists('woocommerce')) {
            add_action( 'init', array($this, 'mumumuesli_cron_cancel_failed_orders'), 10, 1 );
            add_action( 'failed_orders_event', array($this, 'mumumuesli_cancel_failed_orders'), 10, 1 );
        } // end if woocommerce
    } // end function construct

    /**
     * Hourly cron job to cancel old failed orders
     */
    public function mumumuesli_cron_cancel_failed_orders() {
        if ( !wp_next_scheduled( 'failed_orders_event' ) ) {
            wp_schedule_event( time(), 'hourly', 'failed_orders_event');
        }
    }

    /**
     * Function run by cron to actually remove older failed orders
     */
    public function mumumuesli_cancel_failed_orders() {
        $failed_orders = wc_get_orders( array(
            'limit'        => -1,
            'status'       => 'failed',
            'date_created' => '<' . ( time() - DAY_IN_SECONDS),
        ));

        foreach ( $failed_orders as $order ) {
            $cancelled_text = __("No successful payment", "woocommerce");
            $order->update_status( 'cancelled',$cancelled_text);
        }
        
    }
} // end class

$mumumuesli_woocommerce_orders = new MuMuMuesli_WooCommerce_Orders();