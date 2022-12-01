<div class="card-body">
    <div class="timeline timeline-one-side" id="status-history" data-timeline-content="axis" data-timeline-axis-style="dashed">
    @foreach($order->stakeholders as $key=>$stakeholder)
        <div class="timeline-block">
            <span class="timeline-step badge-success">
                @if ($statuses[$stakeholder->pivot->status_id] == 'Just created')
                    <i class="ni ni-bell-55"></i>
                @elseif($statuses[$stakeholder->pivot->status_id] == 'Accepted by admin')
                    <i class="fas fa-check"></i>
                @elseif($statuses[$stakeholder->pivot->status_id] == 'Accepted by restaurant')
                    <i class="fas fa-check-double"></i>
                @elseif($statuses[$stakeholder->pivot->status_id] == 'Assigned to driver')
                    <i class="fas fa-route"></i>
                @elseif($statuses[$stakeholder->pivot->status_id] == 'Prepared')
                    <i class="fas fa-utensils"></i>
                @elseif($statuses[$stakeholder->pivot->status_id] == 'Delivered')
                <i class="fas fa-handshake"></i>
                @endif
                
            </span>
            <div class="timeline-content">
                <div class="d-flex justify-content-between pt-1">
                    <div>
                        <span class="text-muted text-sm font-weight-bold">
                            @if ($statuses[$stakeholder->pivot->status_id] == 'Assigned to driver')
                                Pedido en Camino
                            @else
                                {{ __($statuses[$stakeholder->pivot->status_id]) }}
                            @endif
                            
                        </span>
                    </div>
                    <div class="text-right">
                        <small class="text-muted"><i class="fas fa-clock mr-1"></i>{{ $stakeholder->pivot->created_at->locale(Config::get('app.locale'))->isoFormat('LLLL') }}</small>
                    </div>
                </div>
                <h6 class="text-sm mt-1 mb-0">{{ __('Status from') }}: {{$stakeholder->name }}</h6>
            </div>
        </div>
    @endforeach
    </div>
</div>