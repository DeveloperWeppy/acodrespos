@foreach ($setup['discounts'] as $item)
    <tr>
        <td>{{ $item->name }}</td>
        <td>{{ $item->type == 0 ? $item->price." ".config('settings.cashier_currency') : $item->price." %"}}</td>
        <td>{{ $item->active_from }}</td>
        <td>{{ $item->active_to }}</td>
        <td>{{ $item->opcion_discount }}</td>
        <td>
            <a href="{{ route("admin.restaurant.coupons.editDiscount",$item->id) }}" class="btn btn-primary btn-sm">{{ __('crud.edit') }}</a>
            <a href="{{ route("admin.restaurant.coupons.deleteDiscount",$item->id) }}"  class="btn btn-danger btn-sm">{{ __('crud.delete') }}</a>
        </td>
    </tr>
@endforeach