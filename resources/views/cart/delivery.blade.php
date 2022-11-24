<div class="card card-profile shadow">
    <div class="px-4">
      <div class="mt-5">
        <h3>{{ __('Delivery / Pickup') }}<span class="font-weight-light"></span></h3>
      </div>
      <div class="card-content border-top">
        <br />

        <div class="custom-control custom-radio mb-3">
           @if ($ifAreaDelivery)
             <input name="deliveryType" class="custom-control-input" id="deliveryTypeDeliver" type="radio" value="delivery" checked>
             <label class="custom-control-label" for="deliveryTypeDeliver">{{ __('Delivery') }}</label>
           @else
           <input name="deliveryType" class="custom-control-input" id="deliveryTypeDeliver" type="radio" value="delivery" disabled>
           <label class="custom-control-label" for="deliveryTypeDeliver">{{ __('Delivery') }} ,el restaurante no tiene áreas de entrega creadas</label>
          @endif
       
          
        </div>
        <div class="custom-control custom-radio mb-3">
           @if ($ifAreaDelivery)
             <input name="deliveryType" class="custom-control-input" id="deliveryTypePickup" type="radio" value="pickup"  >
           @else
            <input name="deliveryType" class="custom-control-input" id="deliveryTypePickup" type="radio" value="pickup"  checked>
            @endif
         
          <label class="custom-control-label" for="deliveryTypePickup">{{ __('Pickup') }}</label>
        </div>

      </div>
      <br />
      <br />
    </div>
  </div>
  <br />
