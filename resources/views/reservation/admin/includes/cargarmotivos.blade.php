
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
                <div class="col-sm-3 col-12">
                    <p>{{$item->description}}</p>
                </div>
                <div class="col-sm-3 col-12">
                    <p>{{$item->price}}</p>
                </div>
                <div class="col-sm-3 col-12">
                    <a href="" data="delivered" data-toggle="modal" data-id="{{$item}}" data-target="#modal-registrar-motivo" class="btn btn-sm  btn-primary editarMotivo">
                        <span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span>
                        Editar</a>
                    <a href="{{route('reservationreason.delete',[$item->id ])}}" data="rejected_by_restaurant" class="btn btn-sm  btn-danger">
                        <span class="btn-inner--icon"><i class="ni ni-fat-remove"></i></span>
                        Eliminar</a>
                </div>
            {{-- </div> --}}
        @endforeach
    @endif


