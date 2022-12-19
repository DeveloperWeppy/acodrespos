
    @if (count($solicitudes) == 0)
    @else

    

        @foreach ($solicitudes as $item)

            <tr>
                <td>
                    <a class="btn badge badge-success badge-pill" href="{{route('reservation.editsolicitud',[$item->id])}}">#{{$item->id}}</a>
                </td>
                <td>
                    <p><small class="text-muted">{{ Carbon\Carbon::parse($item->date_reservation)->format(config('settings.datetime_display_format')) }}</small></p><p>
                </p></td>
                
                <td class="table-web">
                    {{$item->cli}}
                </td>
                
                <td>
                    <span class="badge badge-success badge-pill">{{$item->mot}}</span>
                </td>
                
                <td class="table-web">
                    @money( $item->total, config('settings.cashier_currency'),config('settings.do_convertion'))
                
                </td>
                <td>

                    @if($item->reservation_status==1)
                        <span class="badge badge-info badge-pill">En proceso</span>
                    @elseif($item->reservation_status==2)
                        <span class="badge badge-danger badge-pill">Rechazada</span>
                    @elseif($item->reservation_status==3)
                        <span class="badge badge-success badge-pill">Aprobada</span>
                    @endif
                   
                </td>
    
                
            </tr>
        @endforeach

        
        

    @endif

    

