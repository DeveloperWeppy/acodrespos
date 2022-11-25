
"use strict";
var cartContent=null;
var cartItemId=null;
var ordenId=null;
var commentOrd=null;
var cartSessionId=null;
var orderContent=null;
var receiptPOS=null;
var cartTotal=null;
var ordersTotal=null;
var footerPages=null;
var total=null;
var expedition=null;
var modalPayment=null;
var carro=null;
var cartContentPersons=null;
var valor_propi = 0;

$('#localorder_phone').hide();



/**
 *
 * @param {Number} net The net value
 * @param {Number} delivery The delivery value
 * @param {String} expedition 1 - Delivery 2 - Pickup 3 - Dine in
 */
function updatePrices(net,delivery,expedition){

  var porcentaj_propina = $('#porcentaje_propina').val();
  $('#spanporcentaje_propina').hide();
  var span_propi_porcen = $('#spanporcentaje_propina').text(porcentaj_propina+"%");
  

  net=parseFloat(net);
  delivery=parseFloat(delivery);
  var formatter = new Intl.NumberFormat(LOCALE, {
    style: 'currency',
    currency:  CASHIER_CURRENCY,
  });
  
  var deduct=parseFloat(cartTotal.deduct);
  //console.log("Deduct is "+deduct);

  //totalPrice -- Subtotal
  //withDelivery -- Total with delivery

  //Subtotal
  cartTotal.totalPrice=net;
  cartTotal.totalPriceFormat=formatter.format(net);

  if(expedition==1){
    //Delivery
    cartTotal.delivery=true;
    cartTotal.deliveryPrice=delivery;
    cartTotal.deliveryPriceFormated=formatter.format(delivery);

    //Total
    cartTotal.withDelivery=net+delivery-deduct;
    cartTotal.withDeliveryFormat=formatter.format(net+delivery-deduct);
    total.totalPrice=net+delivery-deduct;

     //modalPayment updated
    modalPayment.totalPrice=cartTotal.withDelivery;
    modalPayment.totalPriceFormat=cartTotal.withDeliveryFormat;
    modalPayment.received=0;


  }else{
   
    //No delivery
    //Delivery
    cartTotal.delivery=false;

    //Total
    cartTotal.withDelivery=net-deduct;
    cartTotal.withDeliveryFormat=formatter.format(net-deduct);
    total.totalPrice=net-deduct;

     //modalPayment updated
    modalPayment.totalPrice=net-deduct;
    modalPayment.totalPriceFormat=formatter.format(net-deduct);
    modalPayment.totalPropinaFormat=formatter.format(0);
    modalPayment.received=0;

    $('#ask_propina_check').change(function() {
      if (this.checked) {
        $('#autoprop').show();
        $('#addprop').hide();
        $('#edit_propina_check').prop('checked',false);

        valor_propi = ((net-deduct)*porcentaj_propina)/100;
        modalPayment.totalPropinaFormat=formatter.format(valor_propi);

        //modalPayment updated
        modalPayment.totalPrice=net-deduct+valor_propi;
        modalPayment.totalPriceFormat=formatter.format(net-deduct+valor_propi);
        $('#spanporcentaje_propina').show();
      } else {
        modalPayment.totalPrice=net-deduct;
        modalPayment.totalPriceFormat=formatter.format(net-deduct);
        modalPayment.totalPropinaFormat=formatter.format(0);
        $('#spanporcentaje_propina').hide();
      }
    });

    $('#edit_propina_check').change(function() {
      if (this.checked) {
        $('#ask_propina_check').prop('checked',false);
        $('#autoprop').hide();
        $('#addprop').show();
        valor_propi = Number($(".propi").val()); 
        modalPayment.totalPropinaFormat=formatter.format(valor_propi);

        //modalPayment updated
        modalPayment.totalPrice=net-deduct+valor_propi;
        modalPayment.totalPriceFormat=formatter.format(net-deduct+valor_propi);
        $('#spanporcentaje_propina').show();
        
      } else {
        $('#autoprop').show();
        $('#addprop').hide();
        modalPayment.totalPrice=net-deduct;
        modalPayment.totalPriceFormat=formatter.format(net-deduct);
        modalPayment.totalPropinaFormat=formatter.format(0);
        $('#spanporcentaje_propina').hide();
      }
    });

    $(document).ready(function(){
      $(".propi").keyup(function(){
        valor_propi = Number($(".propi").val()); 
        modalPayment.totalPropinaFormat=formatter.format(valor_propi);
    
        //modalPayment updated
        modalPayment.totalPrice=net-deduct+valor_propi;
        modalPayment.totalPriceFormat=formatter.format(net-deduct+valor_propi);
      });
    });
   
  }
 
  setTimeout(() => {
    if ($(".cardAdd").length > 0 ) {
      $('#actualizarPedido').show();
    }else{
      $('#actualizarPedido').hide();
    }
    if($('#expedition').is(':visible')){
      $('#createOrder').hide();
   }else{
    if($('#orderId').is(':visible')){
      $('#createOrder').hide();
    }else{
      $('#createOrder').show();
    }
   }
   if(ordenId==0 || ordenId==null){
    $("#orderId").hide();
   }
 }, 100);
  total.lastChange=new Date().getTime();
  cartTotal.lastChange=new Date().getTime();
  cartTotal.expedition=1;
}




