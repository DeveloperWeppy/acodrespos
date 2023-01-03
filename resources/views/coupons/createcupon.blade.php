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
                            <h3 class="mb-0">{{ __('New coupon') }}</h3>
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

                                        @include('partials.input',['class'=>"col-12 col-md-3", 'ftype'=>'input','name'=>"Name",'id'=>"name",'placeholder'=>"Nombre del cupón",'required'=>true, 'value'=>isset($coupon)&&$coupon->name?$coupon->name:""])

                                        @include('partials.input',['class'=>"col-12 col-md-3", 'ftype'=>'input','name'=>"Code",'id'=>"code",'placeholder'=>"Codigo del cupón",'required'=>true,'value'=>$codeCupon])
                                
                                        @include('partials.select', ['class'=>"col-12 col-md-3",'name'=>"Tipo de cupón",'id'=>"type",'placeholder'=>"Tipo de cupón",'data'=>['Precio Fijo', 'Porcentaje'],'required'=>true])

                                        @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'name'=>"Valor",'id'=>"price",'placeholder'=>"Ingrese el valor",'required'=>false, 'additionalInfo'=>''])
                                
                                </div>
                                <div class="row">
                                    <div class="form-group-date col-md-3">
                                        <div class="input-daterange datepicker align-items-center">
                                        <div class="form-group">
                                                <label class="form-control-label">{{ __('Active from') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                    </div>
                                                    <input id="active_from" name="active_from" class="form-control" placeholder="{{ __('Active from') }}" type="text" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group-date  col-md-3">
                                        <div class="input-daterange datepicker align-items-center">
                                        <div class="form-group">
                                                <label class="form-control-label">{{ __('Active to') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                    </div>
                                                    <input id="active_to"  name="active_to" class="form-control" placeholder="{{ __('Active to') }}" type="text" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                
                                    @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number','name'=>"límite uso",'id'=>"limit_to_num_uses",'placeholder'=>"Número límite",'required'=>true, 'step'=>1])

                                    @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number','name'=>"Redención por usuario",'id'=>"red",'placeholder'=>"Número límite",'required'=>true, 'step'=>1])
                            
                                    @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'name'=>"Valor mínimo en el carrito",'id'=>"min_price",'placeholder'=>"Ingrese el valor",'required'=>true, 'additionalInfo'=>''])

                                </div>
                                
                                <div class="row">

                                    @include('partials.bool',['class'=>"col-12", 'ftype'=>'input','name'=>"Cupón sin fecha límite",'id'=>"has_ilimited",'placeholder'=>"", 'value'=>'0',])
                                
                                    @include('partials.bool',['class'=>"col-12", 'ftype'=>'input','name'=>"Cupón Envio Gratis",'id'=>"has_free_delivery",'placeholder'=>"", 'value'=>"0",])
                                    
                                    @include('partials.bool',['class'=>"col-12", 'ftype'=>'input','name'=>"No plicar a productos en descuento",'id'=>"has_discount", 'value'=>"0", 'additionalInfo'=>'El cupon no se aplicará si hay productos en descuento en el carrito '])
                                
                                </div>

                                
                                
                            <div>
                                <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
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

        $('#form-group-price').hide();
     
        $('#type').on('change', function() {
            $('#form-group-price').show();
            $("#price").attr("required",true);
            if(this.value == 0){
                $("#price").attr("placeholder","Ingrese el valor COP");
                $("#price").removeAttr("min");
                $("#price").removeAttr("max");
            }else{
                $("#price").attr("placeholder","Ingrese el valor en porcentaje");
                $("#price").attr("min","0");
                $("#price").attr("max","100");
            }
        });

        $('#has_ilimited').change(function() {
            if(this.checked) {
                $('.form-group-date').hide();
                $("#active_from").attr("required",false);
                $("#active_to").attr("required",false);
            }else{
                $('.form-group-date').show();
                $("#active_from").attr("required",true);
                $("#active_to").attr("required",true);
            }     
        });

        $('#has_free_delivery').change(function() {
            if(this.checked) {
                $('#form-group-type').hide();
                $('#form-group-price').hide();
                $("#price").attr("required",false);
                $("#type").attr("required",false);
            }else{
                $('#form-group-type').show();
                $('#form-group-price').show();
                $("#price").attr("required",true);
                $("#type").attr("required",true);
            }     
        });


    </script>
@endsection
