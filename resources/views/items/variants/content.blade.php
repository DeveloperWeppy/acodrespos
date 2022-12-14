@section('thead')
    <th>{{ __('Price') }}</th>
    <th>{{ __('Options') }}</th>
    <th>{{ __('Actions') }}</th>
@endsection
@section('tbody')
@foreach ($setup['items'] as $item)
<tr>
    <td>{{ $item->price }}</td>
    <td>
        {{ $item->optionsList }}
    </td>
    <td><a href="{{ route("items.variants.edit",["variant"=>$item->id]) }}" class="btn btn-primary btn-sm">{{ __('Edit') }}</a><button class="btn btn-danger btn-sm" onclick="eliminarVariante('{{ route('items.variants.delete',['variant'=>$item->id]) }}')">{{ __('Delete') }}</button></td>
</tr> 
@endforeach



@endsection