$("textarea#order_comment").change(function() {
   if(commentOrd !=$('textarea#order_comment').val()){
    $('#actualizarPedido').show();
   }
});
function updateSubTotalPrice(net,expedition){
  updatePrices(net,(cartTotal.deliveryPrice?cartTotal.deliveryPrice:0),expedition)
}

function addToCartVUE(){
  var addCartEndpoint='/cart-add';
  if(CURRENT_TABLE_ID!=null&&CURRENT_TABLE_ID!=undefined){
    addCartEndpoint+="?session_id="+CURRENT_TABLE_ID;
  }
 
    $("#itemsSelect").val([]);
    $('#itemsSelect').trigger('change');

    axios.post(addCartEndpoint, {
        id: $('#modalID').text(),
        quantity: $('#quantity').val(),
        personaccount: $('#personasdivision').find(":selected").text(),
        extras:extrasSelected,
        variantID:variantID
      })
      .then(function (response) {
          if(response.data.status){
            $('#productModal').modal('hide');
            getCartContentAndTotalPrice();

            openNav();
          }else{
            $('#productModal').modal('hide');
            js.notify(response.data.errMsg,"warning");
          }

      })
      .catch(function (error) {
        
      });
}

function getAllOrders(){
  axios.get('/poscloud/orders').then(function (response) {
    orderContent.items=response.data.orders;

    makeFree();
    response.data.orders.forEach(element => {
      makeOcccupied(element.id)
    });
    ordersTotal.totalOrders=response.data.count;
    //updateSubTotalPrice(response.data.total,true);
   })
   .catch(function (error) {
     
   });
}

function doMoveOrder(tableFrom,tableTo){
  
  axios.get('/poscloud/moveorder/'+tableFrom+'/'+tableTo).then(function (response) {
    if(response.data.status){
      js.notify(response.data.message, "success");
      getCartContentAndTotalPrice();
    }else{
      js.notify(response.data.message, "warning");
    }
    
    
  }).catch(function (error) {
    
    js.notify(error, "warning");
  });
}

function withSession(endpoint){
   if(CURRENT_TABLE_ID!=null&&CURRENT_TABLE_ID!=undefined){
    endpoint+="?session_id="+CURRENT_TABLE_ID;
   }
   return endpoint;
}


function clearDeduct(){
  cartTotal.deduct=0;
  $('#coupon_code').val("");
}
/**
 * getCartContentAndTotalPrice
 * This functions connect to laravel to get the current cart items and total price
 * Saves the values in vue
 */
