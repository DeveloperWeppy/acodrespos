<div class="card card-profile shadow mt-3 mb-3" id="expedition">
  
    <div class="px-4">
      
      <div class="card-content ">
        <br />

        <label style="width:100%;display: contents;"><span>{{ __('Client name') }}</span> <button style="float: right;" type="button" data-bs-toggle="modal" data-bs-target="#modalRegister" class="btnFormClient btn btn-outline-primary btn-icon btn-sm page-link btn-cart-radius"><span class="btn-inner--icon btn-cart-icon"><i aria-hidden="true" class="fa fa-plus"></i></span></button></label>
        <div class="input-group mb-3">
            <select :value="config.client_name" style="width:100%;" type="text" id="client_name" class="form-control"   required>
               
            </select>
        </div>

        <label>{{ __('Client phone') }}</label>
        <div class="input-group mb-3">
            <input  :value="config.client_phone" type="text" id="client_phone"  class="form-control" placeholder="{{ __('Client phone') }}" aria-label="phone">
        </div>

       
        
        <label>{{ __('Time') }}</label><br />
        <div class="input-group mb-3">
          <select name="timeslot" id="timeslot" class="form-control{{ $errors->has('timeslot') ? ' is-invalid' : '' }}" style="width: 100%" required>
            @foreach ($timeSlots as $value => $text)
                <option value={{ $value }}>{{$text}}</option>
            @endforeach
          </select>
        </div>

        <div id="client_address_fields">
          <label>{{ __('Client address') }}</label>
          <div class="input-group mb-3">
           
              <input :value="config.client_address" type="text" id="client_address" class="form-control" placeholder="{{ __('Client address') }}">
          </div>
  
          <label>{{ __('Delivery area') }}</label>
          <div class="input-group mb-3">
            <select name="delivery_area" id="delivery_area" class="form-control{{ $errors->has('deliveryAreas') ? ' is-invalid' : '' }}" >
              <option  value="0">{{__('Select delivery area')}}</option>
            
              @foreach ($geoZoneDelivery as $simplearea)
              <option  value={{ $simplearea->id }}>{{ $simplearea->name }} @money($simplearea->price,config('settings.cashier_currency'),true)</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="input-group mb-3">
          <button onclick="updateExpeditionPOS()" class="btn btn-primary">{{__('Save')}}</button>
        </div>

       

      </div>
    </div>

</div>
<div class="modal fade" id="modalRegister" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form class="modal-content" id="from-create-client">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Registrar Cliente</h5>
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close" style="color: black;">
           <span aria-hidden="true">×</span>
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
            <div class="input-group-prepend">
              <span class="input-group-text2"><i class="fa fa-calendar"></i></span>
            </div>
            <input class="form-control"   style="border: 0px;" placeholder="Fecha de Nacimiento" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="birth_date" value="" required="">
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
