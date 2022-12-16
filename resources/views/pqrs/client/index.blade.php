@extends('layouts.app', ['title' => __('PQRS')])
@if (strlen(config('settings.recaptcha_site_key'))>2)
    @section('head')
    {!! htmlScriptTagJsApi([]) !!}
    @endsection
@endif

@section('content')
@include('users.partials.headerpqr', [
        'title' => "Centro de ayuda",
    ])

    <div class="container mt--8">
        <!-- Table -->
        <div class="row justify-content-center">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        @if (Auth::guest())
                            @include('pqrs.includes.pqrclient_nologin')
                        @else
                            @include('pqrs.includes.pqr_clien_login')
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @section('js')
    <script>


        
        


        window.onload = (event) => {
            var telephone1 = document.querySelector("#telephone1");
            var telephone2 = document.querySelector("#telephone2");
            var iti=window.intlTelInput(telephone1, {
                separateDialCode:true,
            });
            var iti=window.intlTelInput(telephone2, {
                separateDialCode:true,
            });
        };

       
        let Checked = null;
        //The class name can vary
        for (let CheckBox of document.getElementsByClassName('only-one')){
            CheckBox.onclick = function(){
            if(Checked!=null){
            Checked.checked = false;
            Checked = CheckBox;
             if (Checked.value=="otro") {
                $("#divnum_pedido").show();
             }else{
                $("#divnum_pedido").hide();
             }
            }
            Checked = CheckBox;
        }
        }

        //validar extensión de archivos a cargar
        function validarExtensionArchivo(){
            var fileInput = document.getElementById('input-file');
            var filePath = fileInput.value;
            var allowedExtensions = /(\.pdf|\.png|\.jpg|\.jepg)$/i;
            if(!allowedExtensions.exec(filePath)){
                Swal.fire({
                    title: 'Archivo no válido',
                    text: 'Solo se permite archivos con esta extensión .pdf/.png/.jpg/.jepg .',
                    icon: "error",
                    button: false,
                    timer: 4000
                });
                fileInput.value = '';
                return false;
            }else{
                //Otro Código
            }
        }

        //validar y enviar PQR
        $("#register_pqr").validate({
            rules: {
                name: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                },
                phone: {
                    required: true,
                    minlength: 7,
                    maxlength: 10
                },
                message: {
                    required: true,
                },
                accept: {
                    required: true,
                },
                n_order:{
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Por favor ingrese un nombre y apellido"
                },
                email: {
                    required: "Por favor ingrese el email",
                    email: "Ingrese una dirección de correo válida",
                },
                phone: {
                    required: "Por favor el numero de telefono",
                    minlength: "Número no válido",
                    maxlength: "Número no válido",
                },
                message: {
                    required: "Por favor ingrese un mensaje de su solicitud"
                },
                accept: {
                    required: "Acepta la Política de Tratamiento de Datos",
                },
                n_order: {
                    required: "Seleccione una acción"
                },
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                if($(element).attr('id')!="formPhone"){
                $(element).addClass('is-invalid');
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form,event){
                event.preventDefault();
                        var formDataClient=new FormData(form);
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                            type: "post",
                            encoding:"UTF-8",
                            url: "{{route('pqrs.store')}}",
                            data:formDataClient ,
                            processData: false,
                            contentType: false,
                            dataType:'json',
                            beforeSend:function(){
                            Swal.fire({
                                    title: 'Validando datos, espere por favor...',
                                    button: false,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    timer: 4000,
                                    timerProgressBar: true,
                                        didOpen: () => {
                                            Swal.showLoading()
                                        },
                                });
                            }
                        }).done(function( respuesta ) {
                            if (!respuesta.error) {
                                Swal.fire({
                                        title: "Hecho",
                                        text: "Solicitud Enviada Exitosamente",
                                        icon: "success",
                                        showConfirmButton: false,
                                        timer: 3000
                                });
                                location.reload();
                                
                            } else {
                                setTimeout(function () {
                                    Swal.fire({
                                        title: "Se presento un error!",
                                        html: respuesta.mensaje,
                                        icon: "error",
                                    });
                                }, 2000);
                            }
                        }).fail(function( resp) {
                            console.log(resp);
                            Swal.fire({
                                        title: 'Los datos proporcionados no son válidos',
                                        text:'mensajeError',
                                        icon: 'error',
                                        button: true,
                                        timer: 2000
                                    });
                        });
            }
        });

        //validar y enviar PQR
        $("#register_pqr2").validate({
            rules: {
                name: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                },
                phone: {
                    required: true,
                    number:true,
                    minlength: 7,
                    maxlength: 10,
                },
                message: {
                    required: true,
                },
                accept: {
                    required: true,
                },
                n_order:{
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Por favor ingrese un nombre y apellido"
                },
                email: {
                    required: "Por favor ingrese el email",
                    email: "Ingrese una dirección de correo válida",
                },
                phone: {
                    required: "Por favor ingrese el numero de telefono",
                    number: "Ingrese solo números",
                    minlength: "Número no válido",
                    maxlength: "Número no válido",
                },
                message: {
                    required: "Por favor ingrese un mensaje de su solicitud"
                },
                accept: {
                    required: "Acepta la Política de Tratamiento de Datos",
                },
                n_order: {
                    required: "Seleccione una acción"
                },
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                if($(element).attr('id')!="formPhone"){
                $(element).addClass('is-invalid');
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form,event){
                event.preventDefault();
                        var formDataClient=new FormData(form);
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                            type: "post",
                            encoding:"UTF-8",
                            url: "{{route('pqrs.store')}}",
                            data:formDataClient ,
                            processData: false,
                            contentType: false,
                            dataType:'json',
                            beforeSend:function(){
                            Swal.fire({
                                    title: 'Validando datos, espere por favor...',
                                    button: false,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    timer: 4000,
                                    timerProgressBar: true,
                                        didOpen: () => {
                                            Swal.showLoading()
                                        },
                                });
                            }
                        }).done(function( respuesta ) {
                            if (!respuesta.error) {
                                Swal.fire({
                                        title: "Hecho",
                                        text: "Solicitud Enviada Exitosamente",
                                        icon: "success",
                                        showConfirmButton: false,
                                        timer: 3000
                                });
                                location.href = respuesta.case_id;
                                
                            } else {
                                setTimeout(function () {
                                    Swal.fire({
                                        title: "Se presento un error!",
                                        html: respuesta.mensaje,
                                        icon: "error",
                                    });
                                }, 2000);
                            }
                        }).fail(function( resp) {
                            console.log(resp);
                            Swal.fire({
                                        title: 'Los datos proporcionados no son válidos',
                                        text:'mensajeError',
                                        icon: 'error',
                                        button: true,
                                        timer: 2000
                                    });
                        });
            }
        });
    </script>
    @endsection
@endsection