function getCartContentAndTotalPrice(){

  //clear select item
  
  

  $('#createOrder').prop('disabled', false);
   axios.get(withSession('/cart-getContent-POS')).then(function (response) {
    if (typeof response.data.order_id !== 'undefined'){

          //aparece el modal de cargando, para darle tiempo al codigo de refresar la mesa
          Swal.fire({
            title: 'Cargando datos, Espere por favor...',
            button: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: false,
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading()
              },
          });


          ordenId=0;
          if(response.data.order_id>0){

             ordenId=response.data.order_id;
             commentOrd=response.data.comment;
            $('textarea#order_comment').val(response.data.comment);
            $("#orderId").html('Numero de orden: <a style="background-color: #28a745;" class="btn badge badge-success badge-pill" href="../orders/'+response.data.order_id+'">#'+response.data.order_id+'</a>');
            $("#orderId").show();
            $("#orderNumber").hide();
          }else{
            $("#orderId").hide();
          }
    }else{
      ordenId=0;
      $("#orderId").hide();
      $("#orderNumber").show();
    }
    cartSessionId=response.data.id;
    cartContent.items=response.data.data;
    //cartTotal.deduct=0;

    $("#mesaid").val(cartSessionId);
    //console.log(response.data);

    var obj=response.data.config;
    
    if( Object.keys(obj).length != 0 ){
      expedition.config=response.data.config;

      //Set the dd
      if(response.data.config.delivery_area){
        $("#delivery_area").val(response.data.config.delivery_area);
        $('#delivery_area').trigger('change');
        cartTotal.deliveryPrice=DELIVERY_AREAS[response.data.config.delivery_area];
      }
      if(response.data.config.timeslot){
        $("#timeslot").val(response.data.config.timeslot);
        $('#timeslot').trigger('change');
      }
    }
    /*** script que permite generar la división de cuentas ***/
    var formatter = new Intl.NumberFormat(LOCALE, {
      style: 'currency',
      currency:  CASHIER_CURRENCY,
    });
    var nuevovalor = [];
    var objperso = response.data.data;
    //console.log(objperso);
    var clave = Object.values(objperso);
    if( clave.length != 0 ){
      Object.entries(objperso).forEach(([key, value]) => {
        var persons = value.personaccount;
        var saldounit = value.price;
        var quantity = value.quantity;

        if (persons != null) {
          $('#card_division_personas').show();
        }else{
          $('#card_division_personas').hide();
        }

        var saldo = saldounit*quantity;
        var array = {'nombre':persons, 'saldo':saldo};
        nuevovalor.push(array);
      });
      const miCarritoSinDuplicados = nuevovalor.reduce((acumulador, valorActual) => {
        const elementoYaExiste = acumulador.find(elemento => elemento.nombre === valorActual.nombre);
        if (elementoYaExiste) {
          return acumulador.map((elemento) => {
            if (elemento.nombre === valorActual.nombre) {
              return {
                ...elemento,
                saldo: elemento.saldo + valorActual.saldo
              }
            }
      
            return elemento;
          });
        }
      
        return [...acumulador, valorActual];
      }, []);
      var data_array = [];
      Object.entries(miCarritoSinDuplicados).forEach(([key, value]) => {
         value.saldo= formatter.format(value.saldo);
         data_array[key] = value;
      });
      cartContentPersons.items=data_array;
      
       
    }else{
      $('#card_division_personas').hide();
    }
    updateSubTotalPrice(response.data.total,EXPEDITION);
    $(".listItemCart").each(function(){
      if($(this).attr("data")=="0"){
      
       $(this).css("border","1px solid #0a8eff");
       }
   });
   })
   .catch(function (error) {
     
   });

   //On the same call if POS, call get order
   if(IS_POS){
    getAllOrders();
   }

 };

 function setDeduct(deduction){
  var formatter = new Intl.NumberFormat(LOCALE, {
    style: 'currency',
    currency:  CASHIER_CURRENCY,
  });
  
  cartTotal.deduct=deduction;
  cartTotal.deductFormat=formatter.format(deduction);
  total.lastChange=null;
  cartTotal.lastChange=null;
  getCartContentAndTotalPrice();
}


function applyDiscount(){
  var code = $('#coupon_code').val();

  

  axios.post('/coupons/apply', {code: code,cartValue:cartTotal.totalPrice}).then(function (response) {
      if(response.data.status){
          //$("#promo_code_btn").attr("disabled",true);
          //$("#promo_code_btn").attr("readonly");
         // $("#promo_code_war").hide();
          //$("#promo_code_succ").show();
          setDeduct(response.data.deduct);
          js.notify(response.data.msg,"success");
          //$('#promo_code_btn').hide();
          //$( "#coupon_code" ).prop( "disabled", true );
      }else{
          //$("#promo_code_succ").hide();
          //$("#promo_code_war").show();
          js.notify(response.data.msg,"warning");
      }
  }).catch(function (error) {
    applyDiscount();
  });
}

