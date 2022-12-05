

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

                                        <div class="custom-control custom-control-alternative custom-checkbox ckpropina">
                                            <input class="custom-control-input" name="valor_propina" id="ask_propina_check" type="checkbox">
                                            <label class="custom-control-label" for="ask_propina_check">
                                                <span class="text-muted" id="span_propina">Pagar porcentaje</span>
                                            </label>
                                        </div>

                                        <label class="mt-3">{{ __('Total') }}</label>
                                        <p class="h2">@{{ totalPriceFormat }} </p>
            
            
                                    </form>
                                </div>
                                <div class="col">
                                    <form role="form text-left">
                                        <label>{{ __('Received ammount')}}</label>
                                        <div class="input-group mb-3">
                                            <input type="text" v-model="receivedFormated" v-on:keyup="show" class="form-control" placeholder="0" aria-label="o" autofocus >
                                        </div>
                                        <label>{{ __('Change') }}</label>
                                        <p class="h2 text-success">@{{ totalCambioFormated }}
                                        </p>
            
                                        <label>{{ __('Remaining') }}</label>
                                        <p class="h2 text-danger">@{{ totalPriceRestadoFormated }}
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
                    <div class="modal-footer" v-if="received-totalPrice>=0" v-cloak>

                        <i id="indicator" style="display: none" class="fas fa-spinner fa-spin"></i>
                        <button type="button" id="pagarReserva" onclick="pagarReserva()" class="btn bg-gradient-primary">
                            <span class="btn-inner--text">{{ __('Submit')}}</span>
                            <span class="btn-inner--icon"><i class="ni ni-curved-next"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




