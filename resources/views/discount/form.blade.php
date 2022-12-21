<div class="row">
    @if(isset($coupon))
        <div class="col-md-3">
            @include('partials.input',['class'=>"col-12", 'ftype'=>'input','name'=>"Code",'id'=>"code",'placeholder'=>"",'required'=>true, 'value'=>$coupon->code])
        </div>
    @endif
    <div class="col-md-3">
        @include('partials.input',['class'=>"col-12", 'ftype'=>'input','name'=>"Name",'id'=>"name",'placeholder'=>"Introduzca el código",'required'=>true, 'value'=>isset($coupon)&&$coupon->name?$coupon->name:""])
    </div>
    <div class="col-md-3">
        @if(isset($coupon))
            @include('partials.select', ['class'=>"col-12",'name'=>"Type",'id'=>"type",'placeholder'=>"Tipo de cupón",'data'=>['Precio Fijo', 'Porcentaje'],'required'=>true, 'value'=>$coupon->type])
        @else
            @include('partials.select', ['class'=>"col-12",'name'=>"Type",'id'=>"type",'placeholder'=>"Tipo de cupón",'data'=>['Precio Fijo', 'Porcentaje'],'required'=>true])
        @endif
    </div>
    <div class="col-md-3">
        @if(isset($coupon) && $coupon->type == 0)
            @include('partials.input',['class'=>"col-12", 'type'=>'number', 'name'=>"Price",'id'=>"price_fixed",'placeholder'=>"Ingrese el precio",'required'=>false, 'additionalInfo'=>'Precio en  '.config('settings.cashier_currency'), 'value'=>$coupon->price])
            @include('partials.input',['class'=>"col-12", 'type'=>'number', 'min'=>'1', 'max'=>'100', 'name'=>"Price",'id'=>"price_percentage",'placeholder'=>"Ingrese el porcentaje",'required'=>false, 'additionalInfo'=>'Valor porcentual', 'value'=>$coupon->price])
        @elseif(isset($coupon) && $coupon->type == 1)
        @include('partials.input',['class'=>"col-12", 'type'=>'number', 'name'=>"Price",'id'=>"price_fixed",'placeholder'=>"Ingrese el precio",'required'=>false, 'additionalInfo'=>'Precio en '.config('settings.cashier_currency'), 'value'=>$coupon->price])
            @include('partials.input',['class'=>"col-12", 'type'=>'number', 'min'=>'1', 'max'=>'100', 'name'=>"Price",'id'=>"price_percentage",'placeholder'=>"Ingrese el porcentaje",'required'=>false, 'additionalInfo'=>'Valor porcentual','value'=>$coupon->price])
        @else
            @include('partials.input',['class'=>"col-12", 'type'=>'number', 'name'=>"Price",'id'=>"price_fixed",'placeholder'=>"Ingrese el precio",'required'=>false, 'additionalInfo'=>'Precio en  '.config('settings.cashier_currency')])
            @include('partials.input',['class'=>"col-12", 'type'=>'number', 'min'=>'1', 'max'=>'100', 'name'=>"Price",'id'=>"price_percentage",'placeholder'=>"Ingrese el porcentaje",'required'=>false, 'additionalInfo'=>'Valor porcentual'])
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="input-daterange datepicker row align-items-center" style="margin-left: 15px;">
           <div class="form-group">
                <label class="form-control-label">{{ __('Active from') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                    </div>
                    @if(isset($coupon))
                        <input name="active_from" class="form-control" placeholder="{{ __('Active from') }}" value="{{ old('active_from', $coupon->active_from) }}" type="text">
                    @else
                        <input name="active_from" class="form-control" placeholder="{{ __('Active from') }}" type="text">
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="input-daterange datepicker row align-items-center" style="margin-left: 15px;">
           <div class="form-group">
                <label class="form-control-label">{{ __('Active to') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                    </div>
                    @if(isset($coupon))
                        <input name="active_to" class="form-control" placeholder="{{ __('Active to') }}" value="{{ old('active_to', $coupon->active_to) }}" type="text">
                    @else
                        <input name="active_to" class="form-control" placeholder="{{ __('Active to') }}" type="text">
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        @if(isset($coupon))
            @include('partials.input',['class'=>"col-12", 'type'=>'number','name'=>"Número límite",'id'=>"limit_to_num_uses",'placeholder'=>"Número límite",'required'=>true, 'value'=>$coupon->limit_to_num_uses, 'step'=>1])
        @else
            @include('partials.input',['class'=>"col-12", 'type'=>'number','name'=>"Número límite",'id'=>"limit_to_num_uses",'placeholder'=>"Número límite",'required'=>true, 'step'=>1])
        @endif
    </div>
</div>
