@extends('layouts.app', ['title' => __('Coupon')])

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            @if(isset($coupon))
                                <h3 class="mb-0">{{ __('Edit coupon') }}</h3>
                            @else
                                <h3 class="mb-0">{{ __('New coupon') }}</h3>
                            @endif
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('admin.restaurant.coupons.index') }}" class="btn btn-sm btn-primary">{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="heading-small text-muted mb-4">{{ __('Coupon information') }}</h6>
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                    <div class="pl-lg-4">
                        
                        <form method="post" action="{{ route('admin.restaurant.coupons.store') }}" autocomplete="off" enctype="multipart/form-data">
                                    @csrf
                                
                                    <div class="row">
            
                
                                        @include('partials.input',['class'=>"col-12 col-md-3", 'ftype'=>'input','name'=>"Code",'id'=>"code",'placeholder'=>"",'required'=>true])
                                        
                                
                                
                                        @include('partials.input',['class'=>"col-12 col-md-3", 'ftype'=>'input','name'=>"Name",'id'=>"name",'placeholder'=>"Introduzca el código",'required'=>true, 'value'=>isset($coupon)&&$coupon->name?$coupon->name:""])
                                
                                
                                        @if(isset($coupon))
                                            @include('partials.select', ['class'=>"col-12 col-md-3",'name'=>"Tipo de cupón",'id'=>"type",'placeholder'=>"Tipo de cupón",'data'=>['Precio Fijo', 'Porcentaje'],'required'=>true, 'value'=>$coupon->type])
                                        @else
                                            @include('partials.select', ['class'=>"col-12 col-md-3",'name'=>"Tipo de cupón",'id'=>"type",'placeholder'=>"Tipo de cupón",'data'=>['Precio Fijo', 'Porcentaje'],'required'=>true])
                                        @endif
                                
                                
                                        @if(isset($coupon) && $coupon->type == 0)
                                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'name'=>"Valor",'id'=>"price_fixed",'placeholder'=>"Ingrese el precio",'required'=>false, 'additionalInfo'=>'Precio en  '.config('settings.cashier_currency'), 'value'=>$coupon->price])
                                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'min'=>'1', 'max'=>'100', 'name'=>"Valor",'id'=>"price_percentage",'placeholder'=>"Ingrese el porcentaje",'required'=>false, 'additionalInfo'=>'Valor porcentual', 'value'=>$coupon->price])
                                        @elseif(isset($coupon) && $coupon->type == 1)
                                        @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'name'=>"Valor",'id'=>"price_fixed",'placeholder'=>"Ingrese el precio",'required'=>false, 'additionalInfo'=>'Precio en '.config('settings.cashier_currency'), 'value'=>$coupon->price])
                                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'min'=>'1', 'max'=>'100', 'name'=>"Valor",'id'=>"price_percentage",'placeholder'=>"Ingrese el porcentaje",'required'=>false, 'additionalInfo'=>'Valor porcentual','value'=>$coupon->price])
                                        @else
                                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'name'=>"Valor",'id'=>"price_fixed",'placeholder'=>"Ingrese el precio",'required'=>false, 'additionalInfo'=>'Precio en  '.config('settings.cashier_currency')])
                                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'min'=>'1', 'max'=>'100', 'name'=>"Valor",'id'=>"price_percentage",'placeholder'=>"Ingrese el porcentaje",'required'=>false, 'additionalInfo'=>'Valor porcentual'])
                                        @endif
                                
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="input-daterange datepicker align-items-center">
                                        <div class="form-group">
                                                <label class="form-control-label">{{ __('Active from') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                    </div>
                                                    @if(isset($coupon))
                                                        <input name="active_from" class="form-control" placeholder="{{ __('Active from') }}" value="{{ old('active_from', $coupon->active_from) }}" type="text" required>
                                                    @else
                                                        <input name="active_from" class="form-control" placeholder="{{ __('Active from') }}" type="text" required>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-daterange datepicker align-items-center">
                                        <div class="form-group">
                                                <label class="form-control-label">{{ __('Active to') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                    </div>
                                                    @if(isset($coupon))
                                                        <input name="active_to" class="form-control" placeholder="{{ __('Active to') }}" value="{{ old('active_to', $coupon->active_to) }}" type="text" required>
                                                    @else
                                                        <input name="active_to" class="form-control" placeholder="{{ __('Active to') }}" type="text" required>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                
                                
                                
                                        @if(isset($coupon))
                                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number','name'=>"Número límite",'id'=>"limit_to_num_uses",'placeholder'=>"Número límite",'required'=>true, 'value'=>$coupon->limit_to_num_uses, 'step'=>1])
                                        @else
                                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number','name'=>"Número límite",'id'=>"limit_to_num_uses",'placeholder'=>"Número límite",'required'=>true, 'step'=>1])
                                        @endif
                                
                                        @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number','name'=>"Redención por usuario",'id'=>"red",'placeholder'=>"Número límite",'required'=>true, 'step'=>1])
                                
                                </div>
                                
                                <div class="row">
                                    @include('partials.bool',['class'=>"col-12", 'ftype'=>'input','name'=>"Cupón ilimitado",'id'=>"has_ilimited",'placeholder'=>"",'required'=>true, 'value'=>isset($coupon)&&$coupon->has_ilimited?$coupon->has_ilimited:"",])
                                
                                    @include('partials.bool',['class'=>"col-12", 'ftype'=>'input','name'=>"Cupón Envio Gratis",'id'=>"has_free_delivery",'placeholder'=>"",'required'=>true, 'value'=>isset($coupon)&&$coupon->has_free_delivery?$coupon->has_free_delivery:"",])
                                    
                                    @include('partials.bool',['class'=>"col-12 col-md-3", 'type'=>'number','name'=>"Aplicar a productos en descuento",'id'=>"has_descount",'placeholder'=>"Número límite",'required'=>true, 'step'=>1])
                                
                                </div>

                                
                                
                            <div>
                                @if(isset($coupon))
                                    <button type="submit" class="btn btn-primary mt-4">{{ __('Update')}}</button>
                                @else
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>

</div>
@endsection

@section('js')
    <script>
        "use strict";
        /*$('#type option').each(function() {
            if($(this).is(':selected')){
                alert($(this).value)
            }
        })*/

        var coupon = <?php if(isset($coupon)) { echo json_encode($coupon); } else { echo json_encode(null); } ?>;
        if(coupon != null){
            var coupon_type = coupon.type;
            if(coupon_type == 0){
                $('#form-group-price_fixed').show();

                $("#price_fixed").attr("required",true);
                $("#price_percentage").attr("required",false);
            }else{
                $('#form-group-price_percentage').show();

                $("#price_percentage").attr("required",true);
                $("#price_fixed").attr("required",false);
            }
        }

        $('#type').on('change', function() {
            if(this.value == 0){
                $("#price_percentage").attr("required",false);
                $('#form-group-price_percentage').hide();

                $('#form-group-price_fixed').show();
                $("#price_fixed").attr("required",true);

            }else{
                $('#form-group-price_fixed').hide();
                $("#price_fixed").attr("required",false);

                $('#form-group-price_percentage').show();
                $("#price_percentage").attr("required",true);
            }
        });
    </script>
@endsection
