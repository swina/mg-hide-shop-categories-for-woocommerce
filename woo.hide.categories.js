/* Hide Categories Woocomerce jQuery functions */


jQuery(document).ready(function(){
  jQuery('body').delegate('.mood_hide_category','click',function(){
    let checked = '';
    if ( jQuery(this).is(':checked') ){
      checked = 'on';
    };
    let working = jQuery('.working_' + jQuery(this).data('id'))
    jQuery('body').append('<div class="overlay"><h2><div class="lds-ring"><div></div><div></div><div></div><div></div></div></h2></div>');
    working.html('<div class="lds-ring-small"><div></div><div></div><div></div><div></div></div>');
    jQuery.post( wp_hide_category_ajax.ajax_url,
      {
        _ajax_nonce: wp_hide_category_ajax.nonce ,//nonce
        action: "hide_category_woo",            //action
        id: jQuery(this).data('id'),
        checked: checked
      },function(result,success){
        if ( success != 'success' ){
          alert('Error updating data')
        }
        jQuery('.overlay').remove();
        working.html('');
      });
  })

  jQuery('body').delegate('.overlay','click',function(){
    jQuery(this).remove();
  });
});
