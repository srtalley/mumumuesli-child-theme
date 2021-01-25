<?php 

namespace MuMuMuesli\Theme;

class MuMuMuesli_WooCommerce {

  
    public function __construct() {


        if(class_exists('woocommerce')) {

            add_filter('loop_shop_per_page', array(&$this, 'gm_show_all_products_in_shop'), 100);

            add_action( 'woocommerce_before_cart', array(&$this,'ds_woocommerce_before_cart_add_login'), 20);

            add_action( 'woocommerce_before_cart', 'woocommerce_checkout_login_form', 15);

            add_action( 'woocommerce_after_cart', array(&$this,'ds_woocommerce_add_standard_checkout'));

            add_filter( 'woocommerce_return_to_shop_redirect', array(&$this, 'ds_change_return_shop_url') );

            add_action( 'woocommerce_after_add_to_cart_form', array(&$this, 'add_content_after_addtocart'), 10);

            add_action( 'woocommerce_after_main_content',  array(&$this, 'add_reviews_to_categories'), 20 );

            add_action( 'woocommerce_email_header', array(&$this, 'mmu_add_content_specific_email'), 20, 2 );

            add_filter( 'woocommerce_product_tabs', array(&$this, 'remove_woocommerce_product_tabs'), 98 );

            add_action ( 'init', array(&$this, 'mumu_hook_into_tabs'));

            add_filter( 'woocommerce_product_tabs', array(&$this, 'dst_add_WooCommerce_Reviews'), 98 );

            add_action( 'woocommerce_single_product_summary', array(&$this, 'replace_product_rating'), 9 );

            add_filter( 'woocommerce_before_add_to_cart_form', array(&$this, 'dst_woocommerce_reviews_button_single_product') );

            // add_filter('hunch_schema_woocommerce_productschema', array(&$this, 'dst_woocommerce_productschema'));

            // add_filter('hunch_schema_woocommerce_productschema', array(&$this, 'dst_woocommerce_productschema_additional_product_info'));

            // add_action('wp_footer', array(&$this, 'dst_output_schema_homepage'));
            // remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
            // add_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 9);

            add_action( 'init', array(&$this, 'remove_output_structured_data') );


            add_filter( 'wp_schema_pro_schema_product',  array(&$this, 'dst_wp_schema_pro_schema_product'), 10, 3 );

            add_action( 'init', array(&$this, 'mumu_remove_product_category_description') );



        } // end if woocommerce
    } // end function construct


    /**
     * The theme templates do not implement the
     * woocommerce_proceed_to_checkout hook,
     * so we add it after the after_cart hook
    */

    public function ds_woocommerce_before_cart_add_login(){

        // WooCommerce scripts if a WooCommerce block is used
        if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

        wp_register_script( 'wc-checkout', WC()->plugin_url() . '/assets/js/frontend/checkout.min.js', array( 'jquery') );
        wp_enqueue_script( 'wc-checkout' );
        } // end if is_plugin_active

    } 

    /* 
    * The theme templates do not implement the woocommerce_proceed_to_checkout hook,
    * so we add it after the after_cart hook
    */

    public function ds_woocommerce_add_standard_checkout(){
        echo '<div class="wc-checkout-and-paypal-wrapper">';
        do_action( 'woocommerce_proceed_to_checkout' );
        echo '</div>';
    } 

    /**
     * Replace the Proceed to Checkout text
     */
    public function woocommerce_button_proceed_to_checkout() {
        ?>
        <div class="wc-checkout-button">
        <a href="<?php echo esc_url( wc_get_checkout_url() );?>" class="checkout-button button alt wc-forward">
            <?php esc_html_e( 'Pay with Credit Card', 'woocommerce' ); ?>
        </a>
        </div>
        <?php
    }

    /* 
    * Change the return to shop link
    */
    public function ds_change_return_shop_url() {
        return home_url() . '/shop';
    }

    public function add_content_after_addtocart() {

        // get the current post/product ID
        $current_product_id = get_the_ID();

        // get the product based on the ID
        $product = wc_get_product( $current_product_id );

        // get the "Checkout Page" URL
        // if(function_exists('WC')) {
        $checkout_url = WC()->cart->get_checkout_url();
        // }

        // run only on simple products
        if( $product->is_type( 'simple' ) ){
            
            echo '<div class="pay-with-cc-product-page"><a href="'.$checkout_url.'?add-to-cart='.$current_product_id.'&quantity=1&pay_by_cc=1" class="single_add_to_cart_button button alt">Pay with Credit Card</a></div>';
        }
    }


    public function add_reviews_to_categories() {
        if(!is_product()) {
            echo '<div class="reviews-wrapper"><div class="container"><h2 class="roboto">Reviews</h2>';
            //echo do_shortcode('[reviews-summary product_name="Mu Mu Muesli"]');
            echo '<a class="floating-review" href="/submit-review">Leave a Review</a>';
            echo do_shortcode('[ultimate-reviews]');
            echo '</div></div>';
        }
    }

    /**
    * @snippet       Add Content to the Customer Processing Order Email - WooCommerce
    */


    public function mmu_add_content_specific_email( $email_heading, $email ) {

        if ( $email->id == 'new_order' || $email->id == 'customer_processing_order' ||  $email->id == 'customer_invoice' ) {
            echo '<table width="100%">
            <tr>
            <td align="center" style="padding: 0;">
                <h2 style="color: #000; margin-bottom: 0;">Love Mu Mu Muesli?</h2>
            </td>
            </tr>							<!-- Button -->
            <tr>
            <td align="left" style="padding: 10px 0 20px;">
                <table width="200" align="left" border="0" cellpadding="0" cellspacing="0" style="border-radius: 2px;" bgcolor="#e03243">
                    <tr>
                    <td align="center" font-size: 28px; font-weight: 600; color: #ffffff; line-height: 36px;">
                        <a href="https://mumumuesli.com/submit-review/" style="color: #ffffff; text-decoration: none;">Leave a Review</a>
                    </td>
                    </tr>
                </table>
                </td>
            </tr>
            </table>
            <!-- End Button -->';
        }
    }


    public function dst_get_review_count($product_name = '') {
        if($product_name == ''){
        $product_name = 'Mu Mu Muesli';
        }
        global $wpdb;

        // $reviews_count = count($wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='EWD_URP_Product_Name' AND meta_value='Mu Mu Muesli'"));

        $reviews_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts as a LEFT JOIN  $wpdb->postmeta as b on b.post_id = a.ID WHERE meta_key='EWD_URP_Product_Name' AND meta_value='" . $product_name . "' AND a.post_status = 'publish'");
        return $reviews_count;

    } // end function dst_get_review_count

    function dst_get_reviews($product_name = '') {
        if($product_name == ''){
            $product_name = 'Mu Mu Muesli';
        }
        global $wpdb;
        $all_urp_reviews = $wpdb->get_results("SELECT a.ID, a.post_content, a.post_date_gmt, c.meta_value as `EWD_URP_Post_Author`, d.meta_value as `EWD_URP_Overall_Score` FROM $wpdb->posts as a INNER JOIN $wpdb->postmeta as b on b.post_id = a.ID INNER JOIN $wpdb->postmeta as c on c.post_id = a.ID INNER JOIN $wpdb->postmeta as d on d.post_id = a.ID WHERE b.meta_key='EWD_URP_Product_Name' AND b.meta_value='Mu Mu Muesli' AND c.meta_key = 'EWD_URP_Post_Author' AND d.meta_key = 'EWD_URP_Overall_Score'", ARRAY_A);

        return $all_urp_reviews;

    } // end function dst_get_reviews


    /* 
    * Move reviews below descripiton 
    */
    public function remove_woocommerce_product_tabs( $tabs ) {
        unset( $tabs['description'] );
        unset( $tabs['reviews'] );
        unset( $tabs['additional_information'] );
        return $tabs;
    }

    /**
    * Hook in each tabs callback function after single content.
    */
    public function mumu_hook_into_tabs() {

        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
        add_action('woocommerce_after_single_product_summary', function() { echo '<div class="desc clearfix"></div>'; }, 1 );
        
        add_action( 'woocommerce_after_single_product_summary', 'woocommerce_product_description_tab', 2 );

        add_action( 'woocommerce_after_single_product_summary', 'woocommerce_product_additional_information_tab', 3 );
        add_action('woocommerce_after_single_product_summary', function() { echo '<div class="desc clearfix"></div><div class="reviews-related-products d">'; }, 50);
        add_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 51);

        add_action('woocommerce_after_single_product_summary', function() { echo '<div class="related-products-wrapper">'; }, 52);

        add_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 53);

        add_action('woocommerce_after_single_product_summary', function() { echo '</div><!-- .related-products-wrapper --></div><!-- .reviews-related-products -->'; }, 54);
    }
    // Reviews are added below


    public function dst_add_WooCommerce_Reviews($tabs) {

        $reviews_count = $this->dst_get_review_count();
        $new_title = __('Reviews', 'ultimate-reviews') . " (" . $reviews_count . ")";

        $tabs['reviews']['title'] = $new_title;	
        $tabs['reviews']['callback'] = array(&$this,'dst_WooCommerce_Reviews');


        return $tabs;
    }

    public function dst_WooCommerce_Reviews() {

        $this->dst_Display_WC_Reviews();

        // echo "<div class='ewd-urp-woocommerce-tab-divider'></div>";

    } //end dst_WooCommerce_Reviews



    public function dst_Display_WC_Reviews() {


        //  echo "<h2>" . __("Reviews", 'ultimate-reviews') . "</h2>";

        echo "<div class='ewd-urp-wc-tab ";
        echo "ewd-urp-wc-active-tab";
        echo "'>";
        // echo do_shortcode('[reviews-summary product_name="Mu Mu Muesli"]');
        echo '<a class="floating-review" href="/submit-review">Leave a Review</a>';
        echo do_shortcode("[ultimate-reviews product_name='Mu Mu Muesli']");
        echo "</div>";
    

    } // end dst_Display_WC_Reviews


    public function replace_product_rating() {

        global $product;


        $Maximum_Score = get_option("EWD_URP_Maximum_Score");
        if(function_exists('EWD_URP_Get_Aggregate_Score')){
        $EWD_URP_Rating = EWD_URP_Get_Aggregate_Score('Mu Mu Muesli');
        } else {
        $EWD_URP_Rating = '5';
        } 

        $rating_html  = '<div class="star-rating" title="' . sprintf( __( 'Rated %s out of %s', 'woocommerce' ), $EWD_URP_Rating, $Maximum_Score ) . '"><a href="#tab-reviews" class="woocommerce-review-link" rel="nofollow">';
        $rating_html .= '<span style="width:' . (( $EWD_URP_Rating / $Maximum_Score ) * 100 ) . '%"><strong class="rating">' . $EWD_URP_Rating . '</strong> ' . sprintf( __( 'out of %s', 'woocommerce' ), $Maximum_Score) . '</span></a>';
        $rating_html .= '</div>';
        $rating_html .= '<div class="clearfix"></div>';
        // $rating_html .= '<div class="reviews-link">';
        // $rating_html .= '<a href="#tab-reviews">Read Reviews</a>';
        // $rating_html .= '</div>';

        echo $rating_html;

        } // end function replace_product_rating

    public function dst_woocommerce_reviews_button_single_product() {
        $reviews_button_html = '<div class="reviews-link">';
        $reviews_button_html .= '<a href="#tab-reviews">read reviews</a>';
        $reviews_button_html .= '</div>';

        echo $reviews_button_html;
    }

    public function dst_woocommerce_productschema($schema){

        $dst_EWD_URP_Rating = EWD_URP_Get_Aggregate_Score('Mu Mu Muesli');

        $dst_reviews_count = $this->dst_get_review_count();

        $dst_all_reviews = $this->dst_get_reviews();

        $dst_all_review_count = count($dst_all_reviews);

        $schema['aggregateRating'] = array
        (
            '@type' => 'AggregateRating',
            'ratingValue' => $dst_EWD_URP_Rating,
            'ratingCount' => $dst_reviews_count,
            'reviewCount' => $dst_all_review_count,
            'bestRating' => '5',
            'worstRating' => '1'
        );
        

        foreach ($dst_all_reviews as $dst_individual_review) {

        $schema['review'][] = array(

            '@type' => 'Review',
            '@id' => $schema['url'] . '#review-' . $dst_individual_review['ID'],
            'description' => wp_strip_all_tags($dst_individual_review['post_content']),
            'datePublished' => $dst_individual_review['post_date_gmt'],
            'reviewRating' => array(  
                '@type' => 'Rating',
                'ratingValue' => $dst_individual_review['EWD_URP_Overall_Score']
            ),
            'author' => array( 
                '@type' => 'Person',
                'name' => $dst_individual_review['EWD_URP_Post_Author'],
                'url' => ''
            )
        );
        }
        return $schema;
    }

    function dst_woocommerce_productschema_additional_product_info($schema) {
        $schema['brand'] = 'Mu Mu Muesli';
        $schema['offers']['priceValidUntil'] = '2030-01-01';

        if (!empty($schema['sku'])) {
        $schema['mpn'] = $schema['sku']; 
        }
        return $schema;
    }

    function dst_output_schema_homepage() {
        if(is_front_page()) {
        $dst_homepage_schema = array();

        $dst_homepage_schema['@context'] = 'http://schema.org';
        $dst_homepage_schema['@type'] = 'GroceryStore';
        $dst_homepage_schema['telephone'] = "+1-518-284-2441";
        $dst_homepage_schema['sameAs'] = array(
            "https://www.instagram.com/mu_mu_muesli/",
            "https://www.facebook.com/MuMuMuesli/",
            "https://www.pinterest.com/organicmumumuesli/",
            "https://www.youtube.com/channel/UCUcU3akn0eht0Ne-nB4Zbjg"
        );
        $dst_homepage_schema['geo'] = array( 
            "@type" => "GeoCoordinates",
            "name" => "Mu Mu Muesli Geo Coordinates",
            "latitude" => 42.805896,
            "longitude" => -74.626594,
            "@id" => "https://mumumuesli.com/#GeoShapeOrGeoCoordinates"
        );
        $dst_homepage_schema['contactPoint'] = array( 
            "@type" => "ContactPoint",
            "contactType" => "sales",
            "telephone" => "+1-518-284-2441",
            "availableLanguage" => "https://en.wikipedia.org/wiki/English_language",
            "name" => "Mu Mu Muesli Phone Number",
            "@id" => "https://mumumuesli.com/#ContactPoint"
        );
        $dst_homepage_schema['description'] = "Our muesli is packed with organic dates, coconut, raisins, cranberries, roasted almonds and flax seeds. Great as a hot oatmeal! Free Shipping!";
        $dst_homepage_schema['openingHoursSpecification'] = array(
            "@type" => "OpeningHoursSpecification",
            "dayOfWeek" => array(
                "Sunday",
                "Monday",
                "Tuesday",
                "Saturday",
                "Thursday",
                "Wednesday",
                "Friday"
            ),
            "closes" => "23:59:59",
            "name" => "Mu Mu Muesli Normal Hours",
            "opens" => "00:00:00",
            "@id" => "https://mumumuesli.com/#OpeningHoursSpecification"
        );
        $dst_homepage_schema['url'] = "https://mumumuesli.com/";
        $dst_homepage_schema['address'] = array(
            "@type" => "PostalAddress",
            "addressRegion" => "NY",
            "addressCountry" => "USA",
            "postalCode" => "13459",
            "addressLocality" => "Sharon Springs"
        );
        $dst_homepage_schema['priceRange'] = "$$";
        $dst_homepage_schema['potentialAction'] = array(
            "@type" => "OrderAction",
            "priceSpecification" => array(
                "@type" => "UnitPriceSpecification",
                "validFrom" => "2019-08-01T18:23:43-0500",
                "minPrice" => 12,
                "priceCurrency" => "USD",
                "eligibleQuantity" => array(
                "@type" => "QuantitativeValue",
                "name" => "1",
                "@id" => "https://mumumuesli.com/#QuantitativeValue"
                ),
                "maxPrice" => 145,
                "name" => "Unit Price",
                "eligibleTransactionVolume" => array(
                "@type" => "PriceSpecification",
                "name" => "12",
                "@id" => "https://mumumuesli.com/#PriceSpecification0"
                ),
                "validThrough" => "2022-01-01T18:23:49-0600",
                "@id" => "https://mumumuesli.com/#PriceSpecification"
            ),
            "deliveryMethod" => array(
                "@type" => "ParcelService",
                "name" => "Shipped",
                "@id" => "https://mumumuesli.com/#DeliveryMethod"
            ),
            "name" => "Order",
            "@id" => "https://mumumuesli.com/#Action"
            );
            $dst_homepage_schema['name'] = "Mu Mu Muesli";
            $dst_homepage_schema['image'] = "https://mumumuesli.com/wp-content/uploads/2019/06/logo-6.png";
            $dst_homepage_schema['additionalType'] = "https://en.wikipedia.org/wiki/Breakfast_cereal";
            $dst_homepage_schema['@id'] = "https://mumumuesli.com/";

            $dst_final_homepage_schema = $this->dst_woocommerce_productschema($dst_homepage_schema);

            printf( '<!-- Schema DST --><script type="application/ld+json">[%s]</script><!-- Schema DST -->' . "\n", json_encode( $dst_final_homepage_schema ) );
        }
    }

    /* Remove the default WooCommerce 3 JSON/LD structured data */
    public function remove_output_structured_data() {
        remove_action( 'wp_footer', array( WC()->structured_data, 'output_structured_data' ), 10 ); // This removes structured data from all frontend pages
        remove_action( 'woocommerce_email_order_details', array( WC()->structured_data, 'output_email_structured_data' ), 30 ); // This removes structured data from all Emails sent by WooCommerce
    }

    public function dst_wp_schema_pro_schema_product($schema, $data, $post) {
        $product_permalink = get_the_permalink($post->ID);

        if(function_exists('EWD_URP_Get_Aggregate_Score')) {
        $dst_EWD_URP_Rating = EWD_URP_Get_Aggregate_Score('Mu Mu Muesli');
        } else {
        $dst_EWD_URP_Rating = '5';
        }
        $dst_reviews_count = $this->dst_get_review_count();

        $dst_all_reviews = $this->dst_get_reviews();

        $dst_all_review_count = count($dst_all_reviews);

        $schema['aggregateRating'] = array
        (
            '@type' => 'AggregateRating',
            'ratingValue' => $dst_EWD_URP_Rating,
            'ratingCount' => $dst_reviews_count,
            'reviewCount' => $dst_all_review_count,
            'bestRating' => '5',
            'worstRating' => '1'
        );
        foreach ($dst_all_reviews as $dst_individual_review) {

            $schema['review'][] = array(

                '@type' => 'Review',
                '@id' => $product_permalink . '#review-' . $dst_individual_review['ID'],
                'description' => wp_strip_all_tags($dst_individual_review['post_content']),
                'datePublished' => $dst_individual_review['post_date_gmt'],
                'reviewRating' => array(  
                '@type' => 'Rating',
                'ratingValue' => $dst_individual_review['EWD_URP_Overall_Score']
                ),
                'author' => array( 
                '@type' => 'Person',
                'name' => $dst_individual_review['EWD_URP_Post_Author'],
                'url' => ''
                )
            );
        }
        return $schema;

    }
    public function mumu_remove_product_category_description() {
        remove_action( 'woocommerce_archive_description',  'woocommerce_taxonomy_archive_description' , 10);
      
        remove_action( 'woocommerce_archive_description',  'woocommerce_product_archive_description' , 10);
        
        
        add_action( 'woocommerce_after_shop_loop', 'woocommerce_product_archive_description',15 );
        add_action( 'woocommerce_after_shop_loop', 'woocommerce_taxonomy_archive_description',15 );
      
    }
      
    /**
    * Logging function to debug.log
    */
    public function wl ( $log )  {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
} // end class

$mumumuesli_woocommerce = new MuMuMuesli_WooCommerce();