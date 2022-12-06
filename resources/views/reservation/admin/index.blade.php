@extends('layouts.app', ['title' => __('Restaurants')])
@section('admin_title')
    {{ __('Reservas') }}
@endsection
@section('content')

<div class="header bg-gradient-info pb-6 pt-5 pt-md-8">
    <div class="container-fluid">

        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="res_menagment" role="tablist">

                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active " id="tabs-menagment-main" data-toggle="tab" href="#menagment" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-badge mr-2"></i>Reservaciones</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-menagment-main" data-toggle="tab" href="#accountbanks" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-settings-gear-65"></i> Configuración de reservaciones</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-menagment-main" data-toggle="tab" href="#hours" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-tag mr-2"></i>Motivos de reservaciones</a>
                </li>

            </ul>
        </div>

    </div>
</div>


<div class="container-fluid mt--7">
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
                                Reservaciones
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-sm-2 col-12">
                                    <h4>Cliente</h4>
                                </div>
                                <div class="col-sm-2 col-12">
                                    <h4>Area - Mesa</h4>
                                </div>
                                <div class="col-sm-2 col-12">
                                    <h4>Motivo</h4>
                                </div>
                                <div class="col-sm-2 col-12">
                                    <h4>Valor</h4>
                                </div>
                                <div class="col-sm-2 col-12">
                                    <h4>Pendiente</h4>
                                </div>
                                <div class="col-sm-2 col-12">
                                    <h4>Estado</h4>
                                </div>
                                <div class="col-sm-2 col-12">
                                    <h4><a type="button" class="btn btn-sm btn-primary float-left" href={{route('reservation.create')}}>Nueva reservación</button></a>
                                </div>
                            </div>
                            <div class="row mt-3" id="div_cargar_reservas">
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Tab Managment -->
                 <div class="tab-pane fade" id="accountbanks" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="card bg-secondary shadow mb-4" >
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                Configuración de reservaciones
                            </div>
                        </div>
                        <form action="" id="configReservation">
                        <div class="card-body">
                           
                            @if ($compani->has_reservation==1)
                                <h6 class="heading-small text-muted mb-2">{{ __('Tiempos de Reservación') }}</h6>
                                <p>Habilita el tiempo de antelación de reserva con respecto al día que quiera el cliente reservar.</p>
                                <div class="row">
                                    <div class="col-sm-12 col-12">
                                        <div class="form-group  ">
                                            <div class="custom-control custom-checkbox">
                                                <input value="dia" type="checkbox" class="custom-control-input"
                                                    name="time_reservation" id="ch_dia">
                                                <label class="custom-control-label" for="ch_dia">Día</label>
                                            </div>
    
                                            <div class="custom-control custom-checkbox">
                                                <input value="hora" type="checkbox" class="custom-control-input"
                                                    name="time_reservation" id="ch_horas" >
                                                <label class="custom-control-label" for="ch_horas">Hora</label>
                                            </div>
    
                                            <div class="custom-control custom-checkbox">
                                                <input value="dia_actual" type="checkbox" class="custom-control-input"
                                                    name="time_reservation" id="ch_actual">
                                                <label class="custom-control-label" for="ch_actual">Día Actual</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-12">
                                        <div class="form-group">
                                            <div id="dias_">
                                                <label for="" class="form-control-label">Ingrese la cantidad de días con la
                                                que puede el cliente reservar</label>
                                            </div>
                                            <div id="horas" style="display:none">
                                                <label for="" class="form-control-label">Ingrese la cantidad de horas con la
                                                    que puede el cliente reservar</label>
                                            </div>
                                            <input type="number" name="time_reservation_number" class="form-control " id="condi"
                                                value="0" minlength="0" >
                                        </div>
                                    </div>
    
                                    <div class="col-sm-12 col-12">
                                        <div class="form-group">
                                            <div>
                                                <label for="" class="form-control-label">Porcentaje para pago en dos pasos (%)</label>
                                            </div>
                                            <input type="number" name="porcentage_payment" class="form-control " id=""
                                                value="0">
                                        </div>
                                    </div>
    
                                    <div class="col-sm-12 col-12">
                                        <div class="form-group">
                                            <div>
                                                <label for="" class="form-control-label">Tiempo de espera en minutos</label>
                                            </div>
                                            <input type="number" name="wait_time" class="form-control " id=""
                                                value="0">
                                        </div>
                                    </div>
    
                                    <div class="col-sm-12 col-12">
                                        <div class="form-group">
                                            <div>
                                                <label for="" class="form-control-label">Precio estándar por mesa</label>
                                            </div>
                                            <input type="number" name="standard_price" class="form-control " id=""
                                                value="0">
                                        </div>
                                    </div>
    
                                    <div class="col-sm-12 col-12">
                                        <div class="form-group  ">
                                            <div class="custom-control custom-checkbox">
                                                <input value="1" type="checkbox" class="custom-control-input"
                                                    name="time_reservation" id="check_no_cost">
                                                <label class="custom-control-label" for="check_no_cost">Desabilitar pago para reservaciones</label>
                                            </div>
                                        </div>
                                    </div>
    
                                </div>
    
                                <h6 class="heading-small text-muted mb-2">{{ __('Habilitar mesas de Reservación') }}</h6>
                                <p>Establezca las mesas que estarán habilitadas para reservar</p>
    
                                <div class="row">
                                    <div class="col-sm-12 col-12">
                                        <label for="zonas" class="form-control-label">Seleccione la Zona</label>
                                        <select name="zonas[]" class="form-control" id="zonas" multiple>
                                            <option value="">-- Seleccionar --</option>
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
                                </div>

                               
    
                            @else
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p>No has habilitado <strong>Reservación</strong>, ve al módulo de Restaurante y habilita la opción de Reservación para que puedas configurar todos los parámetros de Reservas</p>
                                        <lottie-player src="{{ asset('animations/no_check.json')}}"  background="transparent"  speed="1"  style="width: 300px; height: 300px;" loop autoplay></lottie-player>
                                    </div>
                                </div>
                            @endif
                            
                        </div>

                        <div class="card-footer py-4">
                            <nav class="d-flex justify-content-end" aria-label="...">
                              
                                <button type="submit" class="btn btn-md btn-primary float-left" >Guardar Cambios</button>
                            </nav>
                        </div> 
                    </form> 
                    </div>
                </div>

                 <!-- Tab Managment -->
                 <div class="tab-pane fade" id="hours" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                {{ __('Administre los Motivos de Reservación') }}
                            </div>
                        </div>
                        <div class="card-body">
                         
                            <p>Cree los motivos de reservación que tendrá disponibles para los clientes.</p>

                            @include('reservation.admin.includes.registrarmotivo')

                            <div class="row">
                                <div class="col-sm-3 col-12">
                                    <h4>Nombre del Motivo</h4>
                                </div>
                                <div class="col-sm-3 col-12">
                                    <h4>Descripción del Motivo</h4>
                                </div>
                                <div class="col-sm-3 col-12">
                                    <h4>Precio del Motivo</h4>
                                </div>
                                <div class="col-sm-3 col-12">
                                    <h4><button type="button" class="btn btn-sm btn-primary float-left" data-toggle="modal" data-target="#modal-registrar-motivo">Agregar Motivo</button></h4>
                                </div>
                            </div>
                            <div class="row mt-3" id="div_cargar_motivos">
                            @include('reservation.admin.includes.cargarmotivos')
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>




@section('js')
    <script>
        $(document).ready(function() {
            $("#horas").hide();
            $("#dias_").hide();
            $("#div_mesas").hide();
            //consultarcuenta();
            var select_mesas = $('#select_mesas');

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
                    $("#horas").hide();
                    $("#dias_").show();
                    $("#condi").show();
                } else if ($id === 'ch_horas') {
                    $("#horas").show();
                    $("#dias_").hide();
                    $("#condi").show();
                } else {
                    $("#horas").hide();
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
                alert(idd.id);
                $('input[name=name_motivo]').val(idd.name);
                $('textarea[name=description_motivo]').val(idd.description);
                $('input[name=price_motivo]').val(idd.price);
                $('input[name=motive_id]').val(idd.id);
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
                        $('input[name=standard_price]').val(data[0].standard_price);
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

            

    </script>
@endsection
@endsection
