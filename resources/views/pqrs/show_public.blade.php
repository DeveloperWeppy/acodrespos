@extends('layouts.app', ['title' => __('PQRS')])
@if (strlen(config('settings.recaptcha_site_key'))>2)
    @section('head')
    {!! htmlScriptTagJsApi([]) !!}
    @endsection
@endif

@section('content')
@include('users.partials.headerpqr', [
        'title' => "",
    ])

    <div class="container mt--8">
        <!-- Table -->
        <div class="row justify-content-center">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <div class="row">
                            <!-- Detalles de los datos de contácto --->
                            <div class="col-sm-6">
                                <h5>Fecha de Radicación de Solicitud:
                                    <strong>{{ date('d-m-Y', strtotime($pqr->created_at)) }} a las
                                        {{ date('h:i A', strtotime($pqr->created_at)) }}</strong>
                                </h5>
                            </div>
                            <div class="col-sm-6">
                                <h5>Estado Actual de la Solicitud:
                                    @if ($pqr->status=='radicado')
                                        <span class="text-capitalize text-white badge bg-gradient-warning">Radicado</span>
                                    @elseif($pqr->status=='en revision')
                                        <span class="text-capitalize text-white badge bg-gradient-info">En Revisión</span>
                                    @else
                                        <span class="text-capitalize text-white badge bg-gradient-success">Solicitud Solucionada</span>
                                    @endif
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

                            <div class="col-sm-4">
                                <p>Tipo de Solicitud:</p>
                                <span>{{ $pqr->type_radicate }}</span>
                            </div>

                            <div class="col-sm-4">
                                <p>Estado de la Solicitud:</p>
                                <span class="text-uppercase">{{ $pqr->status }}</span>
                            </div>

                            @if ($pqr->num_order != null)
                                <div class="col-sm-4">
                                    <p>Número de factura relacionada con la solicitud:</p>
                                    <span>#{{ $pqr->num_order }}</span>
                                </div>
                            @endif

                            @if ($pqr->order_id != null)
                                <div class="col-sm-4">
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
                            <!-- detalles de la respuesta de la solicitud --->
                            @if ($pqr->status == 'Solicitud Respondida')
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
                                        Respondida</span>
                                </div>
                            @endif

                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @section('js')
    @endsection
@endsection
