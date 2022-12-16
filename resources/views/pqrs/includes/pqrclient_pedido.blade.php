<form enctype="multipart/form-data" autocomplete="off" id="register_pqr">
    @csrf
    <h6 class="heading-small text-muted mb-4">{{ __('REGISTRAR SOLICITUD') }}</h6>
    <p>Con el fin de dar oportuna respuesta a su solicitud es importante que completes los campos que están con
        <strong>(*)</strong> </p>
    <div class="pl-lg-4">
        <div class="form-group">
            <input type="hidden" name="name" value="{{auth()->user()->name}}">
            <input type="hidden" name="email" value="{{auth()->user()->email}}">
            <label class="form-control-label" for="select-option">{{ __('Seleccione la Orden con la que presentó problema') }}(*)</label>
            @foreach ($my_orders as $item)
                <div class="shadow-sm p-3 mb-2 bg-white rounded">
                    <div class="row align-items-center">
                        <div class="col-sm-3 d-flex align-items-center">
                            <div class="form-check"><input type="checkbox" name="n_order" id="" value="{{$item->id}}"
                                class="only-one h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top  ">
                            </div>
                            @if (!empty($item->detailrestorant->logo))
                                <img src="{{$item->detailrestorant->logo}}" class="rounded " alt="">
                            @else
                                <img src="{{asset('images/default/Imagen-No-Disponible.jpg')}}" width="60px" height="60px" class="rounded " alt="">
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <label for="" class="form-control-label">{{$item->detailrestorant->name}}</label>
                        </div>
                        <div class="col-sm-3">
                            <label for="" class="form-control-label">Factura: <strong>#{{$item->id}}</strong></label>
                            <label for="" class="form-control-label">Cantidad de Productos: <strong>{{count($item->items)}}</strong></label>
                        </div>
                        <div class="col-sm-3">
                            <label for="" class="form-control-label">Fecha del Pedido: <strong>{{$item->created_at}}</strong></label>
                        </div>
                    </div>                    
                </div>
            @endforeach
            <div class="shadow-sm p-3 mb-2 bg-white rounded">
                <div class="row align-items-center">
                    <div class="col-sm-3 d-flex">
                        <div class="form-check"><input type="checkbox" name="n_order" id="" value="otro"
                            class="only-one h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top  ">
                            <label for="terms" class="form-check-label text-gray-500">
                                &nbsp;&nbsp;
                                <strong>Otro Pedido</strong>
                            </label>
                        </div>
                    </div>
                </div>                    
            </div>
        </div>

        <div class="form-group" id="divnum_pedido" style="display: none;">
            <label class="form-control-label" for="num_order">{{ __('N° de Factura') }}* </label>
            <p><small>Si su PQRS está relacionado con una orden en especifico, indique el número de la orden.</small></p>
            <input type="text" name="num_order" id="num_order" class="form-control form-control-alternative">
        </div>
        
        <div class="form-group">
            <label class="form-control-label" >{{ __('Phone') }}(*)</label>
            <input type="text" name="phone1"  id="telephone2"  class="form-control form-control-alternative" value="{{Auth::guest()?"":auth()->user()->phone}}" required style="padding-left:0">
        </div>
        
        <div class="form-group">
            <label class="form-control-label" for="exampleFormControlTextarea1">Objeto de su PQRSD(*)</label>
            <textarea class="form-control form-control-alternative" name="message" id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label class="form-control-label" for="input-file">{{ __('Subir evidencia') }}(opcional)</label>
            <p><small>Si su PQRS está relacionado con una orden en especifico, indique el número de la orden.</small>
            </p>
            <input type="file" name="evidence" id="input-file" class="form-control form-control-alternative"
                onchange="return validarExtensionArchivo()">
        </div>

        <div class="form-group text-center">
            <div class="form-check"><input type="checkbox" name="accept" id="termsCheckBox"
                    class="h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top  ">
                <label for="terms" class="form-check-label text-gray-500">
                    &nbsp;&nbsp;{{ __('Al hacer clic el botón enviar, usted acepta la remisión de la PQRS a Acodrés. Sus datos serán recolectados y tratados conforme con la ') }}
                    <strong>Política de
                        Tratamiento de Datos</strong>
                </label>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
        </div>
    </div>
</form>
