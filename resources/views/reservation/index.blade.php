@extends('layouts.app', ['title' => __('Restaurants')])
@section('admin_title')
    {{__('Reservas')}}
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
                                <h3 class="mb-0">{{ __('Reservas') }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        @include('partials.flash')
                    </div>

                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-2">{{ __('Tiempos de Reservación') }}</h6>
                        <p>Habilita el tiempo de antelación de reserva con respecto al día que quiera el cliente reservar.</p>
                        <div class="row">
                            <div class="col-sm-12 col-12">
                                <div class="form-group  ">
                                    <div class="custom-control custom-checkbox">
                                        <input value="dia"  type="checkbox" class="custom-control-input" name="time_reservation" id="ch_dia">
                                        <label class="custom-control-label" for="ch_dia">Día</label>
                                    </div>

                                    <div class="custom-control custom-checkbox">
                                        <input value="hora"  type="checkbox" class="custom-control-input" name="time_reservation" id="ch_horas">
                                        <label class="custom-control-label" for="ch_horas">Hora</label>
                                    </div>

                                    <div class="custom-control custom-checkbox">
                                        <input value="dia_actual" type="checkbox" class="custom-control-input" name="time_reservation" id="ch_actual">
                                        <label class="custom-control-label" for="ch_actual">Día Actual</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-12">
                                <div id="dias_">
                                    <div class="form-group">
                                        <label for="" class="form-control-label">Ingrese la cantidad de días con la que puede el cliente reservar</label>
                                        <input type="number" name="" class="form-control " id="" value="0">
                                    </div>
                                </div>
                                <div id="horas">
                                    <div class="form-group">
                                        <label for="" class="form-control-label">Ingrese la cantidad de horas con la que puede el cliente reservar</label>
                                        <input type="number" name="" class="form-control " id="" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="heading-small text-muted mb-2">{{ __('Habilitar mesas de Reservación') }}</h6>
                        <p>Establezca las mesas que estarán habilitadas para reservar</p>
                        
                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <label for="">Seleccione la Zona</label>
                                <select name="" class="form-control" id="zonas">
                                    <option value="seleccione">-- Seleccione --</option>
                                    @foreach ($restoareas as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-12">
                                <label for="">Seleccione las mesas que estarán disponibles</label>
                                <select name="" class="form-control" id="">
                                    @foreach ($restoareas as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                    } else if($id === 'ch_horas'){
                        $("#horas").show();
                        $("#dias_").hide();
                    }else{
                        $("#horas").hide();
                        $("#dias_").hide();
                    }
                });

                //cargar mesas de acuerdo a la zona
                $("#zonas").on('change', function() {
                    var id_zona = $(this).val();

                    if (id_zona=='seleccione') {
                        consultarmesa(id_zona);
                    }else{
                        consultarmesa(id_zona);
                    }
                });

                function consultarmesa(id_zona)
                {
                    var url = "{{ route('reservation.obtener') }}";
                    $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    encoding:"UTF-8",
                    url: url,
                    method: "POST",
                    cache: false,
                    datatype: 'html',
                    data: {
                        zona: id_zona,
                    },
                    beforeSend:function(){
                        Swal.fire({
                            text: "Cargando datos, espere por favor...",
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                    }
                    }).done(function(respuesta){

                        $('#div_cargar_infocuenta').html(respuesta);
                        Swal.close();

                    }).fail(function(resp){
                        console.log(resp);
                    });
                }
            });
        </script>
    @endsection
@endsection
