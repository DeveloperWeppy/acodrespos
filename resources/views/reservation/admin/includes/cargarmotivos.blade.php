<div class="row" id="div_cargar_motivos">
    @if (count($motivos) == 0)
        <div class="col-sm-12">
            <p class="text-center">¡No hay motivos de reservas registrados!</p>
        </div>
    @else
        @foreach ($motivos as $item)
            {{-- <div class="row"> --}}
                <div class="col-sm-3 col-12">
                    <p>{{$item->name}}</p>
                </div>
                <div class="col-sm-6 col-12">
                    <p>{{$item->description}}</p>
                </div>
                <div class="col-sm-3 col-12">
                    <p>{{$item->price}}</p>
                </div>
            {{-- </div> --}}
        @endforeach
    @endif


</div>
