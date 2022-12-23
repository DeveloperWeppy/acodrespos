@foreach ($setup['discounts'] as $item)
    <tr>
        <td>{{ $item->name }}</td>
        <td>{{ $item->type == 0 ? $item->price." ".config('settings.cashier_currency') : $item->price." %"}}</td>
        <td>{{ $item->active_from }}</td>
        <td>{{ $item->active_to }}</td>
       
        <td> 
            @if($item->opcion_discount==0)
            <span class="">Todo el restaurante</span>
            @elseif($item->opcion_discount==1)
            <span class="">Productos</span>
            @else
            <span class="">Categorias</span>
            @endif
        </td>
        
        <td hidden> 
            @if($item->active==0)
            <span class="badge badge-success badge-pill">Inactivo</span>
            @else
            <span class="badge badge-warning badge-pill">Activo</span>
            @endif
        </td>
        <td>
            <a href="{{ route("admin.restaurant.coupons.editDiscount",$item->id) }}" class="btn btn-primary btn-sm">{{ __('crud.edit') }}</a>
            <a href="{{ route("admin.restaurant.coupons.deleteDiscount",$item->id) }}"  class="btn btn-danger btn-sm">{{ __('crud.delete') }}</a>
        </td>
    </tr>
@endforeach