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
                                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
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
                                            <select name="cli" class="form-control form-control-sm">
                                                <option value="" >Seleccionar cliente</option>
                                                @foreach($clients as $key)
                                                <option value="{{$key->id}}" >{{$key->number_identification}} - {{$key->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('name_client') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="name_client">Mesas a reservar ( Valor por mesa: <span id="valorMesa"></span> COP)</label>
                                        <select name="zonas[]" class="form-control" id="zonas" multiple>
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
                                            <select name="mot" id="mot" class="form-control form-control-sm">
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
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Fecha</label>
                                                    <div class="">
                                                        <div class="input-group-prepend" hidden>
                                                            <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                        </div>
                                                        <input name="fec" class="form-control" placeholder="Fecha" type="date">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Hora</label>
                                                    <div class="">
                                                        <select name="hora" class="form-control" required>
                                                            <?php 
                                                                $k=0;
                                                                for($i=1;$i<25;$i++){
                                                                    $selected = "";
        
                                                                    $k++;  if($k==13){$k=1;}  
                                                                
        
                                                                    $form = "AM"; if($i>=12 && $i<24){$form="PM";}
                                                                    if(isset($_GET['hhde']) && $_GET['hhde']==$i){
                                                                        $selected = "selected";
                                                                    }
                                                                    if(!isset($_GET['hhde']) && $i==7){
                                                                        $selected = "selected";
                                                                    }
                                                                    echo '<option value="'.$i.'" '.$selected.' >'.$k.':00 '.$form.'</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
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
                            
                                <button type="button" class="btn btn-md btn-primary float-left" data-toggle="modal" data-target="#modal-payment-reservation" >Guardar Reserva</button>
                            </nav>
                        </div>
                    </form>
              
                </div>
            </div>
        </div>
    </div>
     

        @include('reservation.admin.includes.modals')
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

                    var restanteRes = totalReservas.totalPriceReservation-totalPercentage;
                    $('#resRes').html(puntosMil(restanteRes));
                    $('#divPercentage').css('display','block');
                    
                }else{
                    totalReservas.priceReservation = totalReservas.totalPriceReservation;
                    totalReservas.priceReservationFormated = puntosMil(totalReservas.totalPriceReservation);

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
                        if(this.priceReservation>this.received){
                            this.totalPriceRestadoFormated=puntosMil(this.priceReservation-this.received);
                        }
                        
                        this.totalCambioFormated = '0';
                        if(this.received>this.priceReservation){
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
                formData.append('porc',$('#check_por').val());
                formData.append('met',$('#paymentType').val());
                formData.append('met',$('#paymentType').val());
                formData.append('total',totalReservas.totalPriceReservation);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('reservation.store')}}",
                    type: 'POST',
                    success: function (data) {
                        alert(1);
                        Swal.fire({
                            title: "Datos Guardados",
                            text: '',
                            icon: 'success',
                        });
                    },
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }

                            
            
            
       
            
        </script>
        @endsection


        @include('layouts.footers.auth')


        
    </div>
@endsection


