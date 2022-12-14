@extends('layouts.app', ['title' => __('Restaurants')])
@section('admin_title')
    {{ __('Reservas') }}
@endsection
@section('content')

<div class="header bg-gradient-info pb-6 pt-5 pt-md-8">
    <div class="container-fluid">

        <div class="nav-wrapper">
           
        </div>

    </div>
</div>


<div class="container-fluid mt--7 mb-5">
    <div class="row">
        <div class="col-12">
            <br />

            @include('partials.flash')

            <div class="tab-content" id="tabs">


                <!-- Tab Managment -->
                <div class="tab-pane fade show active" id="menagment" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    Reservaciones
                                </div>
                                <div class="col text-right">
                                    <a type="button" class="btn btn-sm btn-primary" href={{route('reservation.create')}}>Solicitar reservación</a>
                                </div>
                            </div>
                            
                        </div>
                        <div class="">


                            <div class="table-responsive">
                                <table class="table align-items-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">N. Reserva</th>
                                            <th scope="col">Fecha y Hora</th>
                                            <th class="table-web" scope="col">Restaurante</th>
                                            <th class="table-web" scope="col">Area - Mesa</th>
                                            <th class="table-web" scope="col">Valor</th>
                                            <th scope="col">Pendiente</th>
                                            <th scope="col">Estado</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="listaReservas" style="background: #ffffff;">
                                        @include('reservation.client.includes.tablareservaciones')
                                    
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="card-footer py-4">
                            @if(count($reservaciones))
                            <nav class="d-flex justify-content-end" aria-label="...">
                                {{ $reservaciones->appends(Request::all())->links() }}
                            </nav>
                            @else
                                <h4>No tienes reservas...</h4>
                            @endif
                        </div>
                        
                    </div>
                </div>

            

            </div>
        </div>
    </div>
</div>

<div class="totalreserva">
    @include('reservation.client.includes.modals')
</div>






