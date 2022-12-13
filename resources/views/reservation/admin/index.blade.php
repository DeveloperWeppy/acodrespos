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
                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-menagment-main" data-toggle="tab" href="#clients" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-badge mr-2"></i>Solicitudes de reservación</a>
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
                                    <a type="button" class="btn btn-sm btn-primary" href={{route('reservation.create')}}>Nueva reservación</a>
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
                                            <th class="table-web" scope="col">Cliente</th>
                                            <th class="table-web" scope="col">Area - Mesa</th>
                                            <th scope="col">Motivo</th>
                                            <th class="table-web" scope="col">Valor</th>
                                            <th scope="col">Pendiente</th>
                                            <th scope="col">Estado</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="listaReservas" style="background: #ffffff;">
                                        @include('reservation.admin.includes.tablareservaciones')
                                    
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


                <!-- Tab Managment -->
                <div class="tab-pane fade show" id="clients" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    Solicitudes de reservación
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
                                            <th class="table-web" scope="col">Cliente</th>
                                            <th scope="col">Motivo</th>
                                            <th class="table-web" scope="col">Valor</th>
                                            <th scope="col">Estado</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="listaReservas" style="background: #ffffff;">
                                        @include('reservation.admin.includes.tablasolicitudreservaciones')                       
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="card-footer py-4">
                            @if(count($solicitudes))
                            <nav class="d-flex justify-content-end" aria-label="...">
                                {{ $solicitudes->appends(Request::all())->links() }}
                            </nav>
                            @else
                                <h4>No tienes solicitudes de reservas...</h4>
                            @endif
                        </div>
                        
                    </div>
                </div>

                    <div class="card-body">
                        @if ($compani->has_reservation==1)
                            <h6 class="heading-small text-muted mb-2">{{ __('Tiempos de Reservación') }}</h6>
                            <p>Habilita el tiempo de antelación de reserva con respecto al día que quiera el cliente reservar.
                            </p>
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
                                                name="time_reservation" id="ch_horas">
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
                                    <div id="dias_">
                                        <div class="form-group">
                                            <div id="">
                                                <label for="" class="form-control-label" id="labelCondition">Ingrese la cantidad de días con la
                                                que puede el cliente reservar</label>
                                            </div>
                                            <input type="number" name="time_reservation_number" class="form-control " 
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
                                                <label for="" class="form-control-label">Tiempo de espera en minutos despues de pasada la hora de reservación</label>
                                            </div>
                                            <input type="number" name="wait_time" class="form-control " id=""
                                                value="0">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-12">
                                        <div class="form-group">
                                            <div>
                                                <label for="" class="form-control-label">Tiempo en minutos en el que se marcara una mesa como ocupada antes de cada reservación</label>
                                            </div>
                                            <input type="number" name="anticipation_time" class="form-control " id=""
                                                value="0">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-12">
                                        <div class="form-group">
                                            <div>
                                                <label for="" class="form-control-label">Intervalo de tiempo en minutos entre cada reserva</label>
                                            </div>
                                            <input type="number" name="interval" class="form-control " id=""
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
                                        <div class="form-group">
                                            <div>
                                                <label for="" class="form-control-label">Precio modificación reserva</label>
                                            </div>
                                            <input type="number" name="update_price" class="form-control " id=""
                                                value="0">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-12">
                                        <div class="form-group  ">
                                            <div class="custom-control custom-checkbox">
                                                <input value="1" type="checkbox" class="custom-control-input"
                                                    name="check_no_cost" id="check_no_cost">
                                                <label class="custom-control-label" for="check_no_cost">Desabilitar pago para reservaciones</label>
                                            </div>
                                        </div>
                                    </div>
    
                                </div>
                            </div>

                            <h6 class="heading-small text-muted mb-2">{{ __('Habilitar mesas de Reservación') }}</h6>
                            <p>Establezca las mesas que estarán habilitadas para reservar</p>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <label for="zonas" class="form-control-label">Seleccione la Zona</label>
                                    <select name="" class="form-control" id="zonas">
                                        <option value="seleccione">-- Seleccione --</option>
                                        @foreach ($restoareas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6 col-12" id="div_mesas">
                                    <label for="select_mesas" class="form-control-label">Seleccione las mesas que estarán disponibles</label>
                                    <select name="mesas[]" class="form-control js-example-basic-multiple" multiple="multiple" id="select_mesas">
                                    </select>
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

                            <h6 class="heading-small text-muted mb-2 mt-3">{{ __('Administre los Motivos de Reservación') }}</h6>
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

                        @else
                            <div class="row">
                                <div class="col-sm-12">
                                    <p>No has habilitado <strong>Reservación</strong>, ve al módulo de Restaurante y habilita la opción de Reservación para que puedas configurar todos los parámetros de Reservas</p>
                                    <lottie-player src="{{ asset('animations/no_check.json')}}"  background="transparent"  speed="1"  style="width: 300px; height: 300px;" loop autoplay></lottie-player>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="totalreserva">
    @include('reservation.admin.includes.modals')
</div>





@section('js')
    <script>
        $(document).ready(function() {

            //permitir chequear solo un checkbox de tiempos de reservación
            $("input:checkbox").on('click', function() {
                // in the handler, 'this' refers to the box clicked on
                var $box = $(this);
                var $id = $(this).attr("id");
                
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
    </script>
@endsection
@endsection
