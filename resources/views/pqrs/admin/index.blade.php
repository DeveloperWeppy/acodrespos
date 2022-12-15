@extends('layouts.app', ['title' => __('PQRS')])

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--9">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h3 class="mb-0 text-capitalize">{{ __('lISTADO DE SOLICITUDES PQRS') }}</h3>
                            
                        </div>
                        <div class="col-6 text-right">
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        Estados de la solicitud:
                        <div class="col-sm-12">
                            <span class="text-capitalize text-white badge bg-gradient-warning">Radicado</span>
                            <span class="text-capitalize text-white badge bg-gradient-info">En Revisión</span>
                            <span class="text-capitalize text-white badge bg-gradient-success">Solucionado</span>
                        </div>
                    </div>

                    <form method="GET" class="pb-3" action="{{route('pqrs.index_admin')}}">
                        <br>
                        <div class="tab-content orders-filters">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="client">Filtrar por consecutivo</label>
            
                                            <select class="form-control" id="consecutive" name="consecutive" >
                                                <option d value="" > -- Buscar Consecutivo -- </option>
                                                @foreach($consecutives as $key)
                                                    <option value="{{$key->consecutive_case}}" <?php if(isset($_GET['consecutive'])&&$_GET['consecutive'].""==$key->consecutive_case.""){echo "selected";} ?> >{{$key->consecutive_case}}</option>
                                                @endforeach
                                                </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="client">Filtrar por email</label>
            
                                            <select class="form-control" id="email" name="email" >
                                                <option d value="" > -- Buscar email -- </option>
                                                @foreach($emails as $key)
                                                    <option value="{{$key->email}}" <?php if(isset($_GET['email'])&&$_GET['email'].""==$key->email.""){echo "selected";} ?> >{{$key->email}}</option>
                                                @endforeach
                                                </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="client">Filtrar por tipo</label>
            
                                            <select class="form-control" id="category" name="category" >
                                                <option d value="" > -- Buscar tipo -- </option>
                                                @foreach($category as $key)
                                                    <option value="{{$key->type_radicate}}" <?php if(isset($_GET['category'])&&$_GET['category'].""==$key->type_radicate.""){echo "selected";} ?> >{{$key->type_radicate}}</option>
                                                @endforeach
                                                </select>
                                        </div>
                                    </div>

                                    
                                                            
                                </div>
            
                                <div class="col-md-6 offset-md-6">
                                    <div class="row">
                                        @if ($_GET)
                                            <div class="col-md-4">
                                                <a href="{{ Request::url() }}" class="btn btn-md btn-block">{{ __('Clear Filters') }}</a>
                                            </div>
                                            <div class="col-md-4">
                                            <a href="{{Request::fullUrl().'&report=true' }}" class="btn btn-md btn-success btn-block">{{ __('Download report') }}</a>
                                            </div>
                                        @else
                                            <div class="col-md-8 text-right">
                                                <a href="{{Request::fullUrl().'?report=true' }}" class="btn btn-md btn-success">{{ __('Download report') }}</a>
                                            </div>
                                        @endif
                                        
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary btn-md btn-block">Filtrar</button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </form>
                   
                </div>

                @if(count($pqrs_all)>0)
                <div class="table-responsive">
                    <table class="table align-items-center table-flush dataTable-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Nº de Consecutivo</th>
                                <th>Persona</th>
                                <th>Correo</th>
                                <th>Tipo de Radicado</th>
                                <th>Estado del Radicado</th>
                                <th>{{ __('Ver Detalle') }}</th>
                            </tr>                                    
                        </thead>
                        <tbody>
                            @foreach ($pqrs_all as $item)
                            @php
                                $color = '';
                                if ($item->status=='radicado') {
                                    $color = 'bg-gradient-warning';
                                } else if($item->status=='en revision'){
                                    $color = 'bg-gradient-info';
                                }else{
                                    $color = 'bg-gradient-success';
                                }
                                
                            @endphp
                            <tr>
                                <td>{{$item->consecutive_case}}</td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->email}}</td>
                                <td>{{$item->type_radicate}}</td>
                                <td><span class="text-capitalize text-white badge {{$color}}">{{$item->status}}</span></td>
                                <td>
                                    <a href="{{ route('pqrs.show',$item->id)}}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4">
                    @if(count($pqrs_all))
                        <nav class="d-flex justify-content-end" aria-label="...">
                            {{ $pqrs_all->appends(Request::all())->links() }}
                        </nav>
                    @endif
                </div>
                @else
                    <div class="text-center">
                        <h3>Parece que no hay ninguna solicitud de PQR aún...</h3>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
    $('#form_register_questions').on('submit', function(e) {
                event.preventDefault();
                if ($('#form_register_questions')[0].checkValidity() === false) {
                    event.stopPropagation();
                } else {
                    $('#exampleModal').modal('hide');
                    // agregar data
                    var $thisForm = $('#form_register_questions');
                    var formData = new FormData(this);

                    //ruta
                    var url = "{{route('admin.encuesta.store')}}";

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        type: "POST",
                        encoding:"UTF-8",
                        url: url,
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType:'json',
                        beforeSend:function(){
                            Swal.fire({
                                text: 'Validando datos, espere porfavor...',
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                },
                            });
                        }
                    }).done(function(respuesta){
                        //console.log(respuesta);
                        if (!respuesta.error) {
                            Swal.fire({
                                title: 'Pregunta registrada!',
                                icon: 'success',
                                showConfirmButton: true,
                                timer: 2000
                            });

                            setTimeout(function(){
                                location.reload();
                            },2000);

                        } else {
                            setTimeout(function(){
                                Swal.fire({
                                    title: espuesta.mensaje,
                                    icon: 'error',
                                    showConfirmButton: true,
                                    timer: 2000
                                });
                            },2000);
                        }
                    }).fail(function(resp){
                        console.log(resp);
                    });

                }
                $('#form_register_questions').addClass('was-validated');

    });
   

    $( document ).ready(function() {
        $('.form_edit_questions').on('submit', function(e) {
                event.preventDefault();
                if ($('.form_edit_questions')[0].checkValidity() === false) {
                    
                    event.stopPropagation();
                } else {
                    
                    // agregar data
                    var $thisForm = $('.form_edit_questions');
                    var formData = new FormData(this);
                    for (var p of formData) {
                        let name = p[0];
                        let value = p[1];

                        if (name == 'id') {
                            $('#updateQuestion'+value).modal('hide');
                        }
                    }
                    //ruta
                    var url = "{{route('admin.encuesta.update')}}";

                     $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        type: "POST",
                        encoding:"UTF-8",
                        url: url,
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType:'json',
                        beforeSend:function(){
                            Swal.fire({
                                text: 'Validando datos, espere...',
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                },
                            });
                        }
                    }).done(function(respuesta){
                        //console.log(respuesta);
                        if (!respuesta.error) {
                            Swal.fire({
                                title: 'Pregunta Actualizada!',
                                icon: 'success',
                                showConfirmButton: true,
                                timer: 2000
                            });

                            setTimeout(function(){
                                location.reload();
                            },2000);

                        } else {
                            setTimeout(function(){
                                Swal.fire({
                                    title: espuesta.mensaje,
                                    icon: 'error',
                                    showConfirmButton: true,
                                    timer: 2000
                                });
                            },2000);
                        }
                    }).fail(function(resp){
                        console.log(resp);
                    });

                }
                $('.form_edit_questions').addClass('was-validated');

        });
    });

    function editQuestion(id, question){
        console.log(id+"+"+question);
    }
    
            function deleteQuestion(id){
            //console.log("soy id"+id);
                Swal.fire({
                    title: 'Eliminar Pregunta',
                    text: "¿Estas seguro de eliminar el registro de esta pregunta?",
                    icon: 'question',
                    showCancelButton: "Cancelar",
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Si, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "encuesta/delete/"+id;
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            type: "GET",
                            encoding:"UTF-8",
                            url: url,
                            dataType:'json',
                            beforeSend:function(){
                                Swal.fire({
                                    text: 'Eliminando pregunta, espere...',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    },
                                });
                            }
                        }).done(function(respuesta){
                            //console.log(respuesta);
                            if (!respuesta.error) {
                                Swal.fire({
                                    title: 'Pregunta Eliminada!',
                                    icon: 'success',
                                    showConfirmButton: true,
                                    timer: 2000
                                });
                                setTimeout(function(){
                                location.reload();
                                },2000);
                            } else {
                                setTimeout(function(){
                                    Swal.fire({
                                        title: respuesta.mensaje,
                                        icon: 'error',
                                        showConfirmButton: true,
                                        timer: 4000
                                    });
                                },2000);
                            }
                        }).fail(function(resp){
                            console.log(resp);
                        });
                    }
                })
        }
    </script>
@endsection

@endsection