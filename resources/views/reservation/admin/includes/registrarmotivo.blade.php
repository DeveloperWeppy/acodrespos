<div class="modal fade" id="modal-registrar-motivo" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal- modal-dialog-centered modal-" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title-new-item">{{ __('Import restaurants from CSV') }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <div class="col-md-12">
                        <form  method="post" id="register_motivo">
                            <div class="form-group">
                                <label class="form-control-label" for="name_motivo">Nombre del Motivo</label>
                                <input type="text" class="form-control" name="name_motivo" id="name_motivo" required>
                            </div>
                            <div class="form-group">
                                <label class="" for="description_motivo">Descripción del Motivo</label>
                                <textarea class="form-control" name="description_motivo" id="description_motivo" rows="5" required></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="price_motivo">Precio del Motivo</label>
                                <input type="number" class="form-control" name="price_motivo" id="price_motivo" required>
                            </div>
                            <input name="restaurant_id" id="restaurant_id" type="hidden" value="{{$compani->id}}" required>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary my-4" id="btn_save_motivo">{{ __('Save') }}</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>