<!--
=========================================================
* Soft UI Dashboard - v1.0.1
=========================================================

* Product Page: https://www.creative-tim.com/product/black-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://github.com/creativetimofficial/black-dashboard/blob/master/LICENSE.md)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('softd') }}/img/apple-icon.png">
  <link rel="icon" type="image/png" href="{{ asset('softd') }}/img/favicon.png">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>
    {{ $vendor->name." - ".config('app.name')}}
  </title>
  <!--     Fonts and icons     -->
  <link href="{{ asset('css') }}/gfonts.css" rel="stylesheet">

  <!-- Nucleo Icons -->
  <link href="{{ asset('softd') }}/css/nucleo-icons.css" rel="stylesheet" />
  <link href="{{ asset('softd') }}/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="{{ asset('vendor') }}/fa/fa.js" crossorigin="anonymous"></script>
  <link href="{{ asset('softd') }}/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('softd') }}/css/soft-ui-dashboard.css?v=1.0.1" rel="stylesheet" />

  <!-- Select2  -->
  <link type="text/css" href="{{ asset('custom') }}/css/select2.min.css" rel="stylesheet">

  <!--Custom CSS -->
  <link type="text/css" href="{{ asset('custom') }}/css/custom.css" rel="stylesheet">

  <link type="text/css" href="{{ asset('custom') }}/css/pos.css" rel="stylesheet">

  @laravelPWA

</head>

<body class="g-sidenav-show bg-gray-100">
  <div class="main-content position-relative bg-gray-100">
    @include('poscloud::navbar')
      <div class="nav-wrapper position-relative end-0 mt-2" id="floorAreas">
        <ul class="nav nav-pills nav-fill p-1 bg-transparent " role="tablist">
          @foreach ($vendor->areas as $key =>$area)
            <li class="nav-item" style="padding-top:8px" >
              <a style="height: 50px;"  class="nav-link mb-0 px-0 py-1 {{$key==0?"active":""}}" id="area-{{ $area->id }}-tab"  data-bs-toggle="tab" data-bs-target="#area-{{ $area->id }}" type="button" role="tab" aria-controls="area-{{ $area->id }}" aria-selected="{{$key==0?"tru":"false"}}"><strong>{{ $area->name }}</strong></a>
            </li>
          @endforeach
        </ul>
      </div>
      @yield('floorPlan')
      @yield('orders')
      @yield('orderDetails')  
    </div>
  </div>
  <input type="hidden" name="propina" id="porcentaje_propina" value="{{$vendor->propina}}">
  <!--   Core JS Files   -->
  <script src="{{ asset('softd') }}/js/core/popper.min.js"></script>
  <script src="{{ asset('softd') }}/js/core/bootstrap.min.js"></script>
  <script src="{{ asset('softd') }}/js/plugins/smooth-scrollbar.min.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('softd') }}/js/soft-ui-dashboard.min.js?v=1.0.1"></script>

  <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>

  <!-- Import Vue -->
  <script src="{{ asset('vendor') }}/vue/vue.js"></script>
  <!-- Import AXIOS --->
  <script src="{{ asset('vendor') }}/axios/axios.min.js"></script>

  <!-- Import Interact --->
  <script src="{{ asset('vendor') }}/interact/interact.min.js"></script>
  
  <!-- Import Select2 --->
  <script src="{{ asset('vendor') }}/select2/select2.min.js"></script>

  <!-- printThis -->
  <script src="{{ asset('vendor') }}/printthis/printThis.js"></script> 
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
  <link type="text/css" href="{{ asset('css/dashboard.css') }}/" rel="stylesheet">
  <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">

   <!-- Add to Cart   -->
   <script>
      var LOCALE="<?php echo  App::getLocale() ?>";
      var CASHIER_CURRENCY = "<?php echo  config('settings.cashier_currency') ?>";
      var USER_ID = '{{  auth()->user()&&auth()->user()?auth()->user()->id:"" }}';
      var PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
      var PUSHER_APP_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
      var CASHIER_CURRENCY = "<?php echo  config('settings.cashier_currency') ?>";
      var LOCALE="<?php echo  App::getLocale() ?>";
      var SELECT_OR_ENTER_STRING="{{ __('Select, or enter keywords to search items') }}";
      var DELIVERY_AREAS = @json($deliveryAreasCost, JSON_PRETTY_PRINT);
      var IS_POS=true;
      var CURRENT_TABLE_ID=null;
      var EXPEDITION=3;
      var CURRENT_TABLE_NAME=null;
      var CURRENT_RECEIPT_NUMBER="";
      var SHOWN_NOW="floor"; //floor,orders,order
      var floorPlan=@json($floorPlan);

      // "Global" flag to indicate whether the select2 control is oedropped down).
      var _selectIsOpen = false;
      var datalistClient=@json($selectClient);
      var datalistPhone=@json($selectTelefono);
      var selectClientId=0;
      var selectClientText="";
      var mesaocupada = false;
   </script>
   <script src="{{ asset('custom') }}/js/cartPOSFunctions.js"></script>
   
   <!-- Cart custom sidemenu -->
   <script src="{{ asset('custom') }}/js/cartSideMenu.js"></script>

   <!-- All in one -->
   <script src="{{ asset('custom') }}/js/js.js?id={{ config('config.version')}}"></script>

   <!-- Notify JS -->
   <script src="{{ asset('custom') }}/js/notify.min.js"></script>
   <link rel="stylesheet" href="{{asset('vendor/intltelinput/build/css/intlTelInput.css')}}">