@section('js')
    <script>
        $(document).ready(function() {

            //permitir chequear solo un checkbox de tiempos de reservación
            $("input:checkbox").on('click', function() {
                // in the handler, 'this' refers to the box clicked on
                var $box = $(this);
                var $id = $(this).attr("id");
                console.log($id);
                if ($box.is(":checked")) {
                    // the name of the box is retrieved using the .attr() method
                    // as it is assumed and expected to be immutable
                    var group = "input:checkbox[name='" + $box.attr("name") + "']";
                    // the checked state of the group/box on the other hand will change
                    // and the current value is retrieved using .prop() method
                    $(group).prop("checked", false);
                    $box.prop("checked", true);
                } else {
                    $box.prop("checked", false);
                }

                if ($id === 'ch_dia') {
                    $("#labelCondition").html("Ingrese la cantidad de días con la que puede el cliente reservar");
                    $("#condi").show();
                } else if ($id === 'ch_horas') {
                    $("#labelCondition").html("Ingrese la cantidad de horas con la que puede el cliente reservar");
                    $("#condi").show();
                } else {
                    $("#labelCondition").html("");
                    $("#dias_").hide();
                    $("#condi").hide();
                }
            });

            //cargar mesas de acuerdo a la zona
            $('#btn_save_motivo').on('click', function(){
                saveMotivo();
            });

            $(document).on('click', '.editarMotivo', function(){
                var idd = $(this).data('id');
                $('input[name=name_motivo]').val(idd.name);
                $('textarea[name=description_motivo]').val(idd.description);
                $('input[name=price_motivo]').val(idd.price);
                $('input[name=motive_id]').val(idd.id);
            });

            $(document).on('click', '.mostrarMesas', function(){
                var idr = $(this).data('id');
                $('#numReservation').html(idr)
                var formdata = new FormData();
                formdata.append('reservacion_id',idr);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('reservation.getTables')}}",
                    type: 'POST',
                    success: function (data) {
                      
                        $('#mesasReservacion').html(data);

                        $('#modal-reservation-mesas').modal('show');
                        
                    },
                    data: formdata,
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });


            

            function saveMotivo(){
                //obteniendo los datos
                let name = $('#name_motivo').val();
                let description = $('#description_motivo').val();
                let price = $('#price_motivo').val();
                let motive_id = $('#motive_id').val();
                //url
                var url_ajax = "{{route('reservationreason.store')}}";
                //form de register motivo
                var formData = new FormData();
                formData.append('motive_id', motive_id);
                formData.append('name', name);
                formData.append('description', description);
                formData.append('price', price);
                //peticion
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    encoding:"UTF-8",
                    url: url_ajax,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType:'json',
                    beforeSend:function(){
                        //mensaje de alerta
                        let timerInterval;
                        Swal.fire({
                            target: document.getElementById('modal-registrar-motivo'),
                            title: "Cargando",
                            text: "Procesando la información, espere...",
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                    }
                }).done(function(respuesta){
                    console.log(respuesta);
                    /* if (respuesta.data.length > 0) {
                        $('#div_cargar_motivos').html(respuesta);
                    } */
                    $('#modal-registrar-motivo').modal('hide');
                    if (!respuesta.error) {
                        $('#name_motivo').val("");
                        $('#description_motivo').val("");
                        $('#price_motivo').val("");
                        consultarmotivos();
                            Swal.fire({
                                title: 'Motivo de reservación guardado!',
                                icon: 'success',
                                showConfirmButton: true,
                                timer: 2000
                            });

                    } else {
                            setTimeout(function(){
                                Swal.fire({
                                    title: respuesta.mensaje,
                                    icon: 'error',
                                    showConfirmButton: true,
                                    timer: 2000
                                });
                            },2000);
                    }
                    
                }).fail(function(resp){
                    console.log(resp);
                    $('#modal-registrar-motivo').modal('hide');
                    Swal.fire({
                        title: "Se presento un error!",
                        text: 'Intenta otra vez, si persiste el error, comunicate con el area encargada, gracias.',
                        icon: 'error',
                    });
                });
            };




            
            
        });
        //consultar motivos
        function consultarmotivos()
        {
            var url = "{{ route('reservationreason.obtener') }}";
                $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                encoding:"UTF-8",
                url: url,
                method: "GET",
                cache: false,
                datatype: 'html',
                /* data: {
                    type: ids_type_account,
                }, */
                beforeSend:function(){
                    Swal.fire({
                        target: document.getElementById('modal-registrar-motivo'),
                        text: "Cargando datos, espere por favor...",
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                }
                }).done(function(respuesta){

                    $('#div_cargar_motivos').html(respuesta);
                    Swal.close();

                }).fail(function(resp){
                    console.log(resp);
                });
        }

        
        $("#configReservation").submit(function(e) {
            e.preventDefault();
            var formData = new FormData($("#configReservation")[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('reservation.storeConfig')}}",
                type: 'POST',
                success: function (data) {

                    console.log(data);
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
        });

        //retornar información
        $(document).ready(function() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('reservation.getInfoConfig')}}",
                type: 'POST',
                success: function (data) {
                    if(data.length>0){
                        $('input[value='+data[0].minimum_period+']').prop('checked', true);
                        $('input[name=time_reservation_number]').val(data[0].condition_period);
                        $('input[name=porcentage_payment]').val(data[0].percentage_payment);
                        $('input[name=wait_time]').val(data[0].wait_time);
                        $('input[name=anticipation_time]').val(data[0].anticipation_time);
                        $('input[name=standard_price]').val(data[0].standard_price);
                        $('input[name=update_price]').val(data[0].update_price);
                        if(data[0].condition_period==1){
                            $('input[name=time_reservation]').prop('checked', true);
                        }
                        var mesas = data[0].mesas.split(',');
                        for(var i=0;i<mesas.length;i++){
                            $('#zonas option[value='+mesas[i]+']').prop('selected', true);

                            $('#zonas').trigger('change')
                        }
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

    

        var totalReservas = new Vue({
            el: '.totalreserva',
            data: {
                reserva_id:0,
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

        $(".modalPendiente").on('click', function() {

            $('.ckpropina').css('display','none');
            var item = $(this).data('item');

            totalReservas.reserva_id = item.id;
            totalReservas.priceReservation = item.pendiente;
            totalReservas.priceReservationFormated = puntosMil(item.pendiente);
            
            $('#modal-payment-reservation').modal('show');
         
        });

        function puntosMil(value){
            return value.toString().replace(/\D/g, "")
            .replace(/([0-9])([0-9]{0})$/, '$1')
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        }

        function pagarReserva(){
            var formData = new FormData();
            formData.append('reserva_id',totalReservas.reserva_id);
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
                url: "{{route('reservation.storePendiente')}}",
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

        



            

    </script>
@endsection
@endsection
