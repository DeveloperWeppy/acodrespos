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
                                <h3 class="mb-0">Solicitar reservación</h3>
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


                                    <div class="form-group{{ $errors->has('name_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="name_client">Restaurante</label>
                                        <div class="">
                                            <select name="res" class="form-control form-control-sm" id="res" required>
                                                <option value="" >Seleccionar restaurante</option>
                                             
                                                    @foreach($restaurant as $key)
                                                    <option value="{{$key->id}}" >{{$key->name}}</option>
                                                    @endforeach
                                               
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('name_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="name_client">Mesas a reservar ( Valor por mesa: <span id="valorMesa"></span> COP)</label>
                                        <select name="zonas[]" class="form-control" id="zonas" multiple required>
                                           
                                        </select>
                                    </div>
                                    <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="email_client">Motivo de reservación</label>
                                        <div class="">
                                            <select name="mot" id="mot" class="form-control form-control-sm" required>
                                                <option value="" data-price="0" >Seleccionar motivo</option>
                                                {{--
                                                   @foreach($motive as $key)
                                                <option value="{{$key->id}}" data-price="{{$key->price}}">{{$key->name}} ({{number_format($key->price, 2, ",", ".")}} COP) </option>
                                                @endforeach
                                                   --}}
                                              
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('phone_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="phone_client">Comentario</label>
                                        <textarea class="form-control" name="com" ></textarea>
                                    </div>

                                    <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}">
                                
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Fecha</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                        </div>
                                                        <input name="fec" class="form-control datepickerReserva" required placeholder="Fecha" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
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
                            
                                <button type="submit" class="btn btn-md btn-primary float-left" >Solicitar Reserva</button>
                            </nav>
                        </div>
                    </form>
              
                </div>
            </div>
        </div>
    </div>
     

</div> 
        @section('js')
        <script>

            var precioEstandar = {{(isset($restaurantConfig[0]->standard_price)?$restaurantConfig[0]->standard_price:0)}};
            var precioEstandarF = 0;

            var porcentagePayment = {{(isset($restaurantConfig[0]->percentage_payment)?$restaurantConfig[0]->percentage_payment:0)}};

         
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
            

            $("#res").on('change', function() {

                var formData = new FormData();
                formData.append('restaurant_id',$('#res').val());
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('reservation.configRestaurant')}}",
                    type: 'post',
                    success: function (data) {
                        alert(1);
                        if(data.datos[3]!=null){
                            let selectMesas = $('#zonas');
                            console.log(data.datos[0].length);
                            if(data.datos[0]!=null){
                                var k = 0;
                               for (var i = 0; i < data.datos[0].length; i++) {
                                    var areas = data.datos[0][i][0];
                                    var mesas = data.datos[0][i][1];
                                    selectMesas.append('<optgroup label="'+areas.name+'" >');
                                    for (var i = 0; i < mesas.length; i++) {
                                        selectMesas.append('<option value="'+mesas[i].id+'">'+mesas[i].name+'</option>');
                                    }
                                    selectMesas.append('</optgroup>');
                                    k++;
                               }
                            }
                            
                        }
                    },
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                });


            });

            $("#zonas").on('change', function() {
                var mot = $("#mot option:selected").data('price');
                var mes = $("#zonas").val();
                var mesas = mes.length;

                $('#valorMesas').html(puntosMil(precioEstandar*mesas));

                totalReservas.totalPriceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservationFormated = puntosMil((precioEstandar*mesas)+mot);


            });

            function puntosMil(value){
                return value.toString().replace(/\D/g, "")
                .replace(/([0-9])([0-9]{0})$/, '$1')
                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
            }

            function pagarReserva(){
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
                $(".datepickerReserva").datepicker({
                    format: 'yyyy-mm-dd',
                });
            });
            

            $('.timepicker').timepicker({
                timeFormat: 'h:mm p',
                interval: 30,
                minTime: '7',
                maxTime: '20',
                dynamic: false,
                dropdown: true,
                scrollbar: true,
                disabledTime:['7','8','9'],
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


    

    
        </script>
        @endsection


        @include('layouts.footers.auth')


        
    </div>
@endsection


