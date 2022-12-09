
    @if (count($reservaciones) == 0)
    @else

    

        @foreach ($reservaciones as $item)

            <tr>
                <td>
                    <a class="btn badge badge-success badge-pill" href="{{route('reservation.edit',[$item->id])}}">#{{$item->id}}</a>
                </td>
                <td>
                    <p><small class="text-muted">{{ $item->date_reservation }}</small></p><p>
                </p></td>
                
                <td class="table-web">
                    {{$item->cli}}
                </td>
                
                <td class="table-web">
                    <button class="btn btn-icon btn-1 btn-sm btn-primary mostrarMesas" type="button" data-id="{{$item->id}}">
                        <span class="btn-inner--icon"><i class="fa fa-address-book-o"></i></span>
                    </button>
                </td>
                <td>
                    <span class="badge badge-success badge-pill">{{$item->mot}}</span>
                </td>
                
                <td class="table-web">
                    {{$item->total}}
                </td>
                <td class="table-web">
                    @if($item->pendiente>0)
                    <button data="rejected_by_restaurant" class="btn btn-sm  btn-danger modalPendiente" data-item="{{json_encode($item)}}" >{{$item->pendiente}}</button> 
                    @else
                    No hay acciones...
                    @endif
                </td>
                <td>
                    @if($item->active==1)
                        <span class="badge badge-danger badge-pill">Pendiente</span>
                    @else
                        <span class="badge badge-success badge-pill">Terminado</span>
                    @endif
                   
                </td>
    
                
            </tr>
        @endforeach

        
        

    @endif

    

