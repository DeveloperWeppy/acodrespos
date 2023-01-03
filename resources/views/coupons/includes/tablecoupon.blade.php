@foreach ($setup['items'] as $item)
    <tr>
        <td>{{ $item->name }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ $item->has_free_delivery==1?"Envío gratis":($item->type == 0 ? $item->price." ".config('settings.cashier_currency') : $item->price." %")}}</td>
        <td>{{ $item->has_ilimited==1?"Sin límite":$item->active_from }}</td>
        <td>{{ $item->has_ilimited==1?"Sin límite":$item->active_to }}</td>
        <td>{{ $item->limit_to_num_uses }}</td>
        <td>{{ $item->used_count }}</td>
        <td hidden> 
            @if($item->active==0)
            <span class="badge badge-success badge-pill">Inactivo</span>
            @else
            <span class="badge badge-warning badge-pill">Activo</span>
            @endif
        </td>
        @include('partials.tableactions',$setup)
    </tr>
@endforeach