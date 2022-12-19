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


                                    <div class="form-group{{ $errors->has('name_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="name_client">{{ __('Client') }}</label>
                                        <div class="">
                                            <select name="cli" class="form-control form-control-sm" readonly="">
                                                <option value="" >Seleccionar cliente</option>
                                                @foreach($clients as $key)
                                                <option value="{{$key->id}}" >{{$key->number_identification}} - {{$key->name}}</option>
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
                                                        <input id="mes" name="mes" class="form-control" required placeholder="N. de mesas" value="1" min="0" type="number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">N. de personas</label>
                                                    <input id="per" name="per" class="form-control" placeholder="N. de personas" required value="1" min="0" type="number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('name_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="name_client">Mesas a reservar ( Valor por mesa: <span id="valorMesa"></span> COP)</label>
                                        <select name="zonas[]" class="form-control" id="zonas" multiple required>
                                            <option value="">Seleccionar mesas</option>
                                            <?php
                                            $k=0;
                                                foreach ($restoareas as $item){
                                                    $opciones="";
                                                    foreach ($restomesas as $item2){
                                                        if($item->id==$item2->restoarea_id){
                                                            $opciones.='<option value="'.$item2->id.'">'.$item2->name.'</option>';
                                                        }
                                                    }
                                                    echo '<optgroup label="'.$item->name.'" >'.$opciones.'</optgroup>';
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

                                    <div class="form-group" >

                                        <label class="form-control-label" for="email_client">Detalles de reserva</label>
                                        <div class="">
                                            <p class="h4"><strong>Total por mesas:</strong> <span id="valorMesas"></span> COP</p>
                                            <p class="h4"><strong>Valor motivo:</strong> <span id="valorMotivo"></span> COP</p>
                                            <p class="h4"><strong>Total:</strong> @{{totalDetalleFormated}} COP</p>
                                        </div>

                                        <label class="form-control-label mt-3" for="email_client">Total a pagar por modificación</label>
                                        <div class="">
                                            <p class="h1">@{{priceReservationFormated}} COP</p>
                                        </div>
                                    </div>


                                    <br>
                                    
                                    <div class="form-group" >
                                        <?php
                                        if($reservation->payment_1!=""){
                                            $payment1 = json_decode($reservation->payment_1);
                                        ?>
                                        <label class="form-control-label" for="email_client">Detalles de pago</label>
                                        <div class="">
                                            <p class="h4"><strong>Método de pago:</strong> {{$payment1->metodo}}</p>

                                            @if($payment1->metodo=="cardterminal")
                                                <p class="h4"><strong>Tipo de tarjeta:</strong> {{($payment1->tarjeta==""?"Ninguna":$payment1->tarjeta)}}</p>
                                                <p class="h4"><strong>Franquicia:</strong> {{($payment1->franquicia==""?"Ninguna":$payment1->franquicia)}}</p>
                                                <p class="h4"><strong>Comprobante voucher:</strong> {{($payment1->voucher==""?"Ninguna":$payment1->voucher)}}</p>
                                            @endif

                                            @if($payment1->metodo=="transferencia")
                                                <?php 
                                                foreach ($configaccountsbanks as $key) {
                                                    if($payment1->cuenta_id==$key->id){
                                                        echo '<p class="h4"><strong>Cuenta:</strong> '.$key->name_bank.'</p>';
                                                    }
                                                }
                                                ?>
                                            @endif
                                            
                                            @if($reservation->url_payment1!="")
                                            <p class="h4"><strong>Evidencia de pago:</strong>
                                                <button type="button" onclick="Swal.fire({ imageUrl: '{{asset($reservation->url_payment1)}}', imageWidth: '100%',imageHeight: 'auto',imageAlt: 'Custom image'})" class="btn btn-outline-success btn-sm">
                                                    <span class="btn-inner--icon">
                                                        <i class="ni ni-image"></i>    
                                                    </span>
                                                </button>
                                            </p>
                                            @endif
                                            <p class="h4"><strong>Total pago:</strong>  @money( $payment1->total, config('settings.cashier_currency'),config('settings.do_convertion')) COP</p>
                                        </div>
                                        <?php } ?>

                                        <br>
                                        <?php
                                        if($reservation->payment_2!=""){
                                            $payment2 = json_decode($reservation->payment_2);
                                        ?>
                                        <label class="form-control-label" for="email_client">Detalles de pago porcentaje</label>
                                        <div class="">
                                            <p class="h4"><strong>Método de pago:</strong> {{$payment2->metodo}}</p>

                                            @if($payment2->metodo=="cardterminal")
                                                <p class="h4"><strong>Tipo de tarjeta:</strong> {{($payment2->tarjeta==""?"Ninguna":$payment2->tarjeta)}}</p>
                                                <p class="h4"><strong>Franquicia:</strong> {{($payment2->franquicia==""?"Ninguna":$payment2->franquicia)}}</p>
                                                <p class="h4"><strong>Comprobante voucher:</strong> {{($payment2->voucher==""?"Ninguna":$payment2->voucher)}}</p>
                                            @endif

                                            @if($payment2->metodo=="transferencia")
                                                <?php 
                                                foreach ($configaccountsbanks as $key) {
                                                    if($payment2->cuenta_id==$key->id){
                                                        echo '<p class="h4"><strong>Cuenta:</strong> '.$key->name_bank.'</p>';
                                                    }
                                                }
                                                ?>
                                            @endif
                                            
                                            @if($reservation->url_payment2!="")
                                            <p class="h4"><strong>Evidencia de pago:</strong>
                                                <button type="button" onclick="Swal.fire({ imageUrl: '{{asset($reservation->url_payment2)}}', imageWidth: '100%',imageHeight: 'auto',imageAlt: 'Custom image'})" class="btn btn-outline-success btn-sm">
                                                    <span class="btn-inner--icon">
                                                        <i class="ni ni-image"></i>    
                                                    </span>
                                                </button>
                                            </p>
                                            @endif
                                            <p class="h4"><strong>Total pago:</strong>  @money( $payment2->total, config('settings.cashier_currency'),config('settings.do_convertion')) COP</p>
                                        </div>
                                        <?php } ?>


                                        <br>
                                        <?php
                                        if($reservation->payment_3!=""){
                                            $payment3 = json_decode($reservation->payment_3);
                                        ?>
                                        <label class="form-control-label" for="email_client">Detalles de pago por modificación</label>
                                        <div class="">
                                            <p class="h4"><strong>Método de pago:</strong> {{$payment3->metodo}}</p>

                                            @if($payment3->metodo=="cardterminal")
                                                <p class="h4"><strong>Tipo de tarjeta:</strong> {{($payment3->tarjeta==""?"Ninguna":$payment3->tarjeta)}}</p>
                                                <p class="h4"><strong>Franquicia:</strong> {{($payment3->franquicia==""?"Ninguna":$payment3->franquicia)}}</p>
                                                <p class="h4"><strong>Comprobante voucher:</strong> {{($payment3->voucher==""?"Ninguna":$payment3->voucher)}}</p>
                                            @endif

                                            @if($payment3->metodo=="transferencia")
                                                <?php 
                                                foreach ($configaccountsbanks as $key) {
                                                    if($payment3->cuenta_id==$key->id){
                                                        echo '<p class="h4"><strong>Cuenta:</strong> '.$key->name_bank.'</p>';
                                                    }
                                                }
                                                ?>
                                            @endif
                                            
                                            @if($reservation->url_payment3!="")
                                            <p class="h4"><strong>Evidencia de pago:</strong>
                                                <button type="button" onclick="Swal.fire({ imageUrl: '{{asset($reservation->url_payment3)}}', imageWidth: '100%',imageHeight: 'auto',imageAlt: 'Custom image'})" class="btn btn-outline-success btn-sm">
                                                    <span class="btn-inner--icon">
                                                        <i class="ni ni-image"></i>    
                                                    </span>
                                                </button>
                                            </p>
                                            @endif
                                            <p class="h4"><strong>Total pago:</strong>  @money( $payment3->total, config('settings.cashier_currency'),config('settings.do_convertion')) COP</p>
                                        </div>
                                        <?php } ?>
                                    </div>


                                </div>
                        </div>
                        <div class="card-footer py-4">
                            <nav class="d-flex justify-content-end" aria-label="...">
                                <button type="submit" class="btn btn-md btn-primary float-left" >Guardar Modifcación</button>
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
            var updatePrice = {{(isset($restaurantConfig[0]->update_price)?$restaurantConfig[0]->update_price:0)}};
            var reserva_id = {{(isset($reservation->id)?$reservation->id:0)}};

         
            $('#valorMesa').html(puntosMil(precioEstandar));
            $('#valorMesas').html("0");
            $('#valorMotivo').html("0");


            $('.ckpropina').css('display','none');

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

        
     
            var totalReservas = new Vue({
                el: '.totalreserva',
                data: {
                    totalPriceReservation:0,//almacena el valor total de la reservación
                    priceReservation:updatePrice,// precio de la reservacion a mostrar y cancelar, contiene todas las codiciones de menos porcentaje y demas
                    priceReservationFormated:puntosMil(updatePrice),
                    received:0,
                    receivedFormated:'0',
                    totalPriceRestado:0,
                    totalPriceRestadoFormated:'0',
                    totalCambioFormated:'0',
                    totalUpdate:updatePrice,
                    totalUpdateFormated:puntosMil(updatePrice),
                    totalDetalleFormated:'0',
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

                totalReservas.totalDetalleFormated = puntosMil((precioEstandar*mesas)+mot);
            });

            $("#zonas").on('change', function() {
                var mot = $("#mot option:selected").data('price');
                var mes = $("#zonas").val();
                var mesas = mes.length;

                $('#valorMesas').html(puntosMil(precioEstandar*mesas));

                totalReservas.totalDetalleFormated = puntosMil((precioEstandar*mesas)+mot);


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
                timPicker.timepicker('option', 'maxTime', min);
                timPicker.timepicker('option', 'maxTime', max);
            }

            function puntosMil(value){
                return value.toString().replace(/\D/g, "")
                .replace(/([0-9])([0-9]{0})$/, '$1')
                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
            }

            function pagarReserva(){
                var formData = new FormData($('#formReserva')[0]);
                formData.append('reserva_id',reserva_id);
                formData.append('met',$('#paymentType').val());
                formData.append('cuentaid',$('#paymentId').val());
                formData.append('tipotarjeta',$('#paymentType2').val());
                formData.append('franquicia',$('#franquicia').val());
                formData.append('voucher',$('#voucher').val());
                formData.append('pagado',totalReservas.priceReservation);
                formData.append('img_payment',$('#img_payment')[0].files[0]);
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('reservation.storeUpdate')}}",
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

        window.onload = function() {

            $('select[name=cli]').val('{{(isset($reservation->client_id)?$reservation->client_id:"")}}');
            $('select[name=cli]').trigger('change');

            $('select[name=mot]').val('{{(isset($reservation->reservation_reason_id)?$reservation->reservation_reason_id:"")}}');
            $('select[name=mot]').trigger('change');

            $('textarea[name=com]').val('{{(isset($reservation->description)?$reservation->description:"")}}');

            var fecha = '{{(isset($reservation->date_reservation)?$reservation->date_reservation:"")}}';
            
            var fechaHora = fecha.split(' ');
            if(fechaHora[0]!=undefined){
                $('input[name=fec]').val(fechaHora[0]);
            }
            if(fechaHora[1]!=undefined){
                let timPicker1 = $('.timepicker').val(formatTime(fechaHora[1]));
                
            }

            $('#mes').val('{{(isset($reservation->mesas)?$reservation->mesas:"1")}}');
            $('#per').val('{{(isset($reservation->personas)?$reservation->personas:"1")}}');
            

            var mesasSeleccionadas = '{{(isset($reservation->mess)?$reservation->mess:"")}}';
            var mesas = mesasSeleccionadas.split(',');
            for(var i=0;i<mesas.length;i++){
                $('#zonas option[value='+mesas[i]+']').prop('selected', true);

                $('#zonas').trigger('change');
            }
        }
            

        function formatTime(timeString) {
            const [hourString, minute] = timeString.split(":");
            const hour = +hourString % 24;
            return (hour % 12 || 12) + ":" + minute + (hour < 12 ? " AM" : " PM");
        }
        </script>
        @endsection


       


        
    </div>
@endsection


