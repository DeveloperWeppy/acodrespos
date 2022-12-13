<div class="row">
    <div class="col-sm-12 col-md-12">
        <h3 class="text-center">Necesitas Ayuda con...</h3>

        <div class="form-group " id="toggler">
            <div class="text-center">
                <div class="btn-group btn-group-toggle  " data-toggle="buttons" role="group">
                    <label class="btn btn-outline-primary rounded m-2 p-3" data-target="#pedidoCollapse" data-toggle="collapse" aria-controls="pedidoCollapse">
                        <input type="radio" name="plan" value="1" required>
                        <img src="{{ asset('images/default/pedido-online.png') }}" alt="">
    
                        <p class="display-4 text-capitalize">
                            Un Pedido
                        </p>
                    </label>
                    <label class="btn btn-outline-primary rounded m-2 p-3" data-target="#informacionCollapse" data-toggle="collapse" aria-controls="informacionCollapse">
                        <input type="radio" name="plan" value="2" required>
                        <img src="{{ asset('images/default/informacion.png') }}" alt="">
    
                        <p class="display-4 text-capitalize">
                            Información
                        </p>
                    </label>
                </div>
            </div>
            

            <div id="pedidoCollapse" class="collapse" data-parent="#toggler">
                @include('pqrs.includes.pqrclient_pedido')
            </div>
            <div id="informacionCollapse" class="collapse" data-parent="#toggler">
                @include('pqrs.includes.pqrclient_nologin')
            </div>
        </div>
    </div>
</div>
