<div class="card card-profile shadow">
    <div class="card-header">
        <h5 class="h3 mb-0">{{ ucfirst(config('settings.url_route'))." ".__("Location")}}</h5>
    </div>
    <div class="card-body">
        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-toggle="tab" href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">{{ __('Location') }}</a>
                </li>
                @if(config('app.isft'))
                    @if ($restorant->can_deliver == 1)
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-toggle="tab" href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false">{{ __('Delivery Area') }}</a>
                        </li>
                    @endif
                @endif
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-3-tab" data-toggle="tab" href="#tabs-icons-text-3" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">Areas de entrega</a>
                </li>
            </ul>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                        <div id="map_location" class="form-control form-control-alternative"></div>
                    </div>
                    <div class="tab-pane fade" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                            <div id="map_area" class="form-control form-control-alternative"></div>
                            <br/>
                            <button type="button" id="clear_area" class="btn btn-danger btn-sm btn-block">{{ __("Clear Delivery Area")}}</button>
                    </div>
                    <div class="tab-pane fade"  id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
                             <div id="map_area2" class="form-control form-control-alternative" style="height: 500px !important;"></div>
                             <br/>
                            <button type="button" id="btnerase" class="btn btn-danger btn-sm btn-block" style="display:none">Borrar</button>
                            <button type="button" id="btnsave" class="btn btn-success btn-sm btn-block" style="display:none">Guardar</button>
                            <button type="button" id="btncancel" class="btn btn-secondary btn-sm btn-block" style="display:none">Cancelar</button>
                            <div class="table-responsive card" style="margin-top:50px;">
                                <table class="table align-items-center table-flush">
                                    <thead class="thead-light" >
                                            <tr>
                                                <th scope="col">Nombre</th>
                                                <th scope="col">Valor de envio</th>
                                                <th scope="col">Color</th>
                                                <th scope="col">Estado</th>
                                                <th scope="col">Fecha de creación</th>
                                                <th scope="col" style="display: flex;align-items: center;"> Acciones  <i class="fa fa-plus-circle" id="btnaddzone" style="font-size:25px;margin-left: 10px;color:#2dce89;"></i></th>
                                            </tr>
                                    </thead>
                                    <tbody id="tgeozone">
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                   
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal-edit-zone" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form class="modal-content" id="fzone">
      <div class="modal-header">
        <h5 class="modal-title" id="ftitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="form-group">
                <label for="exampleInputEmail1">Nombre de zona</label>
                <input type="text" class="form-control" id="fzone-name" aria-describedby="emailHelp" placeholder="Nombre" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Color</label>
                <input  type="color" class="form-control" id="fzone-color" aria-describedby="emailHelp" placeholder="Nombre" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Valor de envio</label>
                <input  type="number" class="form-control" id="fzone-valor" aria-describedby="emailHelp" placeholder="valor de envio" required>
            </div>
            <div class="form-group">
                <label  for="fzone-status">Estado</label>
                <select class="form-control noselecttwo" id="fzone-status">
                     <option value="1" >Habilitado</option>
                     <option value="0" >Deshabilitado</option>
                </select>
            </div>
      </div>
      <div class="modal-footer">
        <button type="submit" id="btn-edit-zone" class="btn btn-primary">Modificar</button>
       
      </div>
    </form>
  </div>
</div>