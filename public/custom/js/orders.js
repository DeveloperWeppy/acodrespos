
"use strict";
$(document).ready(function() { 
    $("#show-hide-filters").on("click",function(){

        if($(".orders-filters").is(":visible")){
            $("#button-filters").removeClass("ni ni-bold-up")
            $("#button-filters").addClass("ni ni-bold-down")
        }else if($(".orders-filters").is(":hidden")){
            $("#button-filters").removeClass("ni ni-bold-down")
            $("#button-filters").addClass("ni ni-bold-up")
        }

        $(".orders-filters").slideToggle();
    });
});

jQuery(document).on("click", ".change-status", function() {
    var $element = jQuery(this);
    var id = $element.attr('id');
    var url = '/orders/status';
    var data = {
        id: id
    }
    jQuery.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        encoding: "UTF-8",
        url: url,
        data: data,
        dataType: 'json',
        beforeSend:function(){
            //$element.val('Cargando');
        },
        success: function(response) {
            //console.log(response);
            if (response.status == 'servicio') {
                $element.find('span').removeAttr('class').attr('class', '');
                $element.find('span').addClass('btn');
                $element.find('span').addClass(response.class_status);
                $element.find('span').text(response.text_status);
                
            }
        }
    });
});
