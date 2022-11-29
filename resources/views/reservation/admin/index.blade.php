@extends('layouts.app', ['title' => __('Restaurants')])
@section('admin_title')
    {{ __('Reservas') }}
@endsection
@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Configuración de Reservas') }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        @include('partials.flash')
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
                                            <label for="" class="form-control-label">Ingrese la cantidad de días con la
                                                que puede el cliente reservar</label>
                                            <input type="number" name="" class="form-control " id=""
                                                value="0">
                                        </div>
                                    </div>
                                    <div id="horas">
                                        <div class="form-group">
                                            <label for="" class="form-control-label">Ingrese la cantidad de horas con la
                                                que puede el cliente reservar</label>
                                            <input type="number" name="" class="form-control " id=""
                                                value="0">
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
                            </div>

                            <h6 class="heading-small text-muted mb-2 mt-3">{{ __('Administre los Motivos de Reservación') }}</h6>
                            <p>Cree los motivos de reservación que tendrá disponibles para los clientes.</p>

                            @include('reservation.admin.includes.registrarmotivo')

                            <div class="row">
                                <div class="col-sm-12 mb-2">
                                    <button type="button" class="btn btn-sm btn-primary float-left" data-toggle="modal" data-target="#modal-registrar-motivo">Agregar Motivo</button>
                                </div>
                                <div class="col-sm-3 col-12">
                                    <h4>Nombre del Motivo</h4>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <h4 class="text-center">Descripción del Motivo</h4>
                                </div>
                                <div class="col-sm-3 col-12">
                                    <h4>Precio del Motivo</h4>
                                </div>
                            </div>
                            @include('reservation.admin.includes.cargarmotivos')

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
                        </nav>
                    </div> 
                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>

@section('js')
    <script>
        $(document).ready(function() {
            $("#horas").hide();
            $("#dias_").hide();
            $("#div_mesas").hide();
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
                } else if ($id === 'ch_horas') {
                    $("#horas").show();
                    $("#dias_").hide();
                } else {
                    $("#horas").hide();
                    $("#dias_").hide();
                }
            });

            //cargar mesas de acuerdo a la zona
            $("#zonas").on('change', function() {
                var id_zona = $(this).val();

                if (id_zona == 'seleccione') {
                    $("#div_mesas").hide();
                    consultarmesa(id_zona);
                    select_mesas.empty();
                } else {
                    $("#div_mesas").show();
                    consultarmesa(id_zona);
                    select_mesas.empty();
                }
            });

            function consultarmesa(id_zona) {
                var url = "{{ route('reservation.obtener') }}";
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    encoding: "UTF-8",
                    url: url,
                    method: "POST",
                    cache: false,
                    datatype: 'html',
                    data: {
                        zona: id_zona,
                    },
                    beforeSend: function() {
                        Swal.fire({
                            text: "Cargando datos, espere por favor...",
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                    }
                }).done(function(response) {
                    $.each(response.data, function (key, value) {
                        select_mesas.append("<option value='" + value.id + "'>" + value.name + "</option>");
                    });
                    Swal.close();

                }).fail(function(resp) {
                    console.log(resp);
                });
            }
            $('#btn_save_motivo').on('click', function(){
                saveMotivo();
            });

            function saveMotivo(){
                //obteniendo los datos
                let name = $('#name_motivo').val();
                let description = $('#description_motivo').val();
                let price = $('#price_motivo').val();
                let restaurant = $('#restaurant_id').val();
                //url
                var url_ajax = "{{route('reservationreason.store')}}";
                var formData = new FormData();
                formData.append('restaurant_id', restaurant);
                formData.append('name', name);
                formData.append('description', description);
                formData.append('price', price);
                //peticion
                console.log(name);
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
                    if (respuesta.data.length > 0) {
                        $('#div_cargar_motivos').html(respuesta);
                    }
                    Swal.fire({
                        text: "Información Cargada",
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('modal-registrar-motivo').modal('hide');
                }).fail(function(resp){
                    console.log(resp);
                    $('modal-registrar-motivo').modal('hide');
                    Swal.fire({
                        title: "Se presento un error!",
                        text: 'Intenta otra vez, si persiste el error, comunicate con el area encargada, gracias.',
                        icon: 'error',
                    });
                });
            };
        });
    </script>
@endsection
@endsection
