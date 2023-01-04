<div class="card card-profile shadow mt--300">
    <div class="px-4">
      <div class="mt-5">
        <h3>{{ __('Checkout') }}<span class="font-weight-light"></span></h3>
      </div>
      <div  class="border-top">
        <!-- Price overview -->
        <div id="totalPrices" v-cloak>
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span v-if="totalPrice==0">{{ __('Cart is empty') }}!</span>

                            <span v-if="totalPrice"><strong>{{ __('Subtotal') }}:</strong></span>
                            <span v-if="totalPrice" class="ammount"><strong>@{{ totalPriceFormat }}</strong></span>
                            @if(config('app.isft')||config('settings.is_whatsapp_ordering_mode')|| in_array("poscloud", config('global.modules',[])) || in_array("deliveryqr", config('global.modules',[])) )
                                <span v-if="totalPrice&&deliveryPrice>0"><br /><strong>{{ __('Delivery') }}:</strong></span>
                                <span v-if="totalPrice&&deliveryPrice>0" class="ammount"><strong>@{{ deliveryPriceFormated }}</strong></span><br />
                            @endif
                            <br />  
                            <div v-if="deduct"> 
                                <span class="text-danger" v-if="deduct">{{ __('Applied coupon discount') }}:</span>
                                <span class="text-danger" v-if="deduct" class="ammount">-@{{ deductFormat }}</span>
                                <br />  
                                <br />  
                            </div>
                           
                            <span v-if="totalPrice"><strong>{{ __('TOTAL') }}:</strong></span>
                            <span v-if="totalPrice" class="ammount"><strong>@{{ withDeliveryFormat   }}</strong></span>
                            <input v-if="totalPrice" type="hidden" id="tootalPricewithDeliveryRaw" :value="withDelivery" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End price overview -->

        @if(in_array("coupons", config('global.modules',[])))
            <!-- Coupons -->
            @include('cart.coupons')
            <!-- End coupons -->
        @endif


        <!-- Payment  Methods -->
        <div class="cards">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <!-- Errors on Stripe -->
                        @if (session('error'))
                            <div role="alert" class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if(!config('settings.is_whatsapp_ordering_mode'))
                        <!-- COD -->
                        @if (!config('settings.hide_cod'))
                            <div class="custom-control custom-radio mb-3">
                                <input name="paymentType" class="custom-control-input" id="cashOnDelivery" type="radio" value="cod" {{ config('settings.default_payment')=="cod"?"checked":""}}>
                                <label class="custom-control-label" for="cashOnDelivery"><span class="delTime">{{ config('app.isqrsaas')?__('Cash / Card Terminal'): __('Cash on delivery') }}</span> <span class="picTime">{{ __('Cash on pickup') }}</span></label>
                            </div>
                        @endif

                        @if($enablePayments)

                            <!-- STIPE CART -->
                            @if (config('settings.stripe_key')&&config('settings.enable_stripe'))
                                <div class="custom-control custom-radio mb-3">
                                    <input name="paymentType" class="custom-control-input" id="paymentStripe" type="radio" value="stripe" {{ config('settings.default_payment')=="stripe"?"checked":""}}>
                                    <label class="custom-control-label" for="paymentStripe">{{ __('Pay with card') }}</label>
                                </div>
                            @endif

                            <!-- Extra Payments ( Via module ) -->
                            @foreach ($extraPayments as $extraPayment)
                                @include($extraPayment.'::selector')
                            @endforeach

                            <div class="custom-control custom-radio mb-3">
                                <input name="paymentType" class="custom-control-input" id="paymentStripe" type="radio" value="transferencia" >
                                <label class="custom-control-label" for="paymentStripe">{{ __('Pago en Transferencia') }}</label>
                            </div>
                        @endif

                        @endif
                        <div class="alert alert-info infobanco" role="alert">
                            <strong>Nota!</strong> Por favor seleccione la cuenta a la que transferirá para que vea los detalles de la cuenta, una vez cancelado el pedido adjunte la evidencia de pago.
                        </div>
                        @if (!empty($configaccountsbanks))
                            <div class="row">
                                <div class="col-sm-12 infobanco">
                                    <label for="">Seleccione la cuenta</label>
                                    <div class="input-group mb-3">
                                        <select class="form-control noselecttwo" id="typeaccount" >
                                            <option value="seleccione">Seleccionar Cuenta</option>
                                            @foreach ($configaccountsbanks as $item)
                                                <option value="{{ $item->id}}" data-id="{{ $item->id}}">{{ $item->name_bank}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>                   
                            </div>
                                @include('restorants.partials.infoconfigcuenta')
                            <div class="row">
                                <div class="col-sm-12 infobanco">
                                    <div class="form-group{{ $errors->has('img_evidencia') ? ' has-danger' : '' }}">
                                    <label>{{ __('Cargar evidencia de pago') }}</label><br>
                                    <button type="button" id="btnselectfile" onclick="selectfileinput()" class="btn btn-primary" style="display: flex;align-items: center;"><span id="fnamefile">Selecciona una imagen  </span>  <i class="ni ni-cloud-upload-96"></i></button>
                                    
                                </div> 
                                @if ($errors->has('img_evidencia'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('img_evidencia') }}</strong>
                                    </span>
                                @endif
                                </div>
                            </div>
                        @else
                            <p>No hay cuentas de bancos para transferir...</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- END Payment -->

        <div class="text-center">
            <div class="custom-control custom-checkbox mb-3">
                <input class="custom-control-input" id="privacypolicy" type="checkbox">
                <!--<label class="custom-control-label" for="privacypolicy">{{ __('I agree to the Terms and Conditions and Privacy Policy') }}</label>-->
                <label class="custom-control-label" for="privacypolicy">
                    &nbsp;&nbsp;{{__('I agree to the')}}
                    <a href="{{config('settings.link_to_ts')}}" target="_blank" style="text-decoration: underline;">{{__('Terms of Service')}}</a> {{__('and')}}
                    <a href="{{config('settings.link_to_pr')}}" target="_blank" style="text-decoration: underline;">{{__('Privacy Policy')}}</a>.
                </label>
            </div>
        </div><br />
        <div class="text-center" id="totalSubmitCOD"  style="" >
            <button
                v-if="totalPrice"
                type="button"
                class="btn btn-success mt-4 paymentbutton"
                onclick="validarmetodopago()"
            >{{ __('Place order') }}</button>
        </div>
        <!-- Payment Actions -->
        @if(!config('settings.social_mode'))

            <!-- COD -->
            @include('cart.payments.cod')

            <!-- Extra Payments ( Via module ) -->
            @foreach ($extraPayments as $extraPayment)
                @include($extraPayment.'::button')
            @endforeach
            
            </form>

            <!-- Stripe -->
            @include('cart.payments.stripe')

            

        @elseif(config('settings.is_whatsapp_ordering_mode'))
            @include('cart.payments.whatsapp')
        @elseif(config('settings.is_facebook_ordering_mode'))
            @include('cart.payments.facebook')
        @endif
        <!-- END Payment Actions -->

        <br/>
        

      </div>
      <br />
      <br />
    </div>
  </div>

  @if(config('settings.is_demo') && config('settings.enable_stripe'))
    @include('cart.democards')
  @endif
