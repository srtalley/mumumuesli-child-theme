<?php 

namespace MuMuMuesli\Theme;

class MuMuMuesli_WooCommerce_UPS {

  
    public function __construct() {
        if(class_exists('woocommerce')) {
            add_filter( 'wf_ups_shipment_confirm_request_data', array($this, 'ph_change_company_name_in_label'), 10, 2 );

        } // end if woocommerce
    } // end function construct


    /**
     * Plugin Hive provided code to copy the person's name
     * to the company name field so that UPS shows the 
     * label name correctly.
     * 
     * Ref: https://gist.github.com/Karthik-Naik/ab6b5016a775f05a0bd145dfbbd98f5a
     */
    public function ph_change_company_name_in_label( $request_arr, $order ) {
    
        if( isset($request_arr['Shipment']['ShipTo']['CompanyName']) && ( empty($request_arr['Shipment']['ShipTo']['CompanyName']) || $request_arr['Shipment']['ShipTo']['CompanyName'] == '-' ) ) {
            $request_arr['Shipment']['ShipTo']['CompanyName'] = $request_arr['Shipment']['ShipTo']['AttentionName'];
        }

        return $request_arr;
    }
} // end class

$mumumuesli_woocommerce_ups= new MuMuMuesli_WooCommerce_UPS();