jQuery(document).ready(function () {
      jQuery(document).on("click",'.piggyWhiteCircle',function(){
          var iconPos = 'TopRight';

          var _dataValue = jQuery(this).data('value');
          switch(_dataValue){
            case 'piggyTopLeft': iconPos = 'TopLeft';break;
            case 'piggyTopRight': iconPos = 'TopRight';break;  
            case 'piggyBottomLeft': iconPos = 'BottomLeft';break;  
            case 'piggyBottomRight': iconPos = 'BottomRight';break;  
          }
          jQuery('.piggyWhiteCircle img').hide();
          jQuery('.'+_dataValue+' img').show();
          jQuery('#iconPostion').val(iconPos);
      });


      var iconPos = jQuery('#iconPostion').val();
      jQuery('.piggyWhiteCircle img').hide();
      jQuery('.piggy'+iconPos+' img').show();

      jQuery(".default_discount").change(function () {
        if(jQuery(this).val() == 'custom'){
            jQuery("#custom_discount").focus();
        }
        else if ( jQuery(this).is(":checked") ) {
            jQuery("#custom_discount").val("");
        }
      });

      jQuery("#custom_discount").click(function(){
        jQuery("#default_discount4").prop('checked', true);
      });

      jQuery("#custom_discount").keyup(function () {
        if ( jQuery(this).val() != ''  ) {
            jQuery(".default_discount").prop('checked', false);
            jQuery("#default_discount4").prop('checked', true);
        }
      });

    // tabs
    function showTab(hash) {
        if ( hash != '' ) {
            jQuery(hash).trigger("click");
        }
    }
    showTab(location.hash);
});
var piggybaq = {

    openPop: function(url) {
        var popupWindow = window.open(url);
        window.addEventListener("focus", function(event) {
            setTimeout(function () {
                window.location.reload();
            },2000);
            window.removeEventListener("focus");
        });
    },
    
    priceChanged: function() {
        if (jQuery('#yourprice').val().trim() != '') {
            var sellprice = jQuery('#sellprice').val();
            var yourprice = jQuery('#yourprice').val();
            var price = (parseFloat(sellprice) + parseFloat(yourprice)).toFixed(2);
            jQuery("#newprice").val(price);
			jQuery("#newpriceSpan").html(price);
        } else {
            jQuery("#newprice").val(jQuery('#sellprice').val());
			jQuery("#newpriceSpan").html(jQuery('#sellprice').val());
        }
    },

    validaterequest: function() {
        var discountprice = jQuery('#discountprice').val();
        var yourprice = jQuery('#yourprice').val();
        var default_discount = jQuery('#default_discount').val();
        var reseller_price = jQuery('#newprice').val();
        var seller_price = jQuery('#sellprice').val();
        var owner_original_price =jQuery('#owneroriginalprice').val();
        var old_reseller_price = (seller_price / ((100-default_discount)/100)).toFixed(2);
        var old_actual_price = old_reseller_price * (default_discount/100);

        
        if (( parseFloat(seller_price) + parseFloat(yourprice) ) < parseFloat(owner_original_price)) {
            alert("Your price cannot be lesser than of " + owner_original_price);
            return false;
        } else {
            return true;
        }
    },

    toggleProductList: function (self, list) {
        location.hash = self.id;
        jQuery('.subsubsub .current').removeClass('current');
        jQuery(self).addClass('current');
        if ( list == 'piggybaq' ) {
            jQuery('#piggybaq_products').show();
            jQuery('#my_products').hide();
        } else {
            jQuery('#piggybaq_products').hide();
            jQuery('#my_products').show();
        }
    }

    
}