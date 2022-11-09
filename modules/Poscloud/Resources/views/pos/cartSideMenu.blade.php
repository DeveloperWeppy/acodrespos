<div id="c" class="mt-4">
    <div class="offcanvas-menu-inner">
        <div class="minicart-content">
            <div class=" minicart-heading ">
                    <span id="orderNumber"></span>
                    <span id="orderId"></span>
                    <h4 id="tableName"></h4>
                    <input type="hidden" id="mesaid">
                </div>
            
            
     

            
            <!-- Order -->
            <div class="searchable-container">
                <div id="cartList">
                    <ul class="list-group items" v-for="item in items">
                        <li v-cloak v-bind:class="[item.order_has_items_id==0 ? 'cardAdd': 'border-0',' list-group-item d-flex p-2 mb-2 bg-gray-100 border-radius-lg']"  :data="item.order_has_items_id">
                            <div class="d-flex flex-column">
                              <h6 class="mb-3 text-sm">@{{ item.name }}  </h6>
                              <span class="mb-2 text-xs">{{ __('Price') }}: <span class="text-dark font-weight-bold ms-2">@{{ item.attributes.friendly_price }}</span></span>
                              <span class="mb-2 text-xs">{{ __('QTY') }}: <span class="text-dark ms-2 font-weight-bold">@{{ item.quantity }}</span></span>
                              <span class="mb-2 text-xs">Observacion: <span class="text-dark ms-2 font-weight-bold">@{{ item.observacion }}</span></span>
                              <span class="mb-2 text-xs" id="" class="personitem">Persona: <span class="text-dark ms-2 font-weight-bold">@{{ item.personaccount }}</span></span>
                            </div>
                            <div class="ms-auto" v-show="item.status_id<3 || item.status_id == null">
                                <button v-if="item.quantity==1" type="button" v-on:click="remove(item.id)"  :value="item.id" class="btn btn-outline-primary btn-icon btn-sm page-link btn-cart-radius">
                                    <span class="btn-inner--icon btn-cart-icon"><i class="fa fa-trash"></i></span>
                                </button>
                                <button v-if="item.quantity!=1" type="button" v-on:click="decQuantity(item.id)" :value="item.id" class="btn btn-outline-primary btn-icon btn-sm page-link btn-cart-radius">
                                    <span class="btn-inner--icon btn-cart-icon"><i class="fa fa-minus"></i></span>
                                </button>
                                <button type="button" v-on:click="incQuantity(item.id)" :value="item.id" class="btn btn-outline-primary btn-icon btn-sm page-link btn-cart-radius">
                                    <span class="btn-inner--icon btn-cart-icon"><i class="fa fa-plus"></i></span>
                                </button>
                                <button type="button" v-on:click="modalObserv(item.id,item.name,item.observacion)" :value="item.id" class="btn btn-outline-primary btn-icon btn-sm page-link btn-cart-radius">
                                    <span class="btn-inner--icon btn-cart-icon"><i class="fa fa-eye"></i></span>
                                </button>
                            </div>
                          </li>
                      </ul>
                </div>
                    
                
            </div>

            
            
            <!-- Client Card -->
            @include('poscloud::pos.expedition')
            <!-- End client cart -->
            <div class="card card-profile shadow mt-3 mb-3" >
                <div class="px-4" style="display:none" > 
                    <div class="card-content  " >  
                            <label style="margin-top:20px">Comentarios</label>
                            <div class="input-group mb-3">
                              <textarea id="order_comment" name="order_comment" rows="3" cols="40" class="form-control"></textarea>
                            </div>   
                    </div>  
                </div>  
            </div>
            <br />

            <!-- mostrando división de cuentas -->
            <div class="card card-stats shadow mb-3" id="card_division_personas">
                <div class="card-body">
                    <div id="cartListPerson">
                        <ul class="list-group items" >
                            <li v-cloak  v-for="item in items" id="itemspersonas">
                                <div class="d-flex flex-column">
                                <span class="mb-2 text-xs" id="" class="personitem">Persona: 
                                    <span class="text-dark ms-2 font-weight-bold">@{{ item.nombre }}
                                        <span class="d-flex justify-content-end font-weight-bold">$@{{ item.saldo }}</span>
                                    </span>
                                    
                                </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="totalPrices" v-cloak>
                <div  class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span v-if="totalPrice==0">{{ __('Cart is empty') }}!</span>
                                <span v-if="totalPrice"><strong>{{ __('Subtotal') }}:</strong></span>
                                <span v-if="totalPrice" class="ammount"><strong>@{{ totalPriceFormat }}</strong></span>
                            </div>
                        </div>

                        <div class="row" v-if="delivery">
                            <div class="col">
                                <span v-if="totalPrice"><strong>{{ __('Delivery') }}:</strong></span>
                                <span v-if="totalPrice" class="ammount"><strong>@{{ deliveryPriceFormated }}</strong></span>
                            </div>
                        </div>
                        <div class="row" v-if="deduct">
                            <div class="col">
                                <span v-if="deduct"><strong>{{ __('Applied discount') }}:</strong></span>
                                <span v-if="deduct" class="ammount"><strong>@{{ deductFormat }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div v-if="totalPrice" v-cloak class="card-body">
                        <div class="row">
                            <div class="col">
                                <span v-if="totalPrice"><strong>{{ __('Total') }}:</strong></span>
                                <span v-if="totalPrice" class="ammount"><strong>@{{ withDeliveryFormat }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
                <br/>
                
                
                <div v-if="totalPrice" v-cloak>
                    <div v-if="totalPrice" v-cloak>

                        @if(in_array("coupons", config('global.modules',[])))
                            <!-- Coupon CODE -->
                            <div  class="card card-stats p-2 mb-0">
                                <div  class="row mt-3">
                                    <div class="col-md-7">
                                        <input  id="coupon_code" name="coupon_code" type="text" class="form-control form-control-alternative" placeholder="{{ __('Discount coupon')}}">
                                    </div>
                                    <div class="col-md-5">
                                        <button onclick="applyDiscount()" id="promo_code_btn" type="button" class="btn btn-outline-primary w-100">{{ __('Apply') }}</button>
                                        <span><i id="promo_code_succ" class="ni ni-check-bold text-success"></i></span>
                                        <span><i id="promo_code_war" class="ni ni-fat-remove text-danger"></i></span>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <!-- End Cooupo Code -->
                        @endif
                        <div  class="row" style="display:flex;justify-content: space-around;">
                            <button id='createOrder' onclick="submitOrderPOS(1)" type="button" style="padding:10px;display:none" class="col-5 btn btn-lg  btn-primary text-white ocultarBtn">Crear Pedido</button>
                            <button id='actualizarPedido' onclick="submitOrderPOS(2)" type="button" style="padding:10px;display:none" class="col-5 btn btn-lg  btn-primary text-white ">Actualizar pedido</button>
                            <button id='dineincheckout' onclick="ocultarbtn()" type="button" style="padding:10px"   class="col-5 btn btn-lg btn-primary text-white" data-bs-toggle="modal" data-bs-target="#modalPayment">Pagar</button>
   
                        </div>
                    </div>
                </div>

                
            
               
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalObservacion" tabindex="-1" role="dialog" aria-labelledby="modalObservacion" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModalOb">Observacion</h5>
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close" style="color: black;">
           <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
            <textarea id="item_observacion" name="item_observacion" rows="3" cols="40" class="form-control"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="updataObser()" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
