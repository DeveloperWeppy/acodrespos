@if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner') || auth()->user()->hasRole('manager_restorant') || auth()->user()->hasRole('kitchen') || auth()->user()->hasRole('staff'))
<?php
$lastStatusAlisas=$order->status->pluck('alias')->last();
?>
<div class="card-footer py-4">
    <h6 class="heading-small text-muted mb-4">{{ __('Actions') }}</h6   >
    <nav class="justify-content-end" aria-label="...">
        <!---- actions for admin ---->
    @if(auth()->user()->hasRole('admin') )
        <script>
            function setSelectedOrderId(id){
                $("#form-assing-driver").attr("action", "/updatestatus/delivered/"+id);
            }
        </script>
        @if($lastStatusAlisas == "just_created")
            <a href="{{ url('updatestatus/accepted_by_admin/'.$order->id) }}" class="btn btn-primary">{{ __('Accept') }}</a>
            <a href="{{ url('updatestatus/rejected_by_admin/'.$order->id) }}" class="btn btn-danger">{{ __('Reject') }}</a>
        
        @elseif($lastStatusAlisas == "accepted_by_restaurant"&&$order->delivery_method.""!="2" &&$order->delivery_method!=3  )
            <button type="button" class="btn btn-primary" onClick=(setSelectedOrderId({{ $order->id }}))  data-toggle="modal" data-target="#modal-asign-driver">{{ __('Assign to driver') }}</button>
        @elseif($lastStatusAlisas == "rejected_by_driver"&&$order->delivery_method.""!="2" &&$order->delivery_method!=3 )
            <button type="button" class="btn btn-primary" onClick=(setSelectedOrderId({{ $order->id }}))  data-toggle="modal" data-target="#modal-asign-driver">{{ __('Assign to driver') }}</button>
        @elseif($lastStatusAlisas == "prepared"&&$order->delivery_method.""!="2"&&$order->delivery_method!=3 &&$order->driver==null)
            <button type="button" class="btn btn-primary" onClick=(setSelectedOrderId({{ $order->id }}))  data-toggle="modal" data-target="#modal-asign-driver">{{ __('Assign to driver') }}</button>
        @else
            <p>{{ __('No actions for you right now!') }}</p>
       @endif
    @endif
    <!---- actions for owner or driver ---->
    @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('manager_restorant') || auth()->user()->hasRole('staff'))
        @include('orders.partials.actions.actions')
    @endif

    <!---- actions for kitchen ---->
    @if(auth()->user()->hasRole('kitchen') && $lastStatusAlisas == "accepted_by_admin")
        <p>{{ __('Esperando que restaurante acepte el pedido!') }}</p>
    @elseif(auth()->user()->hasRole('kitchen') && $lastStatusAlisas == "accepted_by_restaurant")
        @include('orders.partials.actions.actions')
    @elseif(auth()->user()->hasRole('kitchen') && $lastStatusAlisas == "prepared")
        <button type="button" class="btn btn-primary" style="cursor: default">El pedido ha sido preparado</button>
        @elseif(auth()->user()->hasRole('kitchen'))
        <p>{{ __('No actions for you right now!') }}</p>
    @endif
    </nav>
</div>
@endif
