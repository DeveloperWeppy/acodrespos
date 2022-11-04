@extends('layouts.app', ['title' => __('Settings')])

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
                            <h3 class="mb-0">{{ __('Administrar Preguntas de la Encuesta') }}</h3>
                            
                        </div>
                        <div class="col-6 text-right">
                            <button  type="button" class="btn btn-success text-white" data-toggle="modal" data-target="#exampleModal">{{ __('Agregar Pregunta') }}</button>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    @if(count($questions)>0)
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nº</th>
                                        <th>Pregunta</th>
                                        <th>{{ __('Acciones') }}</th>
                                    </tr>                                    
                                </thead>
                                <tbody>
                                    @foreach ($questions as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->question}}</td>
                                        <td>
                                            <button data-toggle="modal" data-target="#updateQuestion{{ $item->id }}" class="btn btn-xs btn-success"><i class="fas fa-edit"></i>Editar</button>
                                            <button class="btn btn-xs btn-danger" onclick="deleteQuestion({{$item->id}})">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button> 
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="updateQuestion{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="exampleModalLabel">Editar Pregunta</h5>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                              </button>
                                            </div>
                                            <form action="" id="" class="form_edit_questions">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="message-textt" class="col-form-label">Edite la pregunta:</label>
                                                        <textarea class="form-control" id="message-textt" name="question" required>{{$item->question}}</textarea>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="id" value="{{$item->id}}">
                                                <div class="modal-footer">
                                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                  <button type="submit" class="btn btn-primary">Actualizar</button>
                                                </div>
                                            </form>
                                            
                                          </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    
                                </tbody>
                            </table>
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
<!-- Modal crear-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Guardar Pregunta</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="" id="form_register_questions">
            <div class="modal-body">
                <div class="form-group">
                    <label for="message-text" class="col-form-label">Registre la pregunta:</label>
                    <textarea class="form-control" id="message-text" name="question" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
        
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