function updateExpeditionPOS(){
  var dataToSubmit={
    table_id:CURRENT_TABLE_ID,
    client_name:$('#client_name').val(),
    client_phone:$('#client_phone').val(),
    timeslot:$('#timeslot').val(),
  };
  if(EXPEDITION==1){
    dataToSubmit.delivery_area=$('#delivery_area').val();
    dataToSubmit.client_address=$('#client_address').val();
  }
  
  axios.post(withSession('/poscloud/orderupdate'), dataToSubmit).then(function (response) {

    if(response.data.status){
      js.notify(response.data.message, "success");
    }else{
      js.notify(response.data.message, "warning");
    }
    
    
  }).catch(function (error) {
    
    js.notify(error, "warning");
  });

}
function ocultarbtn(){
  if(EXPEDITION!=3){
    $('.ckpropina').hide();
    $('.input-persona').hide();
  }else{
    $('.ckpropina').show();
    $('.input-persona').show();
  }
}


function submitOrderPOS(tipo=0){


  if(tipo==1){ $('#createOrder').prop('disabled', true); }
  if(tipo==2){ $('#actualizarPedido').prop('disabled', true); }
  if(tipo==0){ $('#submitOrderPOS').prop('disabled', true); localStorage.removeItem(CURRENT_TABLE_ID); }

    if($('#paymentType').val()=="transferencia" && $('#img_payment')[0].files.length === 0 ){
      $("#img_payment").addClass('is-invalid');
      return false;
    }

  //EXPEDITION=1 enviar,EXPEDITION=2 recibir ,3=en mesa,

  
  
  $('#submitOrderPOS').hide();
  $('#indicator').show();

  

  var dataToSubmit={
    table_id:CURRENT_TABLE_ID,
    paymentType:$('#paymentType').val(),
    paymentId:$('#paymentId').val(),
    paymentType2:$('#paymentType2').val(),
    expedition:EXPEDITION,
    tipo:tipo,
    order_id:ordenId,
    cart_id:cartSessionId,
    propina:valor_propi,
    number_people:$('#form_number_people').val(),
    order_comment:$('textarea#order_comment').val()
  };


  if(EXPEDITION==1||EXPEDITION==2){ 
    //Pickup OR deliver
    dataToSubmit.custom={
      client_id:selectClientId,
      client_name:selectClientText,
      client_phone:$( "#client_phone").val(),
    }
    dataToSubmit.phone=$('#client_phone').val();
    dataToSubmit.timeslot=$('#timeslot').val();
    if(EXPEDITION==1){
      
      dataToSubmit.addressID=$('#client_address').val();
      dataToSubmit.custom.deliveryFee=cartTotal.deliveryPrice;
    }
  }
 
  if(cartTotal.deduct>0){
    dataToSubmit.coupon_code=$('#coupon_code').val();
  }


  axios.post(withSession('/poscloud/order'), dataToSubmit).then(function (response) {

    //subir imagen factura
    submitImage(response.data.id);

   
    $('#submitOrderPOS').show();
    $('#indicator').hide();

    $('#modalPayment').modal('hide');

    

    $('#paymentType').val("cash");
    $("#client_name").val("").trigger('change');
    $('#paymentId').val("");
    $('#paymentType2').val("");
  
    //Call to get the total price and items
    getCartContentAndTotalPrice();

    if(response.data.status){
      $('textarea#order_comment').val("");
      
      window.showOrders();
      receiptPOS.order=response.data.order;
      if(tipo==0){
        js.notify(response.data.message, "success");
        $('#modalPOSInvoice').modal('show');
        $('#modalPOSInvoice').attr('data-id',response.data.order.id);

        
        if ($('#ask_propina_check').is(":checked") || $('#edit_propina_check').is(":checked")) {
          //facturapos
          receiptPOS.totalPropina = valor_propi;
        }else{
          receiptPOS.totalPropina = 0;
        } 
        $("#ask_propina_check").prop("checked", false);
        $("#edit_propina_check").prop("checked", false);
      }else{
        if(tipo==2){
          js.notify('Orden Actualizada', "success");
        }else{
          js.notify('Orden registrada', "success");
        }
        
      }
    }else{
      js.notify(response.data.message, "warning");
    }
    
    
  }).catch(function (error) {
    
    $('#modalPayment').modal('hide');
    $('#submitOrderPOS').show();
    $('#indicator').hide();
    js.notify(error, "warning");
  });
}


