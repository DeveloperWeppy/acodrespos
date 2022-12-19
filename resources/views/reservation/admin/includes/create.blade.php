@extends('layouts.app', ['title' => "Reservaciones"])

@section('content')
    @include('drivers.partials.header', ['title' => "Crear reservación"])

<div class="totalreserva">
    <div class="container-fluid mt--7">
        <div class="row mb-5">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Crear reservación</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('reservation.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                   
                    <form method="post" action="" id="formReserva" autocomplete="off">
                        @csrf
                        <div class="card-body">
                                
                                <h6 class="heading-small text-muted mb-4">{{ __('Client information') }}</h6>
                                <div class="pl-lg-4">

                                    <button type="button" data-toggle="modal" data-target="#modalRegister" class="btnFormClient btn btn-outline-primary btn-icon btn-sm page-link btn-cart-radius m-2" style="float: right;"><span class="btn-inner--icon btn-cart-icon"><i aria-hidden="true" class="fa fa-plus"></i></span></button>

                                    <div class="form-group{{ $errors->has('name_client') ? ' has-danger' : '' }}">

                                        <label class="form-control-label" for="name_client">{{ __('Client') }}</label>
                                        <div class="">
                                            <select name="cli" class="form-control form-control-sm" required>
                                                <option value="" >Seleccionar cliente</option>
                                                @foreach($clients as $key)
                                                <option value="{{$key->id}}" >{{$key->number_identification}} - {{$key->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('name_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="name_client">Mesas a reservar ( Valor por mesa: <span id="valorMesa"></span> COP)</label>
                                        <select name="zonas[]" class="form-control" id="zonas" multiple required>
                                            <option value="">Seleccionar mesas</option>
                                            <?php
                                                for ($i=0; $i < count($areasMesas) ; $i++) { 
                                                    $areas = $areasMesas[$i][0];
                                                    $opciones="";
                                                    for ($j=0; $j < count($areasMesas[$i][1]) ; $j++) { 
                                                        $mesa = $areasMesas[$i][1][$j];
                                                        $opciones.='<option value="'.$mesa->id.'">'.$mesa->name.'</option>';
                                                    }
                                                    echo '<optgroup label="'.$areas->name.'" >'.$opciones.'</optgroup>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="email_client">Motivo de reservación</label>
                                        <div class="">
                                            <select name="mot" id="mot" class="form-control form-control-sm" required>
                                                <option value="" data-price="0" >Seleccionar motivo</option>
                                                @foreach($motive as $key)
                                                <option value="{{$key->id}}" data-price="{{$key->price}}">{{$key->name}} ({{number_format($key->price, 2, ",", ".")}} COP) </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('phone_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="phone_client">Comentario</label>
                                        <textarea class="form-control" name="com" ></textarea>
                                    </div>

                                    <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}">
                                
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label">Jornada</label>
                                                    <div class="">
                                                        <select name="jor" id="jor" class="form-control form-control-sm" required>
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label">Fecha</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                        </div>
                                                        <input name="fec" class="form-control datepickerReserva" value="{{$now}}" required placeholder="Fecha" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label">Hora</label>
                                                    <input name="hora" class="form-control timepicker" placeholder="Hora" required type="text">
                                                </div>
                                            </div>
                                        </div>
                                        
                                
                                    </div>

                                    <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}" >

                                        <label class="form-control-label" for="email_client">Detalles de reserva</label>
                                        <div class="">
                                            <p class="h4"><strong>Total por mesas:</strong> <span id="valorMesas"></span> COP</p>
                                            <p class="h4"><strong>Valor motivo:</strong> <span id="valorMotivo"></span> COP</p>
                                        </div>

                                        <label class="form-control-label" for="email_client">Total a pagar</label>
                                        <div class="">
                                            <p class="h1">@{{priceReservationFormated}} COP</p>
                                        </div>
                                    </div>


                                </div>
                        </div>
                        <div class="card-footer py-4">
                            <nav class="d-flex justify-content-end" aria-label="...">
                            
                                <button type="submit" class="btn btn-md btn-primary float-left" >Guardar Reserva</button>
                            </nav>
                        </div>
                    </form>
              
                </div>
            </div>
        </div>
        @include('layouts.footers.auth')
    </div>
     

        @include('reservation.admin.includes.modals')
</div> 
        @section('js')
        <script>

            var precioEstandar = {{(isset($restaurantConfig[0]->standard_price)?$restaurantConfig[0]->standard_price:0)}};
            var precioEstandarF = 0;

            var porcentagePayment = {{(isset($restaurantConfig[0]->percentage_payment)?$restaurantConfig[0]->percentage_payment:0)}};

            var jornadas = [];
            var jornada = [];
         
            $('#valorMesa').html(puntosMil(precioEstandar));
            $('#valorMesas').html("0");
            $('#valorMotivo').html("0");

            function metodoPago(){
                if ($("#paymentType").val()=='transferencia') {
                    $('#selecuenta').show()
                    $('#loadarchivo').show()
                    $('#seletipocuenta').hide()
                    $('.selecuenta2').hide()
                }else if ($("#paymentType").val()=='cardterminal') {
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
            }

            $(document).on('change', '#check_por', function(){
                if (this.checked) {
                    var totalPercentage = (porcentagePayment/100)*totalReservas.totalPriceReservation;
                    totalReservas.priceReservation = totalPercentage;
                    totalReservas.priceReservationFormated = puntosMil(totalPercentage);

                    totalReservas.received = totalPercentage;
                    totalReservas.receivedFormated = puntosMil(totalPercentage);

                    var restanteRes = totalReservas.totalPriceReservation-totalPercentage;
                    $('#resRes').html(puntosMil(restanteRes));
                    $('#divPercentage').css('display','block');
                    
                }else{
                    totalReservas.priceReservation = totalReservas.totalPriceReservation;
                    totalReservas.priceReservationFormated = puntosMil(totalReservas.totalPriceReservation);

                    totalReservas.received = totalReservas.totalPriceReservation;
                    totalReservas.receivedFormated = puntosMil(totalReservas.totalPriceReservation);

                    $('#resRes').html(0);
                    $('#divPercentage').css('display','none');
                }
            });

            
     
            var totalReservas = new Vue({
                el: '.totalreserva',
                data: {
                    totalPriceReservation:0,//almacena el valor total de la reservación
                    priceReservation:0,// precio de la reservacion a mostrar y cancelar, contiene todas las codiciones de menos porcentaje y demas
                    priceReservationFormated:'0',
                    received:0,
                    receivedFormated:'0',
                    totalPriceRestado:0,
                    totalPriceRestadoFormated:'0',
                    totalCambioFormated:'0',
                    
                },
                methods: {
                    change: function (event) {
                        metodoPago();
                        if(event.target.value=="onlinepayments"||event.target.value=="cardterminal"||event.target.value=="transferencia"){
                            this.received=this.priceReservation;
                            this.receivedFormated = puntosMil(this.priceReservation);
                            this.totalPriceRestadoFormated = puntosMil(0);
                        }
                    },
                    show: function (event) {
                        this.receivedFormated = puntosMil(this.receivedFormated);

                        this.received = this.receivedFormated.replaceAll(".", "").replaceAll(",", ".");

                        this.totalPriceRestadoFormated=0;
                        if(this.priceReservation-this.received>0){
                            this.totalPriceRestadoFormated=puntosMil(this.priceReservation-this.received);
                        }
                        
                        this.totalCambioFormated = '0';
                        if(this.received-this.priceReservation>0){
                            this.totalCambioFormated = puntosMil(this.received-this.priceReservation);
                        }

                    },
                },
            });
            

            $("#mot").on('change', function() {
                var mot = $("#mot option:selected").data('price');
                var mes = $("#zonas").val();
                var mesas = mes.length;

                $('#valorMotivo').html(puntosMil(mot));

                totalReservas.totalPriceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservationFormated = puntosMil((precioEstandar*mesas)+mot);

                totalReservas.totalPriceRestado=(precioEstandar*mesas)+mot;
                totalReservas.totalPriceRestadoFormated =  puntosMil((precioEstandar*mesas)+mot);
            });

            $("#zonas").on('change', function() {
                var mot = $("#mot option:selected").data('price');
                var mes = $("#zonas").val();
                var mesas = mes.length;

                $('#valorMesas').html(puntosMil(precioEstandar*mesas));

                totalReservas.totalPriceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservationFormated = puntosMil((precioEstandar*mesas)+mot);

                totalReservas.totalPriceRestado=(precioEstandar*mesas)+mot;
                totalReservas.totalPriceRestadoFormated =  puntosMil((precioEstandar*mesas)+mot);
            });

            $(document).on('change', '#jor', function(){
                let date = new Date($("input[name=fec]").val());
                var dia = date.getDay();
                jornada = jornadas[$(this).val()];
                selectHora(dia);
            });

            $("input[name=fec]").on('change', function() {
                let date = new Date($(this).val());
                var dia = date.getDay();
                selectHora(dia);
            });

            function getJornadas(){
          
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('reservation.getHours')}}",
                    type: 'GET',
                    success: function (data) {
                        let selectJornada = $("#jor");
                        if(data.length>0){
                            jornadas = data;
                            jornada = data[0];
                            $k = 0;
                            data.forEach(element => {
                                selectJornada.append('<option value="'+$k+'" >Jornada '+($k+1)+'</option>');
                                $k++;
                            });
                            selectJornada.trigger('change');
                        }
                        console.log(data);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
            getJornadas();

            function selectHora(dia){
          
                var min = '6';
                var max = '20';

                switch (dia) {
                    case 0:
                        min = Object.values(jornada)[3];
                        max = Object.values(jornada)[4];
                        break;
                    case 1:
                        min = Object.values(jornada)[5];
                        max = Object.values(jornada)[6];
                        break;
                    case 2:
                        min = Object.values(jornada)[7];
                        max = Object.values(jornada)[8];
                        break;
                    case 3:
                        min = Object.values(jornada)[9];
                        max = Object.values(jornada)[10];
                        break;
                    case 4:
                        min = Object.values(jornada)[11];
                        max = Object.values(jornada)[12];
                        break;
                    case 5:
                        min = Object.values(jornada)[13];
                        max = Object.values(jornada)[14];
                        break;
                    case 6:
                        min = Object.values(jornada)[15];
                        max = Object.values(jornada)[16];
                        break;
                }
                if(min==null && max==null ){
                    min = '6';
                    max = '20';
                }
                $('.timepicker').timepicker({
                    timeFormat: 'h:mm p',
                    interval: 30,
                    minTime: min,
                    maxTime: max,
                    dynamic: false,
                    dropdown: true,
                    scrollbar: true,
                });

                let timPicker = $('.timepicker');
                timPicker.timepicker('option', 'minTime', min);
                timPicker.timepicker('option', 'maxTime', max);
            }
            


          
            

            function puntosMil(value){
                return value.toString().replace(/\D/g, "")
                .replace(/([0-9])([0-9]{0})$/, '$1')
                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
            }

            function pagarReserva(){

                $('#pagarReserva').prop('disabled', true);
                var formData = new FormData($('#formReserva')[0]);
                
                formData.append('met',$('#paymentType').val());
                formData.append('cuentaid',$('#paymentId').val());
                formData.append('tipotarjeta',$('#paymentType2').val());
                formData.append('franquicia',$('#franquicia').val());
                formData.append('voucher',$('#voucher').val());
                formData.append('total',totalReservas.totalPriceReservation);
                formData.append('pagado',totalReservas.priceReservation);

              
                if($('#check_por').is(':checked')){
                    var totalPercentage = (porcentagePayment/100)*totalReservas.totalPriceReservation;
                    var restanteRes = totalReservas.totalPriceReservation-totalPercentage;
                    formData.append('pendiente',restanteRes);
                    formData.append('porc',1);
                }else{
                    formData.append('pendiente',0);
                    formData.append('porc',0);
                }
                
                
                
                formData.append('img_payment',$('#img_payment')[0].files[0]);
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('reservation.store')}}",
                    type: 'POST',
                    success: function (data) {

                        $('#modal-payment-reservation').modal('hide');

                        Swal.fire({
                            title: "Datos Guardados",
                            text: '',
                            icon: 'success',
                        }).then(function() {
                            window.location.href = "{{route('reservation.index');}}";
                        });
                    },
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }

           
        
            $(function(){
                $(".datepickerReserva").daterangepicker({
                    locale: {
                        format: 'YYYY-MM-DD',
                        daysOfWeek: [ "Dom", "Lun","Mar","Mie","Jue","Vie","Sáb" ],
                        monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
                        firstDay: 1,
                        applyLabel: "Aplicar",
                        cancelLabel: "Cancelar",
                    },
                    minDate: '{{$now}}',
                    singleDatePicker: true,
                });
            });

           
            
           


            function revisarOcupacion(){
                var formData = new FormData();
                formData.append('fecha',$('input[name=fec]').val());
                formData.append('hora',$('input[name=hora]').val());
                formData.append('mesas[]',$('#zonas').val());
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('reservation.getOcupation')}}",
                    type: 'POST',
                    success: function (data) {
                        if(data.datos>0){
                            Swal.fire({
                                title: "La mesa ya esta reservada a esa hora",
                                text: '',
                                icon: 'error',
                            })
                            return false;
                        }else{
                            $('#modal-payment-reservation').modal('show');
                        }
                    },
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }

          
            $("#formReserva").submit(function(e) {
                e.preventDefault();

                revisarOcupacion();
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
                       'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
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
                    let datalistClient=resp.selectClient;
                    let datalistPhone=resp.selectTelefono;
                    $('select[name=cli]').select2({
                        width: '100%',
                        placeholder: "Seleccionar Cliente",
                        data: datalistClient
                    });

                    $('.select2').addClass('form-control');
                    $('.select2').css('width','100%');
                    $('.select2-selection').css('border','0');
                    $('.select2-selection__arrow').css('top','10px');
                    $('.select2-selection__rendered').css('color','#8898aa');

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
        @endsection


       


        
    </div>
@endsection


