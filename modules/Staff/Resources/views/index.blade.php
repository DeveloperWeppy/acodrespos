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
                <a href="{{ route( $setup['webroute_path']."delete",$param) }}" class="btn btn-danger btn-sm">Eliminar</a>
                <a href="{{ route( $setup['webroute_path']."loginas",['staff'=>$item->id]) }}" class="btn btn-success btn-sm">{{ __('Login as') }}</a>
            </td>
        </tr> 
    @endforeach
@endsection