function submitImage(orderid){

    var formData = new FormData($('#formImgPayment')[0]);
    formData.append('orderid',orderid);
    formData.append('cuentaid',$('#paymentId').val());
    formData.append('tipotarjeta',$('#paymentType2').val());
    $.ajax({
        url: withSession('/poscloud/order'),
        type: 'POST',
        success: function (data) {
            $('#img_payment').val(null);
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });

}

/**
 * Removes product from cart, and calls getCartConent
 * @param {Number} product_id
 */
function removeProductIfFromCart(product_id){
    axios.post(withSession('/cart-remove'), {id:product_id,orderId:ordenId}).then(function (response) {
      getCartContentAndTotalPrice();
      
    }).catch(function (error) {
      
    });
 }

 /**
 * Update the product quantity, and calls getCartConent
 * @param {Number} product_id
 */
function incCart(product_id){
  axios.get(withSession('/cartinc/'+product_id+"/"+ordenId)).then(function (response) {
    getCartContentAndTotalPrice();
  }).catch(function (error) {
    
  });
}


function decCart(product_id){
  axios.get(withSession('/cartdec/'+product_id+"/"+ordenId)).then(function (response) {
    getCartContentAndTotalPrice();
  }).catch(function (error) {
    
  });
}
//add extras to cart
function updataObser(){
  $("#modalObservacion").modal('hide');
  Swal.fire({
    title: 'Validando datos, espere por favor...',
    button: false,
    showConfirmButton: false,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showCancelButton: false,
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading()
        },
  });
  axios.post(withSession('/updataObser'),{id:cartItemId,orderid:ordenId,observacion:$('textarea#item_observacion').val()}).then(function (response) {
    Swal.fire({
      title: 'Actualizado',
      icon: 'success',
      button: true,
      timer: 1000
    });
    getCartContentAndTotalPrice();
  }).catch(function (error) {
    Swal.fire({
      title: 'Los datos proporcionados no son válidos',
      text:mensajeError,
      icon: 'error',
      button: true,
      timer: 2000
     });
     $("#modalObservacion").modal('show');
  });
}

//GET PAGES FOR FOOTER
function getPages(){
    axios.get('/footer-pages').then(function (response) {
      footerPages.pages=response.data.data;
    })
    .catch(function (error) {
      
    });

};

function dineTypeSwitch(mod){
  

  $('.tablepicker').hide();
  $('.takeaway_picker').hide();

  if(mod=="dinein"){
    $('.tablepicker').show();
    $('.takeaway_picker').hide();

    //phone
    $('#localorder_phone').hide();
  }

  if(mod=="takeaway"){
      $('.tablepicker').hide();
      $('.takeaway_picker').show();

    //phone
    $('#localorder_phone').show();
  }

}

function orderTypeSwither(mod){
      

      $('.delTime').hide();
      $('.picTime').hide();

      if(mod=="pickup"){
          updatePrices(cartTotal.totalPrice,null,false)
          $('.picTime').show();
          $('#addressBox').hide();
      }

      if(mod=="delivery"){
          $('.delTime').show();
          $('#addressBox').show();
          getCartContentAndTotalPrice();
      }
}

setTimeout(function(){
  if(typeof initialOrderType !== 'undefined'){
    
    orderTypeSwither(initialOrderType);
  }else{
    
  }

},1000);

function chageDeliveryCost(deliveryCost){
  $("#deliveryCost").val(deliveryCost);
  updatePrices(cartTotal.totalPrice,deliveryCost,true);
  
}

 //First we beed to capture the event of chaning of the address
  function deliveryAddressSwithcer(){
    $("#addressID").change(function() {
      //The delivery cost
      var deliveryCost=$(this).find(':selected').data('cost');

      //We now need to pass this cost to some parrent funct for handling the delivery cost change
      chageDeliveryCost(deliveryCost);


    });

  }

  function deliveryTypeSwitcher(){
    $('.picTime').hide();
    $('input:radio[name="deliveryType"]').change(function() {
      orderTypeSwither($(this).val());
    })
  }

  function dineTypeSwitcher(){
    $('input:radio[name="dineType"]').change(function() {
      $('.delTimeTS').hide();
      $('.picTimeTS').show();
      dineTypeSwitch($(this).val());
    })
  }


  

  function paymentTypeSwitcher(){

    $('input:radio[name="paymentType"]').change(
      
      function(){
      
          //HIDE ALL
          $('#totalSubmitCOD').hide()
          $('#totalSubmitStripe').hide()
          $('#stripe-payment-form').hide()

          //One for all
          $('.payment_form_submiter').hide()
          

          if($(this).val()=="cod"){
              //SHOW COD
              $('#totalSubmitCOD').show();
          }else if($(this).val()=="stripe"){
              //SHOW STRIPE
              $('#totalSubmitStripe').show();
              $('#stripe-payment-form').show()
          }else{
            $('#'+$(this).val()+'-payment-form').show()
          }
      });
  }

  function deliveryAreaSwitcher(){
    $('#delivery_area').on('select2:select', function (e) {
      var data = e.params.data;
      
      updatePrices(cartTotal.totalPrice,DELIVERY_AREAS[data.id],1);
    });
}

