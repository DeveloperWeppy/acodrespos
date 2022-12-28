@extends('layouts.app', ['title' => "Descuento"])

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
                                <h3 class="mb-0">Editar descuento</h3>
                            @else
                                <h3 class="mb-0">Crear descuento</h3>
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
                        <form method="post" id="formDiscount" autocomplete="off" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                     
                      
                        @include('partials.input',['class'=>"col-12 col-md-3", 'ftype'=>'input','name'=>"Name",'id'=>"name",'placeholder'=>"Digite el nombre",'required'=>true, 'value'=>isset($coupon)&&$coupon->name?$coupon->name:""])
                     
                        
                        @if(isset($coupon))
                            @include('partials.select', ['class'=>"col-12 col-md-3",'name'=>"Tipo de descuento",'id'=>"type",'placeholder'=>"Tipo de descuento",'data'=>['Precio Fijo', 'Porcentaje'],'required'=>true, 'value'=>$coupon->type])
                        @else
                            @include('partials.select', ['class'=>"col-12 col-md-3",'name'=>"Tipo de descuento",'id'=>"type",'placeholder'=>"Tipo de descuento",'data'=>['Precio Fijo', 'Porcentaje'],'required'=>true])
                        @endif
                        
                     
                        @if(isset($coupon) && $coupon->type == 0)
                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'name'=>"Price",'id'=>"price_fixed",'placeholder'=>"Ingrese el precio",'required'=>false, 'additionalInfo'=>'Precio en  '.config('settings.cashier_currency'), 'value'=>$coupon->price])
                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'min'=>'1', 'name'=>"Price",'id'=>"price_percentage",'placeholder'=>"Ingrese el porcentaje",'required'=>false, 'additionalInfo'=>'Valor porcentual', 'value'=>$coupon->price])
                        @elseif(isset($coupon) && $coupon->type == 1)
                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'name'=>"Price",'id'=>"price_fixed",'placeholder'=>"Ingrese el precio",'required'=>false, 'additionalInfo'=>'Precio en '.config('settings.cashier_currency'), 'value'=>$coupon->price])
                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'min'=>'1', 'name'=>"Price",'id'=>"price_percentage",'placeholder'=>"Ingrese el porcentaje",'required'=>false, 'additionalInfo'=>'Valor porcentual','value'=>$coupon->price])
                        @else
                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'name'=>"Price",'id'=>"price_fixed",'placeholder'=>"Ingrese el precio",'required'=>false, 'additionalInfo'=>'Precio en  '.config('settings.cashier_currency')])
                            @include('partials.input',['class'=>"col-12 col-md-3", 'type'=>'number', 'min'=>'1', 'name'=>"Price",'id'=>"price_percentage",'placeholder'=>"Ingrese el porcentaje",'required'=>false, 'additionalInfo'=>'Valor porcentual'])
                        @endif
                    
                    </div>
                    <div class="row">

                        <div class="col-12 col-md-3">
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-control-label">Hora</label>
                                <input name="hora1" class="form-control timepicker" placeholder="Hora" required type="text">
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-control-label">Hora</label>
                                <input name="hora2" class="form-control timepicker" placeholder="Hora" required type="text">
                            </div>
                        </div>

                        


                    </div>

                    <div class="row">

                        <div class="col-12 col-md-3">
                            <div class="form-group">
                            <label class="form-control-label">Aplicar por</label>
                            <select class="form-control col-sm" id="typ2" name="typ2">
                                <option disabled value> Seleccionar producto</option>
                                <option value="0" {{(isset($coupon)?($coupon->opcion_discount=="0"?"selected":""):"")}}>Todos los productos</option>
                                <option value="1" {{(isset($coupon)?($coupon->opcion_discount=="1"?"selected":""):"")}}>Productos especificos</option>
                                <option value="2" {{(isset($coupon)?($coupon->opcion_discount=="2"?"selected":""):"")}}>Categorias</option>
                            </select>
                        </div>
                        </div>

                    <div class="col-md-12"   id="prod" hidden >
                     
                        <div id="form-group-name" class="form-group">
                            <label class="form-control-label">Productos</label>
                                <select class="form-control col-sm" name="prod[]" multiple>
                                    <option disabled value> Seleccionar producto</option>
                                    @foreach ($productos as $key)
                                        @if (isset($select['value'])&&$key==$select['value'])
                                            <option value="{{ $key }}" selected>{{$item }}</option>
                                        @else
                                            <option value="{{ $key['id'] }}">{{$key['name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                        </div>
                    </div>

                    <div class="col-md-12" id="catt" hidden>

                     
                        <div id="form-group-name" class="form-group">
                    

                        <label class="form-control-label">Categorias</label>
                        <select class="form-control col-sm" id="cat" name="catt[]" multiple>
                            <option disabled value> Seleccionar producto</option>
                            @foreach ($categorias as $key)
                                @if (isset($select['value'])&&$key==$select['value'])
                                    <option value="{{ $key }}" selected>{{$item }}</option>
                                @else
                                    <option value="{{ $key->id }}">{{$key->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    </div>

                    </div>

                            <div>
                                <button type="submit" class="btn btn-success mt-4 submitForm">{{ __('Save') }}</button>
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
    

$('.timepicker').timepicker({
    timeFormat: 'h:mm p',
    interval: 60,
    dynamic: false,
    dropdown: true,
    scrollbar: true,
});


 	

$( "#formDiscount" ).submit(function( event ) {

        event.preventDefault();

        Swal.fire({
            title: 'Aplicar el descuento',
            text: 'El descuento se aplicara a todos los productos relacionados\n¿aplicar el descuento?',
            showDenyButton: true,
            showCancelButton: true,
            showDenyButton:false,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                enviarForm();
            } else if (result.isDenied) {
                return false;
            }
        });
});

function enviarForm(){
    var formData = new FormData($( "#formDiscount" )[0]);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('admin.restaurant.coupons.storeDiscount') }}",
        type: 'POST',
        success: function (data) {
            Swal.fire({
                title: "Datos Guardados",
                text: '',
                icon: 'success',
            }).then(function() {
                $(location).attr('href','/coupons');
            });
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
}

$(function(){
    $('.select2').css('height','auto');
});
 	

$( "#typ2" ).change(function() {
    mostrarMultiple();
});

mostrarMultiple();
function mostrarMultiple(){
    if($( "#typ2" ).val()=="0"){
        $("#prod").attr('hidden',true);
        $("#catt").attr('hidden',true);
    }
    if($( "#typ2" ).val()=="1"){
        $("#prod").attr('hidden',false);
        $("#catt").attr('hidden',true);
    }
    if($( "#typ2" ).val()=="2"){
        $("#prod").attr('hidden',true);
        $("#catt").attr('hidden',false);
    }
}


        "use strict";

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

        if($( "#typ2" ).val()=="1"){
            $("#pro").val([{{(isset($coupon->items_ids)?$coupon->items_ids:"")}}]).trigger('change');
        }
        if($( "#typ2" ).val()=="2"){
            $("#cat").val([{{(isset($coupon->items_ids)?$coupon->items_ids:"")}}]).trigger('change');
        }
            
    </script>
@endsection
