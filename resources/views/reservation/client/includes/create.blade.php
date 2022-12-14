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

                                    <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">N. de mesas</label>
                                                    <div class="input-group">
                                                        <input id="mes" name="mes" class="form-control" required placeholder="N. de mesas" value="1" min="1" type="number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">N. de personas</label>
                                                    <input id="per" name="per" class="form-control" placeholder="N. de personas" required value="1" min="1" type="number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="email_client">Motivo de reservación</label>
                                        <div class="">
                                            <select name="mot" id="mot" class="form-control form-control-sm" required>
                                                <option value="" data-price="0" >Seleccionar motivo</option>
                                              
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

            var precioEstandar = 0;
            var precioEstandarF = 0;

            var porcentagePayment = 0;

            var minH = 0;
            var intH = 0;

            var jornadas = [];
            var jornada = [];

         
            $('#valorMesa').html(puntosMil(precioEstandar));
            $('#valorMesas').html("0");
            $('#valorMotivo').html("0");

        

            
     
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

                Swal.fire({
                    title: 'Cargando Restaurante',
                    timer: 2000,
                    timerProgressBar: true,
                    showCloseButton: false,
                    showConfirmButton: false,
                    showCancelButton: false,
                    });
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('reservation.configRestaurant')}}",
                    type: 'post',
                    success: function (data) {
                        if(data.datos[3]!=null){
                            let selectMesas = $('#zonas');
                           
                            if(data.datos[1]!=null){
                                let selectMotivos= $('#mot');
                                for (var i = 0; i < data.datos[1].length; i++) {
                                    var motivo = data.datos[1][i];
                                    selectMotivos.append('<option value="'+motivo.id+'" data-price="'+motivo.price+'" >'+motivo.name+'</option>');
                               }
                            }

                            if(data.datos[2]!=null){
                                let selectJornada = $("#jor");
                                jornadas = data.datos[2];
                                jornada = data.datos[2][0];
                                $k = 0;
                                jornadas.forEach(element => {
                                    selectJornada.append('<option value="'+$k+'" >Jornada '+($k+1)+'</option>');
                                    $k++;
                                });
                                selectJornada.trigger('change');
                            }

                            if(data.datos[3]!=null){
                                var config = data.datos[3];
                                precioEstandar = config.standard_price;
                                precioEstandarF = 0;
                                porcentagePayment = config.percentage_payment;

                                var mesas = $('#mes').val();

                                $('#valorMesas').html(puntosMil(precioEstandar*mesas));

                                var nowDate = new Date();
                               
                                if(config.minimum_period=="dia"){
                                    nowDate.setDate(nowDate.getDate() + config.condition_period);
                                    intH = 0;
                                }
                                if(config.minimum_period=="hora"){
                                    let hour = nowDate.getHours().toString();
                                    
                                    minH = parseInt(hour)+parseInt(config.condition_period);
                                    intH = config.interval_time;

                                    let date = new Date($('input[name=fec]').val());
                                    var dia = date.getDay();
                                    selectHora(dia);
                                }
                                
                    

                                $(".datepickerReserva").daterangepicker({
                                    locale: {
                                        format: 'YYYY-MM-DD',
                                        daysOfWeek: [ "Dom", "Lun","Mar","Mie","Jue","Vie","Sáb" ],
                                        monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
                                        firstDay: 1,
                                        applyLabel: "Aplicar",
                                        cancelLabel: "Cancelar",
                                    },
                                    minDate: nowDate,
                                    singleDatePicker: true,
                                });

                            }
                        }
                    },
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                });

            });

            $("#mot").on('change', function() {
                var mot = $("#mot option:selected").data('price');
                var mes = $("#zonas").val();
                var mesas = $('#mes').val();

                $('#valorMotivo').html(puntosMil(mot));

                totalReservas.totalPriceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservationFormated = puntosMil((precioEstandar*mesas)+mot);
            });

            $("#mes").on('change', function() {
                var mot = $("#mot option:selected").data('price');
                var mes = $("#zonas").val();
                var mesas = $('#mes').val();
                
                $('#valorMesas').html(puntosMil(precioEstandar*mesas));

                totalReservas.totalPriceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservationFormated = puntosMil((precioEstandar*mesas)+mot);

            });
            $("#mes").on('keyup', function() {
                var mot = $("#mot option:selected").data('price');
                var mes = $("#zonas").val();
                var mesas = $('#mes').val();
                
                $('#valorMesas').html(puntosMil(precioEstandar*mesas));

                totalReservas.totalPriceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservation = (precioEstandar*mesas)+mot;
                totalReservas.priceReservationFormated = puntosMil((precioEstandar*mesas)+mot);

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

            function selectHora(dia){
          
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

                if(minH==null && max==null ){
                    minH = '6';
                    max = '20';
                }
                if(minH==0){
                    minH = min;
                }

                let timPicker = $('.timepicker');
                timPicker.timepicker('option', 'minTime', minH.toString());
                timPicker.timepicker('option', 'maxTime', max);
                
            }

            $('.timepicker').timepicker({
                timeFormat: 'h:mm p',
                interval: '60',
                minTime: '8',
                maxTime: '20',
                dynamic: false,
                dropdown: true,
                scrollbar: true,
            });

           


            function puntosMil(value){
                return value.toString().replace(/\D/g, "")
                .replace(/([0-9])([0-9]{0})$/, '$1')
                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
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
                    singleDatePicker: true,
                });
            });
            

            $('.timepicker').timepicker({
                timeFormat: 'h:mm p',
                interval: 10,
                minTime: '7',
                maxTime: '23',
                dynamic: false,
                dropdown: true,
                scrollbar: true,
            });


            function revisarOcupacion(){
                var formData = new FormData();
                formData.append('restaurant_id',$('#res').val());
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
                            enviarSolitud();
                        }
                    },
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }

            function enviarSolitud(){
                var formData = new FormData($('#formReserva')[0]);
                formData.append('total',totalReservas.priceReservation);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('reservation.store')}}",
                    type: 'POST',
                    success: function (data) {
                        if(data.datos>0){
                            Swal.fire({
                                title: "Solicitud enviada",
                                text: '',
                                icon: 'success',
                            }).then((result) => {
                                window.location.href = "{{route('reservation.index');}}";
                            });
                            return false;
                        }else{
                            Swal.fire({
                                title: "Ocurrio un error al enviar la solicitud",
                                text: '',
                                icon: 'error',
                            })
                            return false;
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


