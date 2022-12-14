<?php 
namespace MuMuMuesli\WooCommerce_Custom_Checkout;

class WooCommerce_Custom_Checkout {

    public function __construct() {
        // add_filter ( 'woocommerce_checkout_cart_item_quantity', array($this, 'wc_checkout_remove_qty'), 10, 2 );
        add_filter ( 'woocommerce_cart_item_name', array($this, 'wc_checkout_modify_order'), 10, 3 );
        add_filter( 'woocommerce_checkout_cart_item_quantity', array($this,'qty_input_field_on_checkout'), 20, 3 );

        add_action( 'wp_footer', array($this, 'wc_checkout_add_wc_custom_checkout_js'), 10 );
        add_action( 'wp_ajax_nopriv_update_order_review', array($this, 'wc_checkout_update_order_review') );
        add_action( 'wp_ajax_update_order_review', array($this, 'wc_checkout_update_order_review') );

    }

    /**
    * Remove the quantity count on the checkout review  
    */
    // public function wc_checkout_remove_qty( $cart_item, $cart_item_key ) {
    //     $product_quantity= '';
    //     return $product_quantity;
    // } 


    /**
    * Add the ability to delete products and change the quantity in the 
    * review section on checkout
    */
    public function wc_checkout_modify_order( $product_title, $cart_item, $cart_item_key ) {
        /* Checkout page check */
        if (  is_checkout() ) {
            /* Get Cart of the user */
            $cart     = WC()->cart->get_cart();
                foreach ( $cart as $cart_key => $cart_value ){
                    if ( $cart_key == $cart_item_key ){
                        $product_id = $cart_item['product_id'];
                        $_product   = $cart_item['data'] ;
                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );


                        /* Step 1 : Add delete icon */
                        $return_value = sprintf(
                            '<span class="product-name-left"><a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                            esc_url( WC()->cart->get_remove_url( $cart_key ) ),
                            __( 'Remove this item', 'woocommerce' ),
                            esc_attr( $product_id ),
                            esc_attr( $_product->get_sku() )
                        );
                        
                        /* Step 2 : Add product thumb */
                        $return_value .= '<span class="wc-checkout-thumbnail">';
						$thumbnail = $_product->get_image('thumbnail');
                        $return_value .= '<a href="' . esc_url( $product_permalink ) . '">' . $thumbnail . '</a>'; 
                        $return_value .= '</span>';

                        /* Step 3 : Add product name */
                        $return_value .= '<span class="product_name" >' . $product_title . '</span></span>' ;
                        
                        /* Step 4 : Add Price ea. */
                        $return_value .= '<span class="price-each"><strong>Price each:</strong> ' . WC()->cart->get_product_price( $_product ) . '</span>';
                        /* Step 3 : Add quantity selector */
                        // moved to separate function
                        // if ( $_product->is_sold_individually() ) {
                        //     $return_value .= sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_key );
                        // } else {
                        //     $return_value .= woocommerce_quantity_input( array(
                        //         'input_name'  => "cart[{$cart_key}][qty]",
                        //         'input_value' => $cart_item['quantity'],
                        //         'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                        //         'min_value'   => '1'
                        //         ), $_product, false );
                        // }
                        return $return_value;
                    }
                }
        }else{
            /*
            * It will return the product name on the cart page.
            * As the filter used on checkout and cart are same.
            */
            $_product   = $cart_item['data'] ;
            $product_permalink = $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '';
            if ( ! $product_permalink ) {
                $return_value = $_product->get_title() . '&nbsp;';
            } else {
                $return_value = sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_title());
            }
            return $return_value;
        }
    } // end function 
    
    public function qty_input_field_on_checkout( $quantity_html, $cart_item, $cart_item_key ) {
        $_product = $cart_item['data'];
    
        if ( $_product->is_sold_individually() ) {
            $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
        } else {
            $product_quantity = woocommerce_quantity_input(
                array(
                    'input_name'   => "cart[{$cart_item_key}][qty]",
                    'input_value'  => $cart_item['quantity'],
                    'max_value'    => $_product->get_max_purchase_quantity(),
                    'min_value'    => '0',
                    'product_name' => $_product->get_name(),
                ),
                $_product,
                false
            );
        }
    
        return '<span class="product-quantity"><strong>' . __( 'Qty') . ': </strong>' . $product_quantity . '</span>';
    }


    /**
     * Add the custom JS file
     */ 
    public function wc_checkout_add_wc_custom_checkout_js(){
        if(function_exists('is_checkout')) {
            if ( is_checkout() ) {
                wp_enqueue_script( 'checkout_script', get_stylesheet_directory_uri() . '/js/wc-custom-checkout.js', '', '1.1', false );
                $localize_script = array(
                  'ajax_url' => admin_url( 'admin-ajax.php' )
                );
                wp_localize_script( 'checkout_script', 'wc_custom_checkout', $localize_script );
              }
        }
    }

    public function wc_checkout_update_order_review() {
        $values = array();
        parse_str($_POST['post_data'], $values);
        $cart = $values['cart'];
        foreach ( $cart as $cart_key => $cart_value ){
            WC()->cart->set_quantity( $cart_key, $cart_value['qty'], false );
            WC()->cart->calculate_totals();
            woocommerce_cart_totals();
        }
        wp_die();
    }

} // end class
$mumu_wc_custom_checkout = new WooCommerce_Custom_Checkout();