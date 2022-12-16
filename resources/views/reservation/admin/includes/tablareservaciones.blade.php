
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
                
                <td class="table-web">
                    @money( $item->total, config('settings.cashier_currency'),config('settings.do_convertion'))
            
                </td>
                <td class="table-web">
                    @if($item->pendiente>0)
                    <button data="rejected_by_restaurant" class="btn btn-sm  btn-danger modalPendiente" data-item="{{json_encode($item)}}" >
                        @money( $item->pendiente, config('settings.cashier_currency'),config('settings.do_convertion'))
                    </button> 
                    @else
                    No hay acciones...
                    @endif
                </td>
                <td>

                    @if($item->active==1)
                    <span class="badge badge-warning badge-pill">Agendada</span> 
                    @elseif($item->active==2)
                        <span class="badge badge-info badge-pill">En proceso</span>
                    @else
                        <span class="badge badge-success badge-pill">Finalizado</span>
                    @endif
                   
                </td>

                <td>

                    @if($item->active==1)
                        <button class="btn btn-sm  btn-danger liberarMesa" data-item="{{json_encode($item)}}" >Liberar mesa</button> 
                    @elseif($item->active==2)
                        <span class="badge badge-info badge-pill">En proceso</span>
                    @else
                        No hay acciones...
                    @endif
                   
                </td>
    
                
            </tr>
        @endforeach

        
        

    @endif

    

