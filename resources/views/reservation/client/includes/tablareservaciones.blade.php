
    @if (count($reservaciones) == 0)
    @else

    

        @foreach ($reservaciones as $item)

            <tr>
                <td>
                    <a class="btn badge badge-success badge-pill" href="{{route('reservation.editsolicitud',[$item->id])}}">#{{$item->id}}</a>
                </td>
                <td>
                    <p><small class="text-muted">{{ Carbon\Carbon::parse($item->date_reservation)->format(config('settings.datetime_display_format')) }}</small></p><p>
                </p></td>
                <td>
                    <p><small class="text-muted">{{ $item->comp }}</small></p><p>
                </p></td>
            
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
                        <span class="badge badge-danger badge-pill" style="font-size: 10px">@money( $item->total, config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                    @else
                        No hay acciones...
                    @endif
                </td>
                <td>
                    @if($item->reservation_status==1)
                        <span class="badge badge-info badge-pill">En proceso</span>
                    @elseif($item->reservation_status==2)
                        <span class="badge badge-danger badge-pill">Rechazada</span>
                    @elseif($item->reservation_status==3)
                        <span class="badge badge-success badge-pill">Aprobada</span>
                    @elseif($item->reservation_status==0)
                    <span class="badge badge-muted badge-pill">Realizada por el restaurante</span>
                @endif
                   
                </td>
    
                
            </tr>
        @endforeach

        
        

    @endif

    