<script src="{{asset('vendor/intltelinput/build/js/intlTelInput.js')}}"></script>
<script src="{{asset('vendor/intltelinput/build/js/utils.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @stack('js')
  @yield('js')

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

  </script>


  <script type="text/javascript">
  
  $('#client_name').select2({
      width: '100%',
      placeholder: "Nombre o Documento",
      data: datalistClient
  });
  $('#client_name').change(function() {
    selectClientId=$( "#client_name").val();
    selectClientText=$("#client_name option:selected").text();
    $( "#client_phone").val(datalistPhone[$( "#client_name").val()]);
  });
  if(datalistClient.length>0){
     if(datalistClient[0]['text']=="cliente general"){
      $("#client_name").val(datalistClient[0]['id']).trigger('change');
     }else{
      $("#client_name").val("").trigger('change');
     }
   
  }
  var urlbasse="{{url('/pdf');}}";
      $(function() {

        $('#printPos').on("click", function () {
         // $("#posRecipt").printThis(); 
         printJS(urlbasse+"/"+$('#modalPOSInvoice').attr('data-id'));
        });

        $("#paymentType").on('change', function() {
          
          if ($(this).val()=='transferencia') {
            $('#selecuenta').show()
            $('#loadarchivo').show()
            $('#seletipocuenta').hide()
            $('.selecuenta2').hide()
          }else if ($(this).val()=='cardterminal') {
            $('#selecuenta').hide()
            $('#seletipocuenta').hide()
            $('#loadarchivo').hide()
            $('.selecuenta2').show()
          }else {
            $('#selecuenta').hide()
            $('#seletipocuenta').hide()
            $('#loadarchivo').hide()
            $('.selecuenta2').hide()
          }
        });

        //INterval getting orders
        setInterval(() => {
          getAllOrders();

        }, 12000);


        $('#orderTo').select2({
            dropdownParent: $('#modalSwitchTables')
        });
        $('#orderFrom').select2({
            dropdownParent: $('#modalSwitchTables')
        });
        $('#swithTableButton').on('click',function(e){
          $('#modalSwitchTables').modal('hide');
          doMoveOrder($('#orderFrom').val(),$('#orderTo').val());
        })

      
        $('.select2init').select2({
          id:"-1",
          placeholder:"Buscar ..."
        });

        $('select').on('change', function() {
          if(this.id=="itemsSelect"&&this.value!=""){
            setCurrentItem( this.value );
          }
          
        });


        // Initialize the select2.
      const $mySelect = $("#itemsSelect");
      $mySelect.select2({
        placeholder: { 
          id: "",
          text: SELECT_OR_ENTER_STRING
        }, 
        selectOnClose: true,
      });

      $mySelect
        .on("select2:open", event => {
          _selectIsOpen = true;
        })
        .on("select2:close", event => {
          _selectIsOpen = false;
        })
        .on("select2:select", (event) => {});

        $("body")
        .on("select2:opening", event => {})
        
        .on("keypress", event => {
          if ($(event.target).is('input, textarea, select')) return;
          if (_selectIsOpen) {
            return;
          }
          if(SHOWN_NOW!="order"){
            //But first check if in order
            return;
          }


          if (event.keyCode === 13) {
              if($('#addToCart1').is(":visible")) {
                addToCartVUE();
              }
              return;
            }
          const charCode = event.which;
          if (
            !(event.altKey || event.ctrlKey || event.metaKey) &&
            ((charCode >= 48 && charCode <= 57) ||
              (charCode >= 65 && charCode <= 90) ||
              (charCode >= 97 && charCode <= 122))
          ) {
            $mySelect.select2("open");
            $("input.select2-search__field")
              .eq(0)
              .val(String.fromCharCode(charCode));
          }
        });


        //Get all order - vue
        getAllOrders();

        $('#orderList .orderRow').hover(function() {
                  $(this).addClass('hoverTableRow');
            }, function() {
                $(this).removeClass('hoverTableRow');
        });

        $('#orderList tr').on( "click", function() {
          var id=$( this ).attr('id');
        });
      })

    
  

    function showOrders() {
     
      $("#floorTabs").hide();
      $("#floorAreas").hide();
      $("#orders").show();
      $("#orderDetails").hide();
      $("#createOrder").hide();
      SHOWN_NOW="orders";
    }

   
    function showOrderDetail(id) {




     $('textarea#order_comment').val("");
     $("#floorTabs").hide();
     $("#floorAreas").hide();
     $("#orders").hide();
     $("#orderDetails").show();

     //Set the name of the table
     $("#tableName").html(CURRENT_TABLE_NAME);
     $("#orderNumber").html(CURRENT_RECEIPT_NUMBER);

     EXPEDITION==1?$('#client_address_fields').show():$('#client_address_fields').hide(); 
     EXPEDITION==3?$('#expedition').hide():$('#expedition').show();
     EXPEDITION==3?$('#row_propina').show():$('#row_propina').hide();
     if (EXPEDITION==3) {
      $('#row_propina').show();
      $('#autoprop').show();
      $('.input-persona').show();
      $('#form_number_people').show();
      $('#expedition').hide();
     } else {
      $('#row_propina').hide();
      $('#expedition').show();
      $('#autoprop').hide();
      $('#form_number_people').hide();
      $('.input-persona').hide();
     }


     SHOWN_NOW="order";
     clearDeduct();
     $('#coupon_code').html("");
   }


   function moveOrder(){
    //Find Occupied
    //Find Free tables
    var occupiedList={};
    var freeList={};
    $('.resize-drag').each(function(i, obj) {
        var id=obj.id.replace("drag-","");
        if($("#"+obj.id).hasClass('occcupied')){
          occupiedList[id]=floorPlan[id];
        }else{
          freeList[id]=floorPlan[id];
        }
    });
    
    
    //If occupied or free is empty, show a message
    if(Object.keys(occupiedList).length==0){
      js.notify("No hay órdenes activas en las mesas", "warning");
    }else if(Object.keys(freeList).length==0){
      js.notify("No hay mesas libres", "warning");
    }else{

      //Set selects
      $('#orderFrom').empty();
      $('#orderTo').empty();
      Object.keys(occupiedList).map((key)=>{
        var newOption = new Option(occupiedList[key], key, false, false);
        $('#orderFrom').append(newOption);//.trigger('change');
      })
      Object.keys(freeList).map((key)=>{
        var newOption = new Option(freeList[key], key, false, false);
        $('#orderTo').append(newOption);//.trigger('change');
      })
      $('#orderFrom').trigger("change");
      $('#orderTo').trigger("change");
     

      //Switch Tables modal
      $('#modalSwitchTables').modal('show');
    }

    

    


    //Open the modal
   }

   function createDeliveryOrder() {
    $("#client_name").val("").trigger('change');
      CURRENT_TABLE_ID= 1+""+(new Date().getTime()+"").substring(6)
      CURRENT_TABLE_NAME="Nueva orden de entrega";
      EXPEDITION=1;
      expedition.config={};
      getCartContentAndTotalPrice();

      showOrderDetail(CURRENT_TABLE_ID);
   }

   function createPickupOrder() { 

    $("#client_name").val("").trigger('change');

      CURRENT_TABLE_ID= (new Date().getTime()+"").substring(6)
      CURRENT_TABLE_NAME="Nuevo pedido para llevar";
      EXPEDITION=2;
      expedition.config={};
      getCartContentAndTotalPrice();
    
      showOrderDetail(CURRENT_TABLE_ID);

    }


    function showFloor() {

      $("#floorTabs").show();
      $("#floorAreas").show();
      $("#orders").hide();
      $("#orderDetails").hide();
      SHOWN_NOW="floor";
    }
 //ejecuta al dar clic en la mesa

    function openTable(id,receipt_number) {
      CURRENT_TABLE_ID=id;

      CURRENT_RECEIPT_NUMBER=receipt_number;
      idLength=(id+"").length;
      if(idLength<6){
        CURRENT_TABLE_NAME=floorPlan[id];
        EXPEDITION=3;

        
      }else if(idLength==7){
        CURRENT_TABLE_NAME="Pedido para llevar";
        EXPEDITION=2;
      }else{
        CURRENT_TABLE_NAME="Orden de entrega";
        EXPEDITION=1;
      }
      $("#row_names").hide();
      
      //console.log(mesaocupada);
      if(EXPEDITION==3){

        var ocupada = ocupacionMesa();
        
     
        
          var getlocal = JSON.parse(localStorage.getItem(CURRENT_TABLE_ID));

          if(getlocal != null && getlocal != "" && getlocal != false && getlocal != undefined){
              $("#modal-add-consumidor").modal("hide");
              $('.personitem').show();
              $('#card_division_personas').show();
          }else{

              $('.personitem').text("");
              $("#modal-add-consumidor").modal("show");
              $('#card_division_personas').hide();
              $('#ask_divide_check').change(function() {
                  if (this.checked) {
                      $("#span_dividir").text("Cuenta Dividida");
                      $("#row_names").show();
                      $("#btncontinuar").hide();
                  } else {
                      $("#span_dividir").text("Sin cuenta dividida");
                      $("#row_names").hide();
                      $("#btncontinuar").show();
                  }
              });
          }

          /*
          if(ocupada==0){
        }
        */

        
       
      }
      getCartContentAndTotalPrice();
      showOrderDetail(id);
    }

    function makeOcccupied(id){
      $('#drag-'+id).addClass('occcupied');
    }

    function makeFree(){
      $('.occcupied').removeClass('occcupied');
    }


    function ocupacionMesa(){

      var formData = new FormData();
      formData.append('table_id',CURRENT_TABLE_ID);
      $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{route('poscloud.ocupationTable')}}",
          type: 'POST',
          success: function (data) {
            alert(data);
          },
          data: formData,
          cache: false,
          contentType: false,
          processData: false
      });


      $('#modalMesaReservada').modal('show');
      alert(CURRENT_TABLE_ID);
      return 1;
    }
  </script>

  <script type="module">
    interact('.resize-drag')
    .on('tap', function (event) {

      //The drag id
      var dragid=event.currentTarget.id;
      var id=dragid.replace('drag-',"");
      openTable(id,"");
      event.preventDefault()
    });

    function initPhone(name){
        var input = document.querySelector("input[name='"+name+"']");
        if(input!=null){
            var iti=window.intlTelInput(input, {
              nationalMode:true,
                hiddenInput: name,
                //customContainer:"form-controls",
                autoHideDialCode:true,
                separateDialCode:true,
                autoPlaceholder:"aggressive",
                initialCountry: "auto",
                utilsScript: "/vendor/intltelinput/build/js/utils.js", 
                geoIpLookup: function(success, failure) {
                    $.get("https://ipinfo.io?token=c2999fc5e1aefc",function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "co";
                    success(countryCode);
                    });
                },
            });


        var reset = function() {
		  input.classList.remove("error");
          setTheHidden();
		};

        var setTheHidden =function(){
            var theHidden=document.querySelector("input[type=hidden][name='"+name+"']");
            theHidden.value = iti.getSelectedCountryData().dialCode+input.value;
            console.log(theHidden.value);
        }


		input.addEventListener('change', reset);
		input.addEventListener('keyup', reset);	 

        input.addEventListener("countrychange", function() {
            setTheHidden();
        });

        setTheHidden();

        }
    }
    $( ".btnFormClient" ).click(function() {
    setTimeout(() => {
            initPhone('phone');
        }, 1000);
   });
   $(document).ready(function() {
    $("#from-create-client").validate({
   rules: {
     name: {
       required: true,
     },
     number_identification: {
       required: true,
     },
     email: {
       required: true,
     },
     phone: {
       required: true,
     },

   },
   messages: {
     name: "Por favor ingrese el nombre",
     number_identification: "Por favor ingrese el documento de identificacion",
     email: "Por favor ingrese el email",
     phone: "Por favor el numero de telefono",
   
   },
   errorElement: 'span',
   errorPlacement: function (error, element) {
     error.addClass('invalid-feedback');
     element.closest('.form-group').append(error);
   },
   highlight: function (element, errorClass, validClass) {
     if($(element).attr('id')!="formPhone"){
      $(element).addClass('is-invalid');
     }
   },
   unhighlight: function (element, errorClass, validClass) {
     $(element).removeClass('is-invalid');
   },
   submitHandler: function(form,event){
    event.preventDefault();
             var formDataClient=new FormData(form);
             formDataClient.append( 'password', $( '#fromDocCleint' ).val() );
             formDataClient.append( 'password_confirmation', $( '#fromDocCleint' ).val() );
             $.ajax({
                headers: {
                       'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                type: "post",
                encoding:"UTF-8",
                url: "{{route('register')}}",
                data:formDataClient ,
                processData: false,
                contentType: false,
                dataType:'json',
                beforeSend:function(){
                  $('#modalRegister').modal('hide');
                  Swal.fire({
                           title: 'Validando datos, espere por favor...',
                           button: false,
                           showConfirmButton: false,
                           allowOutsideClick: false,
                           allowEscapeKey: false,
                           showCancelButton: false,
                           showConfirmButton: false,
                           timer: 4000,
                           timerProgressBar: true,
                               didOpen: () => {
                                   Swal.showLoading()
                               },
                       });
                }
             }).done(function( respuesta ) {

                  $.ajax({
                    method: "GET",
                    url: "/listclients/select",
                    dataType:'json',
                  }).done(function(resp) {
                    datalistClient=resp.selectClient;
                    datalistPhone=resp.selectTelefono;
                    $('#client_name').select2({
                        width: '100%',
                        placeholder: "Nombre o Documento",
                        data: datalistClient
                    });
                 });
                 Swal.fire({
                              title: 'Cliente registrado',
                              icon: 'success',
                              button: true,
                              timer: 2000
                          });
             }).fail(function( jqXHR,textStatus ) {
                var mensajeError="";
                if (typeof jqXHR.responseJSON.errors.email != "undefined"){
                     mensajeError="El correo electrónico ya se tomó.";
                }
                Swal.fire({
                              title: 'Los datos proporcionados no son válidos',
                              text:mensajeError,
                              icon: 'error',
                              button: true,
                              timer: 2000
                          });
                setTimeout(() => {
                   $('#modalRegister').modal('show');
                }, 2000);
            });
   }
 });
});

    </script>

    
     

</body>

</html>