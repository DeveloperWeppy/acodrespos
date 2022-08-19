<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="csrf-token" content="{{ csrf_token() }}">



        @yield('title')
        <title>{{ config('app.name', 'FoodTiger') }}</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

        <!-- Fonts -->
        <link href="{{ asset('css') }}/gfonts.css" rel="stylesheet">
        
        <!-- Icons -->
        <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
        <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
        <!-- Argon CSS -->
        <link type="text/css" href="{{ asset('argon') }}/css/argon.css?v=1.0.0" rel="stylesheet">
        <!-- Argon CSS -->
        <link type="text/css" href="{{ asset('custom') }}/css/custom.css" rel="stylesheet">
        <!-- Select2 -->
        <link type="text/css" href="{{ asset('custom') }}/css/select2.min.css" rel="stylesheet">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="{{ asset('vendor') }}/jasny/css/jasny-bootstrap.min.css">
        <!-- Flatpickr datepicker -->
        <link rel="stylesheet" href="{{ asset('vendor') }}/flatpickr/flatpickr.min.css">

         <!-- Font Awesome Icons -->
        <link href="{{ asset('argonfront') }}/css/font-awesome.css" rel="stylesheet" />

        <!-- Styles Personalizados -->
        <link href="{{ asset('css') }}/dashboard.css" rel="stylesheet">

        <!-- Lottie -->
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>


        <!-- Range datepicker -->
        <link rel="stylesheet" type="text/css" href="{{ asset('vendor') }}/daterangepicker/daterangepicker.css" />

        @yield('head')
        @laravelPWA
        @include('layouts.rtl')

        <!-- Custom CSS defined by admin -->
        <link type="text/css" href="{{ asset('byadmin') }}/back.css" rel="stylesheet">




    </head>
    <body class="{{ $class ?? '' }}">
        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @if(\Request::route()->getName() != "order.success"&&(\Request::route()->getName() != "selectpay"))
                @include('layouts.navbars.sidebar')
            @endif
        @endauth

        <div class="main-content">
            @include('layouts.navbars.navbar')
            @yield('content')
        </div>

        @guest()
            @include('layouts.footers.guest')
        @endguest

        <!-- Commented because navtabs includes same script -->
        <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
        <script src="{{ asset('vendor') }}/jasny/js/jasny-bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('argonfront') }}/js/core/popper.min.js" type="text/javascript"></script>
        <script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

        @yield('topjs')

        <script>
            var t="<?php echo 'translations'.App::getLocale() ?>";
           window.translations = {!! Cache::get('translations'.App::getLocale(),"[]") !!};
           
           
        </script>

        <!-- Navtabs -->


        <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <!-- Nouslider -->
        <script src="{{ asset('argon') }}/vendor/nouislider/distribute/nouislider.min.js" type="text/javascript"></script>

        <!-- Latest compiled and minified JavaScript -->
       
        <!-- Custom js -->
        <script src="{{ asset('custom') }}/js/orders.js"></script>
         <!-- Custom js -->
        <script src="{{ asset('custom') }}/js/mresto.js"></script>
        <!-- AJAX -->

        <!-- SELECT2 -->
        <script src="{{ asset('custom') }}/js/select2.js"></script>
        <script src="{{ asset('vendor') }}/select2/select2.min.js"></script>

        <!-- DATE RANGE PICKER -->
        <script type="text/javascript" src="{{ asset('vendor') }}/moment/moment.min.js"></script>
        <script type="text/javascript" src="{{ asset('vendor') }}/daterangepicker/daterangepicker.min.js"></script>

        <!-- All in one -->
        <script src="{{ asset('custom') }}/js/js.js?id={{ config('config.version')}}"></script>

        <!-- Argon JS -->
        <script src="{{ asset('argon') }}/js/argon.js?v=1.0.0"></script>

         <!-- Import Vue -->
        <script src="{{ asset('vendor') }}/vue/vue.js"></script>

        <!-- Import AXIOS --->
        <script src="{{ asset('vendor') }}/axios/axios.min.js"></script>

        <!-- Flatpickr datepicker -->
        <script src="{{ asset('vendor') }}/flatpickr/flatpickr.js"></script>

        <!-- Notify JS -->
        <script src="{{ asset('custom') }}/js/notify.min.js"></script>

         <!-- Cart custom sidemenu -->
        <script src="{{ asset('custom') }}/js/cartSideMenu.js"></script>


        <script>
            var ONESIGNAL_APP_ID = "{{ config('settings.onesignal_app_id') }}";
            var USER_ID = '{{  auth()->user()&&auth()->user()?auth()->user()->id:"" }}';
            var PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
            var PUSHER_APP_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
        </script>
        @if (auth()->user()!=null&&auth()->user()->hasRole('staff'))
            <script>
                //When staff, use the owner
                USER_ID = '{{  auth()->user()->restaurant->user_id }}';
            </script>
        @endif
       

        <!-- OneSignal -->
        @if(strlen( config('settings.onesignal_app_id'))>4)
            <script src="{{ asset('vendor') }}/OneSignalSDK/OneSignalSDK.js" async=""></script>
            <script src="{{ asset('custom') }}/js/onesignal.js"></script>
        @endif

        @stack('js')
        @yield('js')

        <script src="{{ asset('custom') }}/js/rmap.js"></script>

         <!-- Pusher -->
         @if(strlen( config('broadcasting.connections.pusher.app_id'))>2)
            <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
            <script src="{{ asset('custom') }}/js/pusher.js"></script>
        @endif

        <!-- Custom JS defined by admin -->
        <?php echo file_get_contents(base_path('public/byadmin/back.js')) ?>
        <script>
           var notificacionIndes=1;
           var arraynotificacion=[];
       function listnotificacion(index){
            $.ajax({
                      headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                              },
                      type: "get",
                      encoding:"UTF-8",
                      url: "{{route('notificacion.list')}}/"+index,
                      processData: false,
                      contentType: false,
                      dataType:'json',
                      beforeSend:function(){
                      }
                  }).done(function( respuesta ) {
                    var itemIcon='<i class=" ni ni-single-02" style="font-size: 28px;"></i>';
                    var conItem='<a href="/orders/%orderid%" class="row" style="margin-top:10px"><div class="col-2" style="display:flex;align-items:center;">%icon%</div> <span class="col-10">%title%  <br><span style="font-size:11px">%body%</span> <br><span style="font-size:11px">%fecha%</span></span></a>';
                    var listItem="";
                    if(respuesta['total']>0 && index>1){
                      arraynotificacion=arraynotificacion.slice(0,((index-1)*10));
                      arraynotificacion=arraynotificacion.concat(respuesta['data']);
                    }else{
                      arraynotificacion=respuesta['data'];
                    }
                    for (var i = 0; i < arraynotificacion.length ;i++) {
                      if(arraynotificacion[i]['data']['title']=="Pedido rechazado"){
                        itemIcon='<i class="col-2 fa fa-ban" style="color:#f80031;font-size: 28px;"></i>';
                      }
                      if(arraynotificacion[i]['data']['title']=="Su pedido ha sido aceptado"){
                        itemIcon='<i class="col-2  fa fa-check-circle-o" style="color:#03acca;font-size: 28px;"></i>';
                      }
                      if(arraynotificacion[i]['data']['title']=="Tu pedido está listo."){
                        itemIcon='<i class="col-2 fa fa-shopping-bag" style="color:#ff3709ca;font-size: 28px;"></i>';
                      }
                      if(arraynotificacion[i]['data']['title']=="Tu pedido ha sido entregado"){
                        itemIcon='<i class="col-2 fa fa-handshake-o" style="color:#4fd69c;font-size: 28px;"></i>';
                      }
                      var fecha=new Date(arraynotificacion[i]['created_at']).toLocaleString('en-US', { hour12: true });
                      listItem+=conItem.replace('%orderid%',arraynotificacion[i]['data']['order_id']).replace('%icon%',itemIcon).replace('%title%', arraynotificacion[i]['data']['title']).replace('%body%', arraynotificacion[i]['data']['body']).replace('%fecha%', fecha);
                    }
                    if(respuesta['data'].length>0){
                      if((notificacionIndes*10)<respuesta['total']){
                        listItem+="<span class='btn' onclick='listNotificacionAumenta("+(index+1)+");event.stopPropagation();' style='width:100%'> Ver mas</span>";
                      }
                      $("#listNotif").html(listItem);
                    }else{
                      $("#listNotif").html("<span style='padding-left:10px'> No hay notificaciones</span>");
                    }
                    
                  }).fail(function( jqXHR,textStatus ) {
                      
                  });
      }
      function listNotificacionAumenta(index){
            notificacionIndes=index;
            listnotificacion(index);
        }
  $(document).ready(function() {
    setTimeout(function () {listnotificacion(notificacionIndes)}, 1000);
 
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
                 Swal.fire({
                              title: 'Cliente registrado',
                              icon: 'success',
                              button: true,
                              timer: 2000
                          });
                setTimeout(() => {
                    location.reload();
                }, 2000);
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
