@extends('general.index', $setup)
@section('thead')
    <th>{{ __('Name') }}</th>
    <th>{{ __('Options') }}</th>
    <th>{{ __('Actions') }}</th>
@endsection
@section('tbody')
@foreach ($setup['items'] as $item)
<tr>
    <td>{{ $item->name }}</td>
    <td>{{ $item->options }}</td>
    <td><a href="{{ route("items.options.edit",["option"=>$item->id]) }}" class="btn btn-primary btn-sm">{{ __('Edit') }}</a><button onclick="eliminarOpcion('{{ route('items.options.delete',['option'=>$item->id]) }}')" class="btn btn-danger btn-sm">{{ __('Delete') }}</button></td>
</tr> 
@endforeach

@endsection

@section('js')
<script>
    function eliminarOpcion(url){
            Swal.fire({
            title: '¿Estás seguro de eliminar esta opción?',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            }).then((result) => {
                
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                window.location.href = url;
            }
            })
        }
</script>
@endsection