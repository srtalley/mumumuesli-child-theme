//version: 1.1.4
jQuery(function($) {
  $(document).ready(function(){

    //change the continue shopping link in the cart 
    $('.woocommerce-cart-form__contents a.continue-shopping').attr('href', '/shop');

    //update the single product page "pay with credit card" link with the quantity selected
    if($('.pay-with-cc-product-page').length) {
      $('.single-product .quantity input[type="number"].qty').on('change', function() {
        var new_product_quantity = $(this).val();
        $('.pay-with-cc-product-page a.single_add_to_cart_button').each(function(){
          var current_url = $(this).attr('href');
          current_url = current_url.replace(/((\?|&)quantity\=)[0-9]*/, '$1' + new_product_quantity);
          $(this).attr('href', current_url);
        })
      });
      // $('.pay-with-cc-product-page').on('click')
    }

    // See if there are reviews and update the ID
    var ewd_urp_review_blocks = $('.ewd-urp-review-header');
    if($(ewd_urp_review_blocks).length) {
      $(ewd_urp_review_blocks).each(function(){
        ewd_urp_review_block_data = $(this).data('postid').substring(4);
        $(this).attr('id', 'review-' + ewd_urp_review_block_data);
      });
    }

    // Select the Square payment method
    if($('.woocommerce-checkout').length) {
      // var pay_by_cc = param('pay_by_cc');
      var pay_by_cc = getUrlParameter('pay_by_cc');
      if(pay_by_cc == '1') { 
        if($('#payment_method_square_credit_card').length){
          $('#payment_method_square_credit_card').click();
        }
      }
    }

  }); // end document ready

  var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
      }
  };
});
