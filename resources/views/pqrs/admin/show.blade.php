@extends('layouts.app', ['title' => __('PQRS')])

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>
    <div class="container-fluid mt--9">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h3 class="mb-0 text-capitalize">{{ __('Solicitud de ') }}
                                    <strong>{{ $pqr->name }}</strong>
                                </h3>
                            </div>
                            <div class="col-6 text-right">
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Detalles de los datos de contácto --->
                            <div class="col-sm-12">
                                <h5>Fecha de Radicación de Solicitud:
                                    <strong>{{ date('d-m-Y', strtotime($pqr->created_at)) }} a las
                                        {{ date('h:i A', strtotime($pqr->created_at)) }}</strong>
                                </h5>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <h6 class="heading-small text-uppercase text-primary mb-4">{{ __('Datos de Contácto') }}
                                </h6>
                            </div>

                            <div class="col-sm-4 col-12">
                                <p>Nombre Y Apellidos:</p>
                                <span class="text-capitalize">{{ $pqr->name }}</span>
                            </div>
                            <div class="col-sm-4 col-12">
                                <p>Correo Electrónico:</p>
                                <span>{{ $pqr->email }}</span>
                            </div>
                            <div class="col-sm-4 col-12">
                                <p>Celular o teléfono:</p>
                                <span>{{ $pqr->phone }}</span>
                            </div>

                            <!-- Detalles del radicado de la solicitud --->
                            <div class="col-sm-12 mt-4">
                                <h6 class="heading-small text-primary text-uppercase mb-4">
                                    {{ __('Información de la solicitud') }}</h6>
                            </div>

                            <div class="col-sm-6">
                                <p>Tipo de Solicitud:</p>
                                <span>{{ $pqr->type_radicate }}</span>
                            </div>

                            @if ($pqr->num_order != null)
                                <div class="col-sm-6">
                                    <p>Número de factura relacionada con la solicitud:</p>
                                    <span>#{{ $pqr->num_order }}</span>
                                </div>
                            @endif

                            @if ($pqr->order_id != null)
                                <div class="col-sm-6">
                                    <p>Número de factura relacionada con la solicitud:</p>
                                    <span>#{{ $pqr->order_id }}</span>
                                </div>
                            @endif

                            <div class="col-sm-12">
                                <p class="mt-3">Mensaje de la Solicitud:</p>
                                <span class="text-justify">{{ $pqr->message }}</span>
                            </div>

                            @if ($pqr->evidence != null)
                                <div class="col-sm-12 mt-3">
                                    <a class="btn btn-icon btn-3 btn-primary" href="{{ $pqr->evidence }}"
                                        download="{{ $pqr->evidence }}">
                                        <span class="btn-inner--icon"><i class="fas fa-download"></i></span>
                                        <span class="btn-inner--text">Descargar Evidencia de Solicitud</span>
                                    </a>
                                </div>
                            @endif

                            <!-- form de responder solicitud --->
                            @if ($pqr->status == 'en revision')
                                <div class="col-sm-12 mt-4">
                                    <h6 class="heading-small text-primary text-uppercase mb-4">
                                        {{ __('Responder Solicitud') }}</h6>
                                </div>
                                <div class="col-sm-12">
                                    <form enctype="multipart/form-data" autocomplete="off" id="register_answerpqr">
                                        <input type="hidden" name="id" value="{{ $pqr->id }}">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                    for="exampleFormControlTextarea1">Respuesta de la Solicitud</label>
                                                <textarea class="form-control form-control-alternative" name="message" id="exampleFormControlTextarea1" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                    for="input-file">{{ __('Subir evidencia') }}(opcional)</label>
                                                <p><small>Si la respuesta presenta evidencia, adjuntela acá en un formato
                                                        válido.</small>
                                                </p>
                                                <input type="file" name="evidence_answer" id="input-file"
                                                    class="form-control form-control-alternative"
                                                    onchange="return validarExtensionArchivo()">
                                            </div>
                                        </div>

                                        <div class="col-sm-12 ">
                                            <button type="submit"
                                                class="btn btn-success mt-4 mb-2 float-left">{{ __('Save') }}</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                            <!-- detalles de la respuesta de la solicitud --->
                            @if ($pqr->status == 'Solucionado')
                                <div class="col-sm-12 mt-4">
                                    <h6 class="heading-small text-primary text-uppercase mb-4">
                                        {{ __('Respuesta dada a la Solicitud') }}</h6>
                                </div>

                                <div class="col-sm-12">
                                    <p class="mt-3">Respuesta de la Solicitud:</p>
                                    <span class="text-justify">{{ $pqr->message }}</span>
                                </div>
                                @if ($pqr->evidence_answer != null)
                                    <div class="col-sm-12 mt-3">
                                        <a class="btn btn-icon btn-3 btn-primary mb-3" href="{{ $pqr->evidence_answer }}"
                                            download="{{ $pqr->evidence_answer }}">
                                            <span class="btn-inner--icon"><i class="fas fa-download"></i></span>
                                            <span class="btn-inner--text">Descargar Evidencia de Respuesta</span>
                                        </a>
                                    </div>
                                @endif
                                <div class="col-sm-12 text-center mb-3">
                                    <span class="text-capitalize  text-white badge bg-gradient-success">Solicitud
                                        Solucionada</span>
                                </div>
                            @endif

                        </div>
                        <div class="card-footer py-4">
                            <div class="">
                                @if ($pqr->status == 'radicado')
                                    <button type="button" class="btn btn-info float-right btn_status"
                                        id="{{ $pqr->id }}"><i class="fas fa-hourglass-start"></i> Solicitud
                                        Recibida</button>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        jQuery(document).on("click", ".btn_status", function() {
            var $element = jQuery(this);
            var id = $element.attr('id');
            console.log(id);
            //ruta
            var url = "/pqrs/updateestadopqr";
            var data = {
                idpqr: id
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                encoding: "UTF-8",
                url: url,
                data: data,
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        text: 'Validando datos, espere porfavor...',
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                    });
                }
            }).done(function(respuesta) {
                //console.log(respuesta);
                if (!respuesta.error) {
                    Swal.fire({
                        title: 'Solicitud en Proceso!',
                        icon: 'success',
                        showConfirmButton: true,
                        timer: 2000
                    });

                    setTimeout(function() {
                        location.reload();
                    }, 2000);

                } else {
                    setTimeout(function() {
                        Swal.fire({
                            title: respuesta.mensaje,
                            icon: 'error',
                            showConfirmButton: true,
                            timer: 2000
                        });
                    }, 2000);
                }
            }).fail(function(resp) {
                console.log(resp);
            });
        });

        //validar extensión de archivos a cargar
        function validarExtensionArchivo() {
            var fileInput = document.getElementById('input-file');
            var filePath = fileInput.value;
            var allowedExtensions = /(\.pdf|\.png|\.jpg|\.jepg)$/i;
            if (!allowedExtensions.exec(filePath)) {
                Swal.fire({
                    title: 'Archivo no válido',
                    text: 'Solo se permite archivos con esta extensión .pdf/.png/.jpg/.jepg .',
                    icon: "error",
                    button: false,
                    timer: 4000
                });
                fileInput.value = '';
                return false;
            } else {
                //Otro Código
            }
        }

        //validar y responder PQR
        $("#register_answerpqr").validate({
            rules: {
                message: {
                    required: true,
                },
            },
            messages: {
                message: {
                    required: "Por favor ingrese una respuesta a la solicitud"
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
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                var formDataClient = new FormData(form);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: "post",
                    encoding: "UTF-8",
                    url: "{{ route('pqrs.storeRespuesta') }}",
                    data: formDataClient,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function() {
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
                }).done(function(respuesta) {
                    if (!respuesta.error) {
                        Swal.fire({
                            title: "Hecho",
                            text: "Solicitud Respondida Exitosamente",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 3000
                        });
                        location.reload();

                    } else {
                        setTimeout(function() {
                            Swal.fire({
                                title: "Se presento un error!",
                                html: respuesta.mensaje,
                                icon: "error",
                            });
                        }, 2000);
                    }
                }).fail(function(resp) {
                    console.log(resp);
                    Swal.fire({
                        title: 'Los datos proporcionados no son válidos',
                        text: 'mensajeError',
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
