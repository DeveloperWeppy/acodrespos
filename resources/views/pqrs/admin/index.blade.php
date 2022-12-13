@extends('layouts.app', ['title' => __('PQRS')])

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--9">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
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
                            <span class="text-capitalize text-white badge bg-gradient-success">Solicitud Respondida</span>
                        </div>
                    </div>
                    @if(count($pqrs_all)>0)
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush dataTable-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nº</th>
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
                                        <td>{{$item->id}}</td>
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
                            @else
                                <h4>{{ __('You don`t have any orders') }} ...</h4>
                            @endif
                        </div>
                        @else
                        <div class="text-center">
                            <h3>Parece que no hay ninguna pregunta aún...</h3>
                        </div>
                    @endif
                </div>
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