<div class="row" id="div_cargar_infocuenta">
    @if ($type == 'seleccione')
        <div class="col-sm-12">
            <p class="text-center">Ninguna cuenta seleccionada</p>
        </div>
    @else
        @if ($respuesconfigaccountsbanks)
            <div class="col-sm-6">
                <label for="">Banco</label>
                <p>{{ $respuesconfigaccountsbanks->name_bank }}</p>
            </div>
            <div class="col-sm-6">
                <label for="">Nombre de quien recibe</label>
                <p>{{ $respuesconfigaccountsbanks->name_receptor }}</p>
            </div>
            <div class="col-sm-6">
                <label for="">Tipo de Documento</label>
                <p>{{ $respuesconfigaccountsbanks->type_document }}</p>
            </div>
            <div class="col-sm-6">
                <label for="">Número de Documento</label>
                <p>{{ $respuesconfigaccountsbanks->number_document }}</p>
            </div>
            <div class="col-sm-6">
                <label for="">Tipo de Cuenta</label>
                <p>{{ $respuesconfigaccountsbanks->type_account }}</p>
            </div>
            <div class="col-sm-6">
                <label for="">Número de Cuenta</label>
                <p>{{ $respuesconfigaccountsbanks->number_account }}</p>
            </div>
        @else
            <div class="col-12">
                <div class="card pt-3 ps-3">
                    <p>No existe datos de cuenta!</p>
                </div>
            </div>
        @endif
    @endif


</div>
