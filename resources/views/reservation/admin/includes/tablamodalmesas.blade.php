
    @if (count($mesas) == 0)

    @else
        @foreach ($mesas as $item)
                <div class="col">
                    <p class="h3">
                        <button class="btn btn-icon btn-1 btn-sm btn-primary mostrarMesas mr-2" type="button" data-id="2">
                            <span class="btn-inner--icon"><i class="fa fa-cutlery"></i></span>
                        </button>
                          {{$item->area}}  - {{$item->name}}</p>
                </div>
        @endforeach
    @endif

    

