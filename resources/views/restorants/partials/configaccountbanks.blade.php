<div class="modal fade" id="modal-register-account" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-modal-dialog-centered modal-" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title-new-item">{{ __('Agregar Nueva Cuenta') }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body py-lg-5">
                        <div class="col-md-10 offset-md-1">
                        <form role="form" method="post" action="{{ route('configuracioncuenta.store') }}" >
                            @csrf
                            <div class="row text-left">
                                <input type="hidden" id="rid" name="rid" value="{{ $restorant->id }}"/>
                                <div class="col-md-6">
                                    <label for="" class="form-control-label">Nombre de Receptor</label>
                                    <input type="text" name="name_receptor" class="form-control" id="" >
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="" class="form-control-label">Nombre de la entidad Bancaria</label>
                                    <input type="text" name="name_bank" class="form-control" id="" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="" class="form-control-label">Tipo de Documento</label>
                                    <select name="type_document" id="" class="form-control noselecttwo" style="width:100%;" required>
                                        <option value="cedula">Cédula de Ciudadanía</option>
                                        <option value="nit">NIT</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="" class="form-control-label">Número de Documento</label>
                                    <input type="number" name="number_document" class="form-control" id="" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="" class="form-control-label">Tipo de Cuenta</label>
                                    <select name="type_account" id="" class="form-control noselecttwo" style="width:100%;" required>
                                        <option value="ahorros">Ahorros</option>
                                        <option value="corriente">Corriente</option>
                                    </select>
                                </div>
    
                                <div class="col-md-6">
                                    <label for="" class="form-control-label">Número de Cuenta</label>
                                    <input type="number" name="number_account" class="form-control" id="" required>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4">{{ __('Save') }}</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>