window.onload = function () {

  

  //Expedition
  expedition=new Vue({
    el: '#expedition',
    data: {
      config:{}
    },
  })

  //VUE CART
  cartContent = new Vue({
    el: '#cartList',
    data: {
      items: [],
      config:{}
    },
    methods: {
      remove: function (product_id) {
        removeProductIfFromCart(product_id);
      },
      incQuantity: function (product_id){
        incCart(product_id)
      },
      decQuantity: function (product_id){
        decCart(product_id)
      },
      modalObserv: function (product_id,name,observ){
        $('textarea#item_observacion').val(observ);
        $("#modalObservacion").modal('show');
        cartItemId=product_id;
        $("#titleModalOb").html('Observacion de '+name);
      },
    }
  })

    //VUE COMPLETE ORDER TOTAL PRICE
    cartContentPersons = new Vue({
      el: '#cartListPerson',
      data: {
        items: [],
      }
    })

  orderContent = new Vue({
    el: '#orderList',
    data: {
      items: [],
    },
    methods:
    {
      openDetails:function(id,receipt_number){
        
        window.openTable(id,"#"+receipt_number);
      }
    }
  })

  //GET PAGES FOR FOOTER
  getPages();

  //Payment Method switcher
  paymentTypeSwitcher();

  //Delivery type switcher
  deliveryTypeSwitcher();

  //For Dine in / takeout
  dineTypeSwitcher();

  //Activate address switcher
  deliveryAddressSwithcer();

  //Activate delivery area switcher
  deliveryAreaSwitcher();


  //VUE FOOTER PAGES
  footerPages = new Vue({
      el: '#footer-pages',
      data: {
        pages: []
      }
  })

  //VUE COMPLETE ORDER TOTAL PRICE
  total = new Vue({
    el: '#totalSubmit',
    data: {
      totalPrice:0
    }
  })


  //VUE TOTAL
  cartTotal= new Vue({
    el: '#totalPrices',
    data: {
      totalPrice:0,
      deduct:0,
      deductFormat:"",
      minimalOrder:0,
      totalPriceFormat:"",
      deliveryPriceFormated:"",
      withDeliveryFormat:"",
      delivery:true
    }
  })

  modalPayment= new Vue({
    el: '#modalPayment',
    data: {
      totalPrice:0,
      minimalOrder:0,
      totalPriceFormat:"",
      totalPropinaFormat:"",
      deliveryPriceFormated:"",
      delivery:true,
      valid:false,
      received:0
    },
    methods: {
      onChange(event) {
          //console.log(event.target.value)
          if(event.target.value=="onlinepayments"||event.target.value=="cardterminal"||event.target.value=="transferencia"){
            this.received=this.totalPrice;
           
          }
      }
  }
  })


  receiptPOS=new Vue({
    el:"#modalPOSInvoice",
    data:{
      order:null,
      totalPropina:0
    },

    methods: {
      moment: function (date) {
        return moment(date);
      },
      decodeHtml: function (html) {
        var txt = document.createElement("textarea");
        txt.innerHTML = html;

        //console.log("specia");
        //console.log(txt.value)
        return txt.value;
      },
      formatPrice(price){
        var locale=LOCALE;
        if(CASHIER_CURRENCY.toUpperCase()=="USD"){
            locale=locale+"-US";
        }
    
        var formatter = new Intl.NumberFormat(locale, {
            style: 'currency',
            currency:  CASHIER_CURRENCY,
        });
    
        var formated=formatter.format(price);
    
        return formated;
      },
      date: function (date) {
        return moment(date).format('MMMM Do YYYY, h:mm:ss a');
      }
    },
  })
  

  //VUE TOTAL
  ordersTotal= new Vue({
    el: '#ordersCount',
    data: {
      totalOrders:0,
    }
  })

  //Call to get the total price and items
  getCartContentAndTotalPrice();

  var addToCart1 =  new Vue({
    el:'#addToCart1',
    methods: {
        addToCartAct() {

          addToCartVUE();
        },
    },
  });
}





