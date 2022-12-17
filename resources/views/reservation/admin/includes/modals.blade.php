<div class="modal fade" id="modal-payment-reservation" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title-new-item">Pagar reservación</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col">
                                    <form role="form text-left">
                                        <label>{{ __('Payment method') }}</label>
                                        <div class="input-group mb-3">
                                            <select v-on:change="change($event)"  class="form-control noselecttwo" id="paymentType" >
                                                <option value="cash">{{ __('Cash') }}</option>
                                                {{-- <option value="cardterminal">{{ __('Card terminal') }}</option>
                                                <option value="onlinepayments">{{ __('Online payments') }}</option> --}}
                                                <option value="cardterminal">{{ __('Datáfono') }}</option>
                                                <option value="transferencia">{{ __('Transferencia') }}</option>
                                            </select>
                                        </div>
                                        <div id="selecuenta" style="display: none;">
                                            <label for="">Seleccione la cuenta</label>
                                            <div class="input-group mb-3">
                                                <select class="form-control noselecttwo"  id="paymentId" >
                                                    <option value="">Seleccionar</option>
                                                    @foreach ($configaccountsbanks as $item)
                                                        <option value="{{$item->id}}">{{ $item->name_bank . " - ". $item->number_account}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="selecuenta2" style="display: none;">
                                            <label for="">Seleccione tipo de tarjeta</label>
                                            <div class="input-group mb-3">
                                                <select class="form-control noselecttwo"  id="paymentType2" >
                                                    <option value="">Seleccionar</option>
                                                    <option value="Credito">Credito</option>
                                                    <option value="Debito">Debito</option>
                                                </select>
                                            </div>
                                            <label for="">Seleccione franquicia</label>
                                            <div class="input-group mb-3">
                                                <select class="form-control noselecttwo"  id="franquicia" >
                                                    <option value="">Seleccionar</option>
                                                    <option value="American">American</option>
                                                    <option value="Dinners">Dinners</option>
                                                    <option value="Mastercard">Mastercard</option>
                                                    <option value="Visa">Visa</option>
                                                </select>
                                            </div>
                                            <label for="">Comprobante Voucher</label>
                                            <input type="text" placeholder="" class="form-control" id="voucher">
                                        </div>
                                        <div id="seletipocuenta" style="display: none;">
                                            <label for="">Seleccione el tipo de Cuenta</label>
                                            <div class="input-group mb-3">
                                                <select class="form-control noselecttwo" >
                                                    <option value="ahorros">{{ __('Ahorros') }}</option>
                                                    <option value="corriente">{{ __('Corriente') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        @if(isset($restaurantConfig[0]->percentage_payment) && $restaurantConfig[0]->percentage_payment!=0)
                                        <div class="custom-control custom-control-alternative custom-checkbox ckpropina mt-3">
                                            <input class="custom-control-input" name="check_por" value="1" id="check_por" type="checkbox">
                                            <label class="custom-control-label" for="check_por">
                                                <span class="" id="span_propina">Pagar porcentaje</span>
                                            </label>
                                            <br>
                                            <span class="text-secundary mt-2" style="font-size: 11px;display: block;line-height: 17px;">(Al pagar el porcentaje aceptas pagar el {{$restaurantConfig[0]->percentage_payment}}% y el resto en en el restaurante.)</span>
                                        </div>
                                        @endif

                                        <label class="mt-3">{{ __('Total') }}</label>
                                        <div class="totalreserva">
                                            <p class="h1">@{{priceReservationFormated}} COP</p>
                                        </div>
                                        
                                    <div id="divPercentage" style="display: none">
                                        <label class="mt-2">Pendiente a pagar en el restaurante</label>
                                        <div class="totalreserva">
                                            <p class="h1"><span id="resRes"></span> COP</p>
                                        </div>
                                    </div>
            
            
                                    </form>
                                </div>
                                <div class="col">
                                    <form role="form text-left" class="">
                                        <label>{{ __('Received ammount')}}</label>
                                        <div class="input-group mb-3">
                                            <input type="text" v-model="receivedFormated" v-on:keyup="show" class="form-control" placeholder="0" aria-label="o" autofocus >
                                        </div>
                                        <label>{{ __('Change') }}</label>
                                        <p class="h2 text-success">
                                            @{{totalCambioFormated}} COP
                                        </p>
            
                                        <label>{{ __('Remaining') }}</label>
                                        <p class="h2 text-danger">
                                            @{{totalPriceRestadoFormated}} COP
                                        </p>
                                    </form>
            
                                </div>
                            </div>

                            <form id="formImgPayment" >
                                @csrf
                                <div class="row">
                                    <div id="loadarchivo" style="display: none;" class="col">
                                        <label>{{ __('Cargar evidencia de pago') }}</label>
                                        <input class="form-control" type="file" accept="image/*" placeholder="Cargar recibo" name="img_payment" id="img_payment" required>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                    <div class="modal-footer" v-if="(totalPriceRestadoFormated==0)">
                        
                        <button type="button"  id="pagarReserva" onclick="pagarReserva()" class="btn bg-gradient-primary">
                            <span class="btn-inner--text">{{ __('Submit')}}</span>
                            <span class="btn-inner--icon"><i class="ni ni-curved-next"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="modal-reservation-mesas" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title-new-item">Mesas de la Reservación #<span id="numReservation"></span></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <div id="mesasReservacion">

                        </div>
                    </div>
                    <div class="modal-footer" >
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<!-- Modal -->
<div class="modal fade" id="modalRegister" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form class="modal-content" id="from-create-client">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Registrar Cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" >
          <div class="form-group" >
            <div class="input-group input-group-alternative mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text2"><i class="ni ni-hat-3"></i></span>
              </div>
              <input class="form-control" style="border: 0px;" placeholder="Nombre" type="text" name="name" value="" required="" autofocus="">
            </div>
          </div>
          <div class="form-group" >
            <div class="input-group input-group-alternative mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text2"><i class="far fa-id-badge"></i></span>
              </div>
              <input class="form-control" style="border: 0px;" id="fromDocCleint" minlength="7" maxlength="10" placeholder="Número de Identificación" type="text" name="number_identification" value="" required="" autofocus="">
            </div>
          </div>
          <div class="form-group" >
            <div class="input-group input-group-alternative mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text2"><i class="ni ni-email-83"></i></span>
              </div>
              <input class="form-control"  style="border: 0px;"  placeholder="Correo electrónico" type="email" name="email" value="" required="">
            </div>
          </div>
          <div class="form-group" >
            <div class="input-group input-group-alternative mb-3">
              <input class="form-control" id="formPhone"  placeholder="Teléfono" type="phone" name="phone" value="" required="">
            </div>
          </div>
        </div>
        <div class="modal-footer">
        <button type="subtmin" class="btn btn-primary">CREAR</button>
        </div>
      </form>
    </div>
  </div>
















