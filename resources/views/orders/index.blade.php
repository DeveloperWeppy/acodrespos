@extends('layouts.app', ['title' => __('Orders')])
@section('admin_title')
    {{__('Orders')}}
@endsection
@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>


    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <!-- Order Card -->
                @include('orders.partials.ordercard')
            </div>
        </div>
        @include('layouts.footers.auth')
        @include('orders.partials.modals')
    </div>
@section('js')
<script>
    $(function() { 
        $('#tel').removeAttr("step");
        $('#nom').removeAttr("step");
     });
    $("#form-assing-driver").validate({
        rules: {
            nom: {
                required: true,
            },
            tel: {
                    required: true,
                    number:true,
                    minlength: 7,
                    maxlength: 10,
            },
        },
        messages: {
            nom: {
                required: "Por favor ingrese un nombre"
            },
            tel: {
                required: "Por favor ingrese el numero de telefono",
                number: "Ingrese solo números",
                minlength: "Número no válido",
                maxlength: "Número no válido",
            },
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            if ($(element).attr('id') != "formPhone") {
                $(element).addClass('is-invalid');
            }
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
</script>

@endsection
@endsection


