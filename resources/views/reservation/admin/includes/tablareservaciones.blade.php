
    @if (count($reservaciones) == 0)
        <div class="col-sm-12">
            <p class="text-center">¡No hay reservas registradas!</p>
        </div>
    @else

    

        @foreach ($reservaciones as $item)

            <tr>
                <td>
                    <a class="btn badge badge-success badge-pill" href="http://www.testpost.com/orders/776">#{{$item->id}}</a>
                </td>
                
                <td class="table-web">
                    {{$item->client_id}}
                </td>
                
                <td class="table-web">
                    <span class="badge badge-primary badge-pill">{{$item->table_id}}</span>
                </td>
                <td>
                    <span class="badge badge-success badge-pill">{{$item->reservation_reason_id}}</span>
                </td>
                
                <td class="table-web">
                    {{$item->price}}
                </td>
                <td class="table-web">
                    {{$item->price}}
                </td>
    
                <td>
                    <p><small class="text-muted">{{$item->last_status}}</small></p><p>
                </p></td>
            </tr>
        @endforeach

        
        

    @endif

    

