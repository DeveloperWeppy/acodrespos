@extends('general.index', $setup)
@section('tbody')
    @foreach ($setup['items'] as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->email }}</td>
            <td>
                @foreach ($item->roles as $value)
                    {{$value->name == 'staff' ? 'Mesero' : ($value->name == 'manager_restorant' ? 'Administrador de Restaurante' : 'Cocina')}}
                @endforeach
            </td>
            <?php
                $param=[];
                $param[$setup['parameter_name']]=$item->id;
            ?>
            <td>
                <a href="{{ route( $setup['webroute_path']."edit",$param) }}" class="btn btn-primary btn-sm">Editar</a>
                {{-- <a href="{{ route( $setup['webroute_path']."delete",$param) }}" class="btn btn-danger btn-sm">Eliminar</a> --}}
                <button class="btn btn-danger btn-sm" onclick="deletesteaff({{$item->id}})">
                     Eliminar
                </button> 
                <a href="{{ route( $setup['webroute_path']."loginas",['staff'=>$item->id]) }}" class="btn btn-success btn-sm">{{ __('Login as') }}</a>
            </td>
        </tr> 
    @endforeach

    @section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function deletesteaff(id){
            //console.log("soy id"+id);
                Swal.fire({
                    title: 'Eliminar Empleado',
                    text: "¿Estas seguro de eliminar el registro de este empleado?",
                    icon: 'question',
                    showCancelButton: "Cancelar",
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Si, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "del/"+id;
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
                                    text: 'Eliminando empleado, espere...',
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
                                    title: 'Empleado Eliminado!',
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