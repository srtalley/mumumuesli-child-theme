jQuery(function( $ ) {

    function wc_checkout_update(e) {
        var data = {
            action: 'update_order_review',
            security: wc_checkout_params.update_order_review_nonce,
            post_data: $( 'form.checkout' ).serialize()
        };

        jQuery.post( wc_custom_checkout.ajax_url, data, function( response ) {
            $( 'body' ).trigger( 'update_checkout' );
        });

    }

    $( "form.checkout" ).on( "change", "input.qty", function( e ) {
        wc_checkout_update(e);
    